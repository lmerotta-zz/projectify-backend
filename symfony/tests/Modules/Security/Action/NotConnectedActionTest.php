<?php

namespace App\Tests\Modules\Security\Action;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotConnectedActionTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testItRedirectsSuccessfully()
    {
        $this->client->request(
            'GET',
            '/actions/security/authentication/oauth/not-connected'
        );
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('http://dummy/security/login', $this->client->getResponse()->headers->get('location'));
    }
}
