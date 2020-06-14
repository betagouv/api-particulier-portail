from app.db import Api


def test_fixtures_loading():
    assert len(Api.query.all()) == 1


def test_api_key_generation(test_api_key):
    assert test_api_key
