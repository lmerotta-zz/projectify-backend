<?php

namespace App\Modules\FileManagement\Messenger\Commands;

use App\Entity\Files\File;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\MountManager;
use Ramsey\Uuid\Uuid;

class SaveFileHandler
{
    private MountManager $mountManager;
    private EntityManagerInterface $em;

    public function __construct(MountManager $manager, EntityManagerInterface $em)
    {
        $this->mountManager = $manager;
        $this->em = $em;
    }
    public function __invoke(SaveFile $command): File
    {
        $file = $command->getFile();
        $filename = uniqid().'_'.$file->getClientOriginalName();
        $fs = $this->mountManager->getFilesystem($command->getContext()->getValue().'.storage');

        $fs->writeStream($filename, fopen($file->getRealPath(), 'r'));

        $entity = File::create(Uuid::uuid4(), $command->getContext(), $filename);
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }
}