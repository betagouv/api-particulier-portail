<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Trikoder\Bundle\OAuth2Bundle\Event\AuthorizationRequestResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Events;

class OAuthAuthorizationSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            OAuth2Events::AUTHORIZATION_REQUEST_RESOLVE => "onAuthorizationRequestResolve"
        ];
    }

    public function onAuthorizationRequestResolve(AuthorizationRequestResolveEvent $event)
    {
        $user = $this->security->getUser();
        if ($user && in_array("ROLE_USER", $user->getRoles())) {
            $event->resolveAuthorization(AuthorizationRequestResolveEvent::AUTHORIZATION_APPROVED);
        }
    }
}
