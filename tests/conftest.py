import os
import tempfile

import pytest

from app import create_app
from app.factories import ApiFactory
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
def test_api(client):
    api = ApiFactory(
        name="Test API", backend="https://pokeapi.co/api/v2", path="test-api")
    yield api
