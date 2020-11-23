<?php

namespace App\Modules\UserManagement\Controller;

use App\Modules\UserManagement\HTTP\V1\Request\CreateUserRequest;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Rest\Route("/configuration")
 */
class UserConfigurationAPIController
{

    /**
     * @Rest\Post("/create-admin-user")
     * @ParamConverter("createRequest", converter="fos_rest.request_body")
     */
    public function postCreateAdminUser(Request $request, CreateUserRequest $createRequest, ConstraintViolationListInterface $validationErrors)
    {
        dd([$request->files->count(), $createRequest, $validationErrors]);
    }
}
