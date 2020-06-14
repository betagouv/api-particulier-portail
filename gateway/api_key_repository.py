from gateway.models import ApiKey


def get_active_by_hashed_key(hashed_key):
    return ApiKey.query.filter_by(hashed_key=hashed_key, active=True).first()
