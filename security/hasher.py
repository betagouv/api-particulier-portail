import hashlib


def hash_api_key(api_key):
    hasher = hashlib.sha512(str(api_key).encode("utf-8"))
    return hasher.hexdigest()
