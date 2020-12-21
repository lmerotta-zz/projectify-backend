<?php

namespace App\Tests\Modules\UserManagement\FileStorage;

use App\Contracts\FileManagement\Enum\FileContext;
use App\Entity\Files\File;
use App\Modules\UserManagement\FileStorage\UserProfilePictureRetriever;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserProfilePictureRetrieverTest extends TestCase
{
    public function testItGeneratesTheUrlCorrectly()
    {
        $file = $this->prophesize(File::class);
        $router = $this->prophesize(UrlGeneratorInterface::class);

        $file->getContext()->shouldBeCalled()->willReturn(FileContext::get(FileContext::USER_PROFILE_PICTURE));
        $file->getPath()->shouldBeCalled()->willReturn('123abc.png');
        $file->getId()->shouldBeCalled()->willReturn(Uuid::uuid4());
        $router->generate('app.file_management.action.get_file_resource', Argument::type('array'))->willReturn('/a/bc');

        $retriever = new UserProfilePictureRetriever();
        $retriever->setRouter($router->reveal());

        $this->assertEquals('/a/bc', $retriever->generateURL($file->reveal()));
    }
}
