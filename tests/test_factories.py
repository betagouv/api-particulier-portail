from app.db import Api


def test_fixtures_loading():
    assert len(Api.query.all()) == 1
