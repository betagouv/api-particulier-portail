import os
import tempfile
import random
import string
import pytest
from datetime import datetime, timedelta
from app import create_app
from tests.factories import ApiFactory, ApiKeyFactory
from app.settings import TestConfig


@pytest.fixture(autouse=True)
def app():
    app = create_app(TestConfig)
    return app


@pytest.fixture
def client(app):
    with app.test_client() as client:
        yield client


@pytest.fixture(autouse=True)
def test_api(app):
    with app.app_context():
        api = ApiFactory(
            name="Test API", backend="https://pokeapi.co/api/v2", path="test-api"
        )
        yield api


@pytest.fixture(scope="session")
def test_api_key_value():
    stringLength = 30
    lettersAndDigits = string.ascii_letters + string.digits
    return "".join((random.choice(lettersAndDigits) for i in range(30)))


@pytest.fixture(scope="session")
def test_inactive_api_key_value():
    stringLength = 30
    lettersAndDigits = string.ascii_letters + string.digits
    return "".join((random.choice(lettersAndDigits) for i in range(30)))


@pytest.fixture(scope="session")
def test_expired_api_key_value():
    stringLength = 30
    lettersAndDigits = string.ascii_letters + string.digits
    return "".join((random.choice(lettersAndDigits) for i in range(30)))


@pytest.fixture(autouse=True)
def test_api_key(app, test_api_key_value):
    with app.app_context():
        api_key = ApiKeyFactory(key=test_api_key_value)
        yield api_key


@pytest.fixture(autouse=True)
def test_inactive_api_key(app, test_inactive_api_key_value):
    with app.app_context():
        inactive_api_key = ApiKeyFactory(key=test_inactive_api_key_value, active=False)
        yield inactive_api_key


@pytest.fixture(autouse=True)
def test_expired_api_key(app, test_expired_api_key_value):
    with app.app_context():
        inactive_api_key = ApiKeyFactory(
            key=test_expired_api_key_value,
            active=True,
            expires_at=datetime.now() - timedelta(seconds=10),
        )
        yield inactive_api_key
