<?php

namespace App\Tests\Modules\FileManagement\Stage;

use ApiPlatform\Core\GraphQl\Resolver\Stage\SerializeStageInterface;
use App\Contracts\FileManagement\Enum\FileContext;
use App\Contracts\FileManagement\FileRetrieverInterface;
use App\Entity\Files\File;
use App\Modules\FileManagement\FileStorage\FileRetriever;
use App\Modules\FileManagement\Stage\GenerateFileURLSerializeStage;
use PHPUnit\Framework\TestCase;

class GenerateFileURLSerializeStageTest extends TestCase
{
    public function testItSetsFileUrlOnSingleItem()
    {
        $retriever = $this->prophesize(FileRetriever::class);
        $retrieverInterface = $this->prophesize(FileRetrieverInterface::class);
        $stage = $this->prophesize(SerializeStageInterface::class);
        $file = $this->prophesize(File::class);

        $file->getContext()->shouldBeCalled()->willReturn(FileContext::get(FileContext::USER_PROFILE_PICTURE));
        $file->setUrl('/a/bc')->shouldBeCalled()->willReturn($file->reveal());
        $retriever->getRetriever(FileContext::get(FileContext::USER_PROFILE_PICTURE))->shouldBeCalled()->willReturn($retrieverInterface->reveal());
        $retrieverInterface->generateURL($file->reveal())->shouldBeCalled()->willReturn('/a/bc');

        $stage->__invoke($file->reveal(), File::class, 'test', [])->shouldBeCalled()->willReturn([]);

        $handler = new GenerateFileURLSerializeStage();
        $handler->setFileRetriever($retriever->reveal());
        $handler->setStage($stage->reveal());

        $handler($file->reveal(), File::class, 'test', []);
    }
}
