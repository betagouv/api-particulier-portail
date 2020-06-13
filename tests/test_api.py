def test_empty_db(client):
    response = client.get('/')
    assert response.status_code == 404
