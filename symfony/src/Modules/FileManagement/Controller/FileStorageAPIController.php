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
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Rest\Route("/assets")
 */
class FileStorageAPIController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/{uuid<(.+)>}")
     *
     * @OA\Get(
     *     description="Retrieve a file from the database. Appended as query ",
     *     @OA\Parameter(
     *          in="query",
     *          name="parameters",
     *          @OA\Schema(
     *              type="object",
     *              example="{""w"": 400, ""h"": 200}",
     *              additionalProperties=true
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="The requested file"
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="The file was not found"
     *     )
     * )
     */
    public function getFile(string $uuid, Request $request, QueryBus $bus)
    {
        try {
            return $bus->query(new GetFileResource(Uuid::fromString($uuid), $request->query->all()));
        } catch (FileNotFoundException | InvalidUuidStringException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
    }
}