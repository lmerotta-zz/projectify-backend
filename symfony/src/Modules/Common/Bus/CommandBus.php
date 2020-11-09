<?php


namespace App\Modules\Common\Bus;

use Symfony\Component\Messenger\MessageBusInterface;

class CommandBus
{
    protected MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function dispatch($command, array $stamps = []): void
    {
        $this->bus->dispatch($command, $stamps);
    }
}