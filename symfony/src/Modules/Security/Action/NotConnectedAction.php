<?php

namespace App\Modules\Security\Action;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class NotConnectedAction
{
    /**
     * @Route("/security/authentication/oauth/not-connected", name="app.security.action.oauth.not_connected", methods={"GET"})
     */
    public function action(): RedirectResponse
    {
        return new RedirectResponse($_ENV['FRONTEND_URL']."/security/login");
    }
}
