<?php

namespace App\Tests\Modules\FileManagement\FileStorage;

use App\Contracts\FileManagement\Enum\FileContext;
use App\Contracts\FileManagement\FileRetrieverInterface;
use App\Modules\FileManagement\Exception\DuplicateFileRetrieverException;
use App\Modules\FileManagement\Exception\NonExistingFileRetrieverException;
use App\Modules\FileManagement\FileStorage\FileRetriever;
use PHPUnit\Framework\TestCase;

class FileRetrieverTest extends TestCase
{
    public function testAddRetrieverShouldNotAcceptDuplicates()
    {
        $interface = $this->prophesize(FileRetrieverInterface::class);
        $interface->getSupportedContext()->shouldBeCalled()->willReturn(FileContext::get(FileContext::USER_PROFILE_PICTURE));

        $this->expectException(DuplicateFileRetrieverException::class);

        $r = new FileRetriever();
        $r->addRetriever($interface->reveal());
        $r->addRetriever($interface->reveal());
    }

    public function testGetRetrieverShouldFailIfRetrieverNotFound()
    {
        $retriever = new FileRetriever();

        $this->expectException(NonExistingFileRetrieverException::class);

        $retriever->getRetriever(FileContext::get(FileContext::USER_PROFILE_PICTURE));
    }

    public function testItReturnsTheRetriever()
    {
        $interface = $this->prophesize(FileRetrieverInterface::class);
        $interface->getSupportedContext()->shouldBeCalled()->willReturn(FileContext::get(FileContext::USER_PROFILE_PICTURE));

        $r = new FileRetriever();
        $r->addRetriever($interface->reveal());

        $actual = $r->getRetriever(FileContext::get(FileContext::USER_PROFILE_PICTURE));
        $this->assertEquals($interface->reveal(), $actual);
    }
}
