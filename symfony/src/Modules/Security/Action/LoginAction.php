<?php


namespace App\Modules\Security\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginAction
{
    /**
     * @Route("/security/authentication/login", name="app.security.action.login", methods={"POST"})
     */
    public function action(): JsonResponse
    {
        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}