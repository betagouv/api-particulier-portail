<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\AuthorizationRequestResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Events;

class OAuthAuthorizationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            OAuth2Events::AUTHORIZATION_REQUEST_RESOLVE => "onAuthorizationRequestResolve"
        ];
    }

    public function onAuthorizationRequestResolve(AuthorizationRequestResolveEvent $event)
    {
        $event->resolveAuthorization(AuthorizationRequestResolveEvent::AUTHORIZATION_APPROVED);
    }
}
