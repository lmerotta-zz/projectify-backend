<?php

namespace App\Modules\Common\Bus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBus
{
    public function __construct(protected MessageBusInterface $bus)
    {
    }

    public function dispatch($command, array $stamps = []): mixed
    {
        try {
            return $this->bus->dispatch($command, $stamps)->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $e) {
            while ($e instanceof HandlerFailedException) {
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
