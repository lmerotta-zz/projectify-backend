<?php

namespace App\Modules\Common\Bus;

use Symfony\Component\Messenger\Exception\DelayedMessageHandlingException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @codeCoverageIgnore
 */
class CommandBus
{
    public function __construct(protected MessageBusInterface $bus)
    {
    }

    public function dispatch($command, array $stamps = []): mixed
    {
        try {
            return $this->bus->dispatch($command, $stamps)->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException|DelayedMessageHandlingException $e) {
            while ($e instanceof HandlerFailedException || $e instanceof DelayedMessageHandlingException) {
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
