<?php

namespace App\Modules\Common\Bus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @codeCoverageIgnore
 */
class QueryBus
{
    public function __construct(protected MessageBusInterface $queryBus)
    {
    }

    /**
     * @return mixed
     */
    public function query($query, array $stamps = [])
    {
        try {
            return $this->queryBus->dispatch($query, $stamps)->last(HandledStamp::class)->getResult();
        } catch (HandlerFailedException $e) {
            while ($e instanceof HandlerFailedException) {
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
