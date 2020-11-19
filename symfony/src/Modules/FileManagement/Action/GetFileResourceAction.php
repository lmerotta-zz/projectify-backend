<?php

namespace App\Modules\FileManagement\Action;

use App\Modules\Common\Bus\QueryBus;
use App\Contracts\FileManagement\Exception\FileNotFoundException;
use App\Modules\FileManagement\Messenger\Queries\GetFileResource;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetFileResourceAction extends AbstractController
{
    private QueryBus $bus;

    public function __construct(QueryBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @Route("/assets/{id<(.+)>}", name="app.file_management.action.get_file_resource")
     */
    public function action(string $id, Request $request)
    {
        try {
            return $this->bus->query(new GetFileResource(Uuid::fromString($id), $request->query->all()));
        } catch (FileNotFoundException | InvalidUuidStringException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
    }
}