<?php

namespace App\Repository\Analytics;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserRepository
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    public function __construct()
    {
        $this->client = HttpClient::createForBaseUri("http://localhost:3000/", [
            "auth_basic" => ["admin", "juwaub5NOFF"]
        ]);
    }

    public function findAll()
    {
        $response = $this->client->request(
            "GET",
            "/api/users"
        );

        $users = json_decode($response->getContent(), true);

        return $users;
    }

    public function findByEmail(string $email)
    {
        $response = $this->client->request(
            "GET",
            "/api/users/search",
            [
                "query" => [
                    "query" => $email
                ]
            ]
        );

        $userSearch = json_decode($response->getContent(), true);

        return $userSearch;
    }

    public function createUser(string $name, string $email)
    {
        $response = $this->client->request(
            "POST",
            "/api/admin/users",
            [
                "body" => [
                    "name" => $name,
                    "email" => $email,
                    "login" => $email,
                    "password" => md5(rand())
                ]
            ]
        );

        $user = json_decode($response->getContent(), true);

        return $user;
    }

    public function createIfNotExists(string $name, string $email)
    {
        $existingUsers = $this->findByEmail($email);
        if ($existingUsers["totalCount"] > 0) {
            return;
        }

        $this->createUser($name, $email);
    }
}
