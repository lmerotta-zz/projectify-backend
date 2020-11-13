<?php

namespace App\Tests\Modules\FileManagement\Controller;

use App\Contracts\FileManagement\Enum\FileContext;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStorageAPIControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testSaveFileSuccess()
    {
        $file = new UploadedFile(__DIR__.'/../fixtures/40.jpg', '40.jpg');
        $this->client->request('POST', '/api/v1/assets/save', ['context' => FileContext::USER_PROFILE_PICTURE], ['file' => $file]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('uuid', json_decode($this->client->getResponse()->getContent(), true));

        $fs = new Filesystem(new Local(__DIR__.'/../../../storage'));
        foreach($fs->listContents() as $f) {
            if ($f['type'] === 'dir') {
                $fs->deleteDir($f['path']);
            }
        }
    }

    public function testSaveFileMissingFileError()
    {
        $this->client->request('POST', '/api/v1/assets/save', ['context' => FileContext::USER_PROFILE_PICTURE]);
        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
        $this->assertContains('file', json_decode($this->client->getResponse()->getContent(), true)[0]['property_path']);
    }

    public function testGetMissingFileError()
    {
        $this->client->request('GET', '/api/v1/assets/7bff7d1f-5db8-492e-b478-172c110e150f');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
