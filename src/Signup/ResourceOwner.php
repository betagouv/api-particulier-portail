<?php

namespace App\Signup;

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
    private $givenName;

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

    public function __construct(string $sub, string $name, string $givenName, string $email, array $roles, array $organizations)
    {
        $this->sub = $sub;
        $this->name = $name;
        $this->givenName = $givenName;
        $this->email = $email;
        $this->roles = $roles;
        $this->organizations = $organizations;
    }

    public function getId(): string
    {
        return $this->sub;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "name" => $this->name,
            "given_name" => $this->givenName,
            "email" => $this->email,
            "roles" => $this->roles,
            "organizations" => $this->organizations
        ];
    }
}
