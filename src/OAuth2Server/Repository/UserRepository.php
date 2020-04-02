<?php

namespace App\OAuth2Server\Repository;

use App\OAuth2Server\Entity\User;
use App\Repository\UserRepository as AppUserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var AppUserRepository
     */
    private $userRepository;

    public function __construct(AppUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $appUser = $this->userRepository->findOneBy(['email' => $username]);
        if ($appUser === null) {
            return null;
        }

        $oAuthUser = new User($appUser->getId()->toString());
        return $oAuthUser;
    }
}
