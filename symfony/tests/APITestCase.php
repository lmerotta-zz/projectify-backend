<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class APITestCase extends WebTestCase
{
    protected KernelBrowser $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    protected function login(string $username = 'default@default.com', string $password = 'default@default.com')
    {
        $this->client->request('POST',
            '/actions/security/authentication/login',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode(['username' => $username, 'password' => $password]));

        // ask auth
        $verifier_bytes = random_bytes(64);
        $code_verifier = rtrim(strtr(base64_encode($verifier_bytes), "+/", "-_"), "=");
        $challenge_bytes = hash("sha256", $code_verifier, true);
        $code_challenge = rtrim(strtr(base64_encode($challenge_bytes), "+/", "-_"), "=");

        $this->client->request(
            'GET',
            '/actions/security/authentication/authorize',
            [
                'response_type' => 'code',
                'code_challenge' => $code_challenge,
                'code_challenge_method' => 'S256',
                'client_id' => '123456',
                'redirect_uri' => 'http://test.com/test',
                'scope' => ['email'],
            ]
        );

        $location = $this->client->getResponse()->headers->get('location');
        parse_str(parse_url($location)['query'], $query);
        $code = $query['code'];

        // get token
        $this->client->request(
            'POST',
            '/actions/security/authentication/token',
            [
                'grant_type' => 'authorization_code',
                'client_id' => '123456',
                'code_verifier' => $code_verifier,
                'code' => $code,
                'redirect_uri' => 'http://test.com/test'
            ]
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $access_code = $response['access_token'];

        $this->client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer '.$access_code);

    }
}