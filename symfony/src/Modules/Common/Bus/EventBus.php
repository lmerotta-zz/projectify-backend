<?php

namespace App\Modules\Common\Bus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBus
{
    public function __construct(protected MessageBusInterface $eventBus) {}

    public function dispatch($event, array $stamps = []): void
    {
        try {
            $this->eventBus->dispatch($event, $stamps);
        } catch (HandlerFailedException $e) {
            while ($e instanceof HandlerFailedException) {
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
