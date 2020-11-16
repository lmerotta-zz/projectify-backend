<?php

namespace App\Modules\FileManagement\Controller;

use App\Modules\Common\Bus\CommandBus;
use App\Modules\Common\Bus\QueryBus;
use App\Contracts\FileManagement\Enum\FileContext;
use App\Contracts\FileManagement\Exception\FileNotFoundException;
use App\Modules\FileManagement\HTTP\V1\Request\SaveFileRequest;
use App\Modules\FileManagement\HTTP\V1\Response\SaveFileResponse;
use App\Modules\FileManagement\Messenger\Commands\SaveFile;
use App\Modules\FileManagement\Messenger\Queries\GetFileResource;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Rfc4122\UuidV4;
use App\Entity\Files\File;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetFileResourceAction
{
    private QueryBus $bus;
    private RequestStack $requestStack;

    public function __construct(QueryBus $bus, RequestStack $requestStack)
    {
        $this->bus = $bus;
        $this->requestStack = $requestStack;
    }

    public function __invoke(File $data)
    {
        try {
            return $this->bus->query(new GetFileResource($file->getId(), $this->requestStack->getCurrentRequest()->query->all()));
        } catch (FileNotFoundException | InvalidUuidStringException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
    }
}