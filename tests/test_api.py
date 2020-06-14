from app.routes import build_routes


def test_empty_db(client):
    response = client.get("/")
    assert response.status_code == 404


def test_test_api(app, test_api, client):
    build_routes()
    print(app.url_map)
    response = client.get(
        "/{}/pokemon/ditto".format(test_api.path),
        headers={
            "X-Api-Key": "f25ba6ce8729ae6c3dd2493337a999de8d5c94b05dd5d775b306f18477621b29d9df626355b1bd011baf27749beb20a1"
        },
    )
    assert response.status_code == 200
