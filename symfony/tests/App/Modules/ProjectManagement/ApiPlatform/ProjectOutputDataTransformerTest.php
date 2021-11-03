<?php

namespace App\Modules\ProjectManagement\ApiPlatform;

use App\Entity\ProjectManagement\Project;
use App\Entity\Security\User;
use App\Modules\ProjectManagement\Model\ProjectDTO;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;

class ProjectOutputDataTransformerTest extends TestCase
{
    use ProphecyTrait;

    public function testItTransformsTheProjectSuccess()
    {
        $user = $this->prophesize(User::class);
        $id = Uuid::uuid4();
        $project = Project::create($id, 'test name', 'description');
        $project->setCreator($user->reveal());

        $transformer = new ProjectOutputDataTransformer();

        $this->assertTrue($transformer->supportsTransformation($project, ProjectDTO::class));

        $expected = new ProjectDTO();
        $expected->id = $id;
        $expected->creator = $user->reveal();
        $expected->name = "test name";
        $expected->description = "description";

        $output = $transformer->transform($project, ProjectDTO::class);
        $this->assertEquals($expected, $output);
    }
}
