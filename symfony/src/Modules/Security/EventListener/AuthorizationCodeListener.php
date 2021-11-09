<?php

namespace App\Modules\Security\EventListener;

use Trikoder\Bundle\OAuth2Bundle\Event\AuthorizationRequestResolveEvent;

class AuthorizationCodeListener
{
    public function onAuthorizationRequestResolve(AuthorizationRequestResolveEvent $event): void
    {
        if ($event->getUser()) {
            $event->resolveAuthorization(AuthorizationRequestResolveEvent::AUTHORIZATION_APPROVED);
        }
    }
}
