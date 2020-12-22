<?php

namespace App\Tests\Modules\Security\Action;

use App\Modules\Common\Bus\CommandBus;
use App\Modules\UserManagement\Messenger\Commands\SignUserUp;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginActionTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testGetMissingFileError()
    {
        $messenger = static::$container->get(CommandBus::class);
        $messenger->dispatch(new SignUserUp('test@test.com', 'test', 'test', '1234'));

        $this->client->request(
            'POST',
            '/actions/security/authentication/login',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode(['username' => 'test@test.com', 'password' => '1234'])
        );
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }
}
