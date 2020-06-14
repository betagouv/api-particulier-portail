from app.db import Api
from security.hasher import hash_api_key
from gateway.models import ApiKey


def test_fixtures_loading():
    assert len(Api.query.all()) == 1
    assert len(ApiKey.query.all()) == 2


def test_api_key_generation(test_api_key):
    assert test_api_key


def test_api_key_hash(test_api_key, test_api_key_value):
    assert hash_api_key(test_api_key_value) == test_api_key.hashed_key
