from gateway.api_key_repository import get_active_by_hashed_key


def test_missing_key():
    assert get_active_by_hashed_key("yolo") is None


def test_active_key(test_api_key):
    assert (
        get_active_by_hashed_key(test_api_key.hashed_key).hashed_key
        == test_api_key.hashed_key
    )


def test_inactive_key(test_inactive_api_key):
    assert get_active_by_hashed_key(test_inactive_api_key.hashed_key) is None


def test_expired_key(test_expired_api_key):
    assert get_active_by_hashed_key(test_expired_api_key.hashed_key) is None
