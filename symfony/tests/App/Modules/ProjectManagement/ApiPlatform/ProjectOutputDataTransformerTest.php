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
        $createdAt = new \DateTime();
        $user = $this->prophesize(User::class);
        $id = Uuid::uuid4();
        $project = Project::create($id, 'test name', 'description');
        $project->setCreator($user->reveal())->setCreatedAt($createdAt);

        $transformer = new ProjectOutputDataTransformer();

        $this->assertTrue($transformer->supportsTransformation($project, ProjectDTO::class));

        $expected = new ProjectDTO();
        $expected->id = $id;
        $expected->creator = $user->reveal();
        $expected->name = "test name";
        $expected->description = "description";
        $expected->createdAt = $createdAt;
        $expected->updatedAt = null;

        $output = $transformer->transform($project, ProjectDTO::class);
        $this->assertEquals($expected, $output);
    }
}
