<?php


namespace App\Modules\Common\Bus;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBus
{
    protected MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function dispatch($command, array $stamps = [])
    {
        return $this->bus->dispatch($command, $stamps)->last(HandledStamp::class)->getResult();
    }
}