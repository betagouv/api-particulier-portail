from app.routes import build_routes


def test_empty_db(client):
    response = client.get('/')
    assert response.status_code == 404


def test_test_api(app, test_api, client):
    build_routes()
    print(app.url_map)
    response = client.get("/{}/pokemon/ditto".format(test_api.path))
    assert response.status_code == 200
