<?php

namespace App\Modules\FileManagement\GraphQL\Mutations;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use App\Contracts\FileManagement\Enum\FileContext;
use App\Modules\Common\Bus\CommandBus;
use App\Modules\FileManagement\Messenger\Commands\SaveFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Stamp\ValidationStamp;

class SaveFileResolver implements MutationResolverInterface
{
    private CommandBus $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke($item, array $context)
    {
        try {
            return $this->bus->dispatch(new SaveFile(FileContext::get($context['args']['input']['context']), new UploadedFile($context['args']['input']['file'], uniqid())), [new ValidationStamp(['Default', $context['args']['input']['context']])]);
        } catch (ValidationFailedException $e) {
            throw new ValidationException($e->getViolations());
        }
    }
}