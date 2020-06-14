from app.routes import build_routes


def test_empty_db(client):
    response = client.get("/")
    assert response.status_code == 404


def test_test_api(app, test_api, client, test_api_key_value):
    build_routes()
    response = client.get(
        "/{}/pokemon/ditto".format(test_api.path),
        headers={"X-Api-Key": test_api_key_value},
    )
    assert response.status_code == 200
