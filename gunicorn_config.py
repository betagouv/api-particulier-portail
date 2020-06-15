import os
import sys


def pre_exec(server):
    """
    Resets the working directory of the server to GUNICORN_APP_ROOT.

    We run Gunicorn from a symlinked directory, which Gunicorn ends up
    dereferencing (via os.getcwd()) on startup and saving in START_CTX['cwd'].
    This means that simply updating the symlink and forking a new master won't
    work: the new master will run from the dereferenced directory, which is the
    same as the old master's working directory.

    This hook, which runs in the new master after fork() but before starts
    handling connections, lets us correct this. We reset START_CTX['cwd'] back
    to the symlink directory, which the deploy script passes in as an
    environment variable and os.chdir(). This means that the new master is
    exec'd in the correct directory (which it later dereferences, but by then
    we don't care) and we don't have to touch the old master.

    Moreover, the workers are forked with a working directory that has been
    dereferenced from the symlink, so we can actually *remove* or re-point the
    symlink without affecting workers, present or future.
    """
    app_root = os.environ.get("GUNICORN_APP_ROOT")
    print("[pre_exec] Starting hook, app_root = %r" % app_root)
    if app_root:
        orig_cwd = server.START_CTX["cwd"]
        server.START_CTX["cwd"] = app_root
        os.chdir(app_root)
        print("[pre_exec] Switching cwd: %s -> %s" % (orig_cwd, app_root))

        orig_path = os.path.dirname(sys.executable)
        new_path = os.path.join(app_root, ".venv", "bin")

        server.START_CTX[0] = server.START_CTX[0].replace(orig_path, new_path)
        server.START_CTX["args"] = [
            arg.replace(orig_path, new_path) for arg in server.START_CTX["args"]
        ]

    print("[pre_exec] Done running hook, START_CTX = %r" % server.START_CTX)
