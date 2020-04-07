<?php

namespace App\AuthApi;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class ResourceOwner implements ResourceOwnerInterface
{
    /**
     * @var string
     */
    private $sub;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var array
     */
    private $organizations;

    public function __construct(string $sub, string $name, string $surname, string $email, array $roles, array $organizations)
    {
        $this->sub = $sub;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->roles = $roles;
        $this->organizations = $organizations;
    }

    public function getId(): string
    {
        return $this->sub;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "name" => $this->name,
            "surname" => $this->surname,
            "email" => $this->email,
            "roles" => $this->roles,
            "organizations" => $this->organizations
        ];
    }
}
