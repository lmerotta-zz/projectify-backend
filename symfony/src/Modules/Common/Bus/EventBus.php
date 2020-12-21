<?php

namespace App\Modules\Common\Bus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBus
{
    protected MessageBusInterface $bus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->bus = $eventBus;
    }

    public function dispatch($event, array $stamps = []): void
    {
        try {
            $this->bus->dispatch($event, $stamps);
        } catch (HandlerFailedException $e) {
            while ($e instanceof HandlerFailedException) {
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
