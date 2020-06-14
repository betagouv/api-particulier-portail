from functools import wraps
from flask import request, abort
from security.hasher import hash_api_key


def require_api_key(view_function):
    @wraps(view_function)
    def decorated_view_function(*args, **kwargs):
        if request.headers.has_key("x-api-key"):
            hashed_api_key = hash_api_key(request.headers.get("x-api-key"))
            if (
                hashed_api_key
                == "5f6c070b2f2126b39cb8c72cd05e99588c189a7ef689d7f7c5a0d1ed337068e525485469aa007f7202335774150474875a9ac28d5b6f433d103dcd4e25fc48e4"
            ):
                return view_function(*args, **kwargs)
        else:
            abort(401)

    return decorated_view_function
