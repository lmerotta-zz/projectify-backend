<?php

namespace App\Modules\FileManagement\Controller;

use App\Modules\FileManagement\HTTP\V1\Request\SaveFileRequest;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Rest\Route("/assets")
 */
class FileStorageAPIController
{
    /**
     * @Rest\Post("/save")
     * @OA\Post(
     *     description="Save a file resource",
     *     @OA\Response(
     *          response="422",
     *          description="Validation errors",
     *          @Model(type=App\Modules\Common\Model\ValidationErrors::class)
     *     )
     * )
     *
     * @Rest\View()
     */
    public function postSaveFile(Request $request, ValidatorInterface $validator): ?View
    {
        $fileRequest = new SaveFileRequest();
        $fileRequest->context = (string)$request->request->get('context', '');
        $fileRequest->file = $request->files->get('file');

        $errors = $validator->validate($fileRequest, null, ['Default', $fileRequest->context]);
        if ($errors->count() > 0) {
            return View::create($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return null;
    }
}