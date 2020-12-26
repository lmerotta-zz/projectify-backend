<?php

namespace App\Tests\Modules\FileManagement\Action;

use App\Tests\APITestCase;

class GetFileResourceActionTest extends APITestCase
{

    public function testGetMissingFileError()
    {
        $this->login();
        $this->client->request('GET', '/actions/assets/7bff7d1f-5db8-492e-b478-172c110e150f');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
