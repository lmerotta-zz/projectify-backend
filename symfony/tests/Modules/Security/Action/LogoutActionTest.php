<?php

namespace App\Tests\Modules\Security\Action;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LogOutActionTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLogsInSuccessfully()
    {
        $this->client->request(
            'GET',
            '/actions/security/authentication/logout');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }
}
