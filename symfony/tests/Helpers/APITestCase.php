<?php

namespace App\Tests\Helpers;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as APIPlatformTestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class APITestCase extends APIPlatformTestCase
{
    protected Client $client;

    public function setUp(): void
    {
        self::bootKernel();
        $this->client = static::createClient();
    }

    protected function createUser(string $email, string $firstName, string $lastName, ?string $password = null): ResponseInterface
    {
        $realPassword = $password ?? $email;
        $mutation = <<<GQL
            mutation {
                createUser(input: {email: "$email", password: "$realPassword", firstName: "$firstName", lastName: "$lastName"}) {
                    user {
                        id
                   }
                }
            }
        GQL;

        $body = ['query' => $mutation, 'variables' => null];
        return $this->client->request('POST', '/api/graphql', ['json' => $body]);
    }

    protected function graphql(string $query, ?array $variables = null): ResponseInterface
    {
        $body = ['query' => $query, 'variables' => $variables];
        return $this->client->request('POST', '/api/graphql', ['json' => $body]);
    }

    protected function login(string $username = 'default@default.com', ?string $password = null)
    {
        $this->client->request('POST',
            '/actions/security/authentication/login',
            ['json' => ['username' => $username, 'password' => $password ?? $username]]);

        // ask auth
        $verifier_bytes = random_bytes(64);
        $code_verifier = rtrim(strtr(base64_encode($verifier_bytes), "+/", "-_"), "=");
        $challenge_bytes = hash("sha256", $code_verifier, true);
        $code_challenge = rtrim(strtr(base64_encode($challenge_bytes), "+/", "-_"), "=");

        $response = $this->client->request(
            'GET',
            '/actions/security/authentication/authorize',
            [
                'extra' => [
                    'parameters' => [
                        'response_type' => 'code',
                        'code_challenge' => $code_challenge,
                        'code_challenge_method' => 'S256',
                        'client_id' => '123456',
                        'redirect_uri' => 'http://test.com/test',
                        'scope' => ['email']
                    ],
                ]
            ]
        );

        $location = $response->getHeaders(false)['location'][0];
        parse_str(parse_url($location)['query'], $query);
        $code = $query['code'];
        // get token
        $response = $this->client->request(
            'POST',
            '/actions/security/authentication/token',
            [
                'extra' => [
                    'parameters' => [ 'grant_type' => 'authorization_code',
                        'client_id' => '123456',
                        'code_verifier' => $code_verifier,
                        'code' => $code,
                        'redirect_uri' => 'http://test.com/test']
                ]
            ]
        );

        $data = json_decode($response->getContent(), true);
        $access_code = $data['access_token'];

        $this->client = static::createClient([], ['headers' => ['authorization' => 'Bearer '.$access_code]]);
    }

    protected function logout(): void
    {
        $this->client = static::createClient();
    }
}