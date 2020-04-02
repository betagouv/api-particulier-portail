<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\ScopeResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Events;

class ScopeResolveSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            OAuth2Events::SCOPE_RESOLVE => "onScopeResolve"
        ];
    }

    public function onScopeResolve(ScopeResolveEvent $event)
    {
    }
}
