<?php

namespace App\Tests\Modules\FileManagement\Messenger\Commands;

use App\Contracts\FileManagement\Enum\FileContext;
use App\Entity\Files\File;
use App\Modules\FileManagement\Messenger\Commands\SaveFile;
use App\Modules\FileManagement\Messenger\Commands\SaveFileHandler;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SaveFileHandlerTest extends TestCase
{
    public function testItSavesFile()
    {
        $context = FileContext::get(FileContext::USER_PROFILE_PICTURE);
        $mountManager = $this->prophesize(MountManager::class);
        $em = $this->prophesize(EntityManagerInterface::class);
        $file = $this->prophesize(UploadedFile::class)->willBeConstructedWith(['pek', 'pok', null, \UPLOAD_ERR_PARTIAL, true]);
        $fs = $this->prophesize(FilesystemInterface::class);

        $file->getClientOriginalName()->shouldBeCalledOnce()->willReturn('test.jpg');
        $file->getRealPath()->shouldBeCalledOnce()->willReturn('php://memory');
        $mountManager->getFilesystem($context->getValue().'.storage')->shouldBeCalledOnce()->willReturn($fs->reveal());
        $fs->writeStream(Argument::containingString('test.jpg'), Argument::any())->shouldBeCalledOnce();

        $em->persist(Argument::type(File::class))->shouldBeCalledOnce();
        $em->flush()->shouldBeCalledOnce();

        (new SaveFileHandler($mountManager->reveal(), $em->reveal()))(new SaveFile(FileContext::USER_PROFILE_PICTURE, $file->reveal()));
    }
}