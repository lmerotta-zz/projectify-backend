<?php


namespace App\Modules\Security\Action;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @codeCoverageIgnore
 */
class OpenIdConfigurationAction
{
    #[Route(
        '/security/authentication/.well-known/openid-configuration',
        name: 'app.security.action.openid-configuration',
        methods: ['GET']
    )]
    public function action(Request $request, UrlGeneratorInterface $generator): JsonResponse
    {
        return new JsonResponse([
            'issuer' => $request->getSchemeAndHttpHost(),
            'authorization_endpoint' => $generator->generate(name: 'oauth2_authorize', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            'token_endpoint' => $generator->generate(name: 'oauth2_token', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            'userinfo_endpoint' => '',
            'end_session_endpoint' => '',
            'jwks_uri' => ''
        ]);
    }
}