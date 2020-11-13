<?php

namespace App\Tests\Modules\FileManagement\Messenger\Queries;

use App\Contracts\FileManagement\Enum\FileContext;
use App\Contracts\FileManagement\Exception\FileNotFoundException;
use App\Contracts\FileManagement\FileRetrieverInterface;
use App\Entity\Files\File;
use App\Modules\FileManagement\Exception\NonExistingFileRetrieverException;
use App\Modules\FileManagement\FileStorage\FileRetriever;
use App\Modules\FileManagement\Messenger\Queries\GetFileResource;
use App\Modules\FileManagement\Messenger\Queries\GetFileResourceHandler;
use App\Repository\Files\FileRepository;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GetFileResourceHandlerTest extends TestCase
{
    public function testThrowsFileNotFoundIfEntityIsNotFound()
    {
        $id = '7bff7d1f-5db8-492e-b478-172c110e150f';

        $repository = $this->prophesize(FileRepository::class);
        $retriever = $this->prophesize(FileRetriever::class);
        $manager = $this->prophesize(MountManager::class);

        $this->expectException(FileNotFoundException::class);

        $repository->find($id)->shouldBeCalledOnce()->willReturn(null);

        (new GetFileResourceHandler($repository->reveal(), $retriever->reveal(), $manager->reveal()))(new GetFileResource(Uuid::fromString($id)));
    }

    public function testThrowsFileNotFoundIfRetrieverFails()
    {
        $id = '7bff7d1f-5db8-492e-b478-172c110e150f';
        $context = FileContext::get(FileContext::USER_PROFILE_PICTURE);

        $repository = $this->prophesize(FileRepository::class);
        $retriever = $this->prophesize(FileRetriever::class);
        $manager = $this->prophesize(MountManager::class);
        $source = $this->prophesize(FilesystemInterface::class);
        $cache = $this->prophesize(FilesystemInterface::class);
        $file = File::create(Uuid::fromString($id), $context, 'test.jpg');

        $this->expectException(FileNotFoundException::class);

        $repository->find($id)->shouldBeCalledOnce()->willReturn($file);
        $manager->getFilesystem($context->getValue().'.storage')->willReturn($source->reveal());
        $manager->getFilesystem($context->getValue().'.cache')->willReturn($cache->reveal());

        $retriever->getRetriever($context)->shouldBeCalledOnce()->willThrow(new NonExistingFileRetrieverException());

        (new GetFileResourceHandler($repository->reveal(), $retriever->reveal(), $manager->reveal()))(new GetFileResource(Uuid::fromString($id)));
    }
}
