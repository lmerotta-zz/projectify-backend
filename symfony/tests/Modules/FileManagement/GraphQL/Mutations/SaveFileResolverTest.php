<?php

namespace App\Tests\Modules\FileManagement\GraphQL\Mutations;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Contracts\FileManagement\Enum\FileContext;
use App\Modules\Common\Bus\CommandBus;
use App\Modules\FileManagement\GraphQL\Mutations\SaveFileResolver;
use App\Modules\FileManagement\Messenger\Commands\SaveFile;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Stamp\ValidationStamp;
use Symfony\Component\Validator\ConstraintViolationList;

class SaveFileResolverTest extends TestCase
{
    public function testItThrowsAValidationExceptionIfValidationFails()
    {
        $this->expectException(ValidationException::class);

        $commandBus = $this->prophesize(CommandBus::class);
        $commandBus->dispatch(Argument::type(SaveFile::class), [new ValidationStamp(['Default', FileContext::USER_PROFILE_PICTURE])])->shouldBeCalledOnce()->willThrow(new ValidationFailedException(new \stdClass(), new ConstraintViolationList()));

        (new SaveFileResolver($commandBus->reveal()))(null, ['args' => ['input' => ['context' => FileContext::USER_PROFILE_PICTURE, 'file' => __DIR__.'/../../fixtures/40.jpg']]]);
    }
}
