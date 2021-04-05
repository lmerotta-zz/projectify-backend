<?php

namespace App\Modules\Security\Action;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RedirectAfterOAuthAction
{
    #[Route(
        '/security/authentication/oauth/connected',
        name: 'app.security.action.oauth.connected',
        methods: ['GET']
    )]
    public function action(Request $request): RedirectResponse
    {
        return new RedirectResponse($_ENV['FRONTEND_URL'].$request->get('target'));
    }
}
