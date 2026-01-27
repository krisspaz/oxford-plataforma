<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

/**
 * Adds custom claims to the JWT payload
 * This ensures the 'id' is always available in the token for frontend use
 */
class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();

        // Add the user ID to the payload
        if ($user instanceof User) {
            $payload['id'] = $user->getId();
            
            // Optionally add other useful claims
            $payload['name'] = $user->getName();
        }

        $event->setData($payload);
    }
}
