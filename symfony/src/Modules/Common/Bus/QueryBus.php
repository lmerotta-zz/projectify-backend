<?php


namespace App\Modules\Common\Bus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QueryBus
{
    protected MessageBusInterface $bus;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->bus = $queryBus;
    }

    public function query($query, array $stamps = [])
    {
        try {
            return $this->bus->dispatch($query, $stamps)->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $e) {
            while ($e instanceof HandlerFailedException) {
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}