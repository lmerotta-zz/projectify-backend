<?php

namespace App\Tests\Modules\FileManagement\Action;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetFileResourceActionTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testGetMissingFileError()
    {
        $this->client->request('GET', '/actions/assets/7bff7d1f-5db8-492e-b478-172c110e150f');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
