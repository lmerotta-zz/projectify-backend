<?php

namespace App\Modules\Security\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

// @codeCoverageIgnore
class LogoutAction
{
    #[Route(
        '/security/authentication/logout',
        name: 'app.security.action.logout',
        methods: ['GET']
    )]
    public function action(): JsonResponse
    {
        // @codeCoverageIgnoreStart
        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
        // @codeCoverageIgnoreEnd
    }
}
