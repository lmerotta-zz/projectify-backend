<?php

namespace App\Modules\ProjectManagement\ApiPlatform;

use App\Entity\ProjectManagement\Project;
use App\Entity\Security\User;
use App\Modules\ProjectManagement\Model\ProjectDTO;
use App\Tests\Helpers\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;

class ProjectOutputDataTransformerTest extends TestCase
{
    use ProphecyTrait;
    use ReflectionTrait;

    public function testItTransformsTheProjectSuccess()
    {
        $createdAt = new \DateTimeImmutable();
        $user = $this->prophesize(User::class);
        $id = Uuid::uuid4();
        $project = Project::create($id, 'test name', 'description');
        $this->setFieldValue($project, 'creator', $user->reveal());
        $this->setFieldValue($project, 'createdAt', $createdAt);
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
