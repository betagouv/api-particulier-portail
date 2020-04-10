<?php

namespace App\EventSubscriber;

use App\Event\UserCreatedEvent;
use App\Repository\Analytics\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserRepository
     */
    private $analyticsUserRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->analyticsUserRepository = $userRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserCreatedEvent::class => 'onUserCreated'
        ];
    }

    public function onUserCreated(UserCreatedEvent $event)
    {
        $user = $event->getUser();

        $this->analyticsUserRepository->createIfNotExists(
            $user->getFullName(),
            $user->getEmail()
        );
    }
}
