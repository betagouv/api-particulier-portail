from functools import wraps
from flask import request, abort
from security.hasher import hash_api_key
from gateway.api_key_repository import get_active_by_hashed_key


def require_api_key(view_function):
    @wraps(view_function)
    def decorated_view_function(*args, **kwargs):
        if request.headers.has_key("x-api-key"):
            hashed_api_key = hash_api_key(request.headers.get("x-api-key"))
            stored_api_key = get_active_by_hashed_key(hashed_api_key)
            if stored_api_key:
                return view_function(*args, **kwargs)
        abort(401)

    return decorated_view_function
