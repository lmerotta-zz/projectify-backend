<?php

namespace App\Modules\FileManagement\Controller;

use App\Modules\Common\Bus\CommandBus;
use App\Modules\Common\Bus\QueryBus;
use App\Modules\FileManagement\Enum\FileContext;
use App\Modules\FileManagement\Exception\FileNotFoundException;
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
     * @Rest\Post("/save")
     * @OA\Post(
     *     description="Save a file resource",
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(ref=@Model(type=SaveFileRequest::class))
     *          )
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="Validation errors",
     *          @Model(type=App\Modules\Common\Model\ValidationErrors::class)
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="File uploaded successfully",
     *          @Model(type=App\Modules\Common\Model\UUID::class)
     *     )
     * )
     *
     * @Rest\View()
     */
    public function postSaveFile(Request $request, ValidatorInterface $validator, CommandBus $bus): ?View
    {
        $fileRequest = new SaveFileRequest();
        $fileRequest->context = (string)$request->request->get('context', '');
        $fileRequest->file = $request->files->get('file');

        $errors = $validator->validate($fileRequest, null, ['Default', $fileRequest->context]);
        if ($errors->count() > 0) {
            return View::create($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $id = UuidV4::uuid4();
        $bus->dispatch(new SaveFile($id, FileContext::get($fileRequest->context), $fileRequest->file));

        return View::create($id);
    }
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