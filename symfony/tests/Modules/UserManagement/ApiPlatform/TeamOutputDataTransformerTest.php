<?php

namespace App\Tests\Modules\UserManagement\ApiPlatform;

use App\Entity\Security\User;
use App\Modules\UserManagement\ApiPlatform\TeamOutputDataTransformer;
use App\Modules\UserManagement\Model\TeamDTO;
use App\Tests\Helpers\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use App\Entity\UserManagement\Team;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;

class TeamOutputDataTransformerTest extends TestCase
{
    use ProphecyTrait;
    use ReflectionTrait;

    public function testItTransformsTheTeamSuccess()
    {
        $user = User::create(
            Uuid::uuid4(),
            'test',
            'last',
            '1234',
            'test@test.com'
        );

        $creationDate = new \DateTimeImmutable();

        $team = Team::create(Uuid::uuid4(), 'team');
        $this->setFieldValue($team, 'createdAt', $creationDate);
        $this->setFieldValue($team, 'owner', $user);

        $transformer = new TeamOutputDataTransformer();

        $expected = new TeamDTO();
        $expected->name = 'team';
        $expected->owner = $user;
        $expected->id = $team->getId();
        $expected->createdAt = $creationDate;

        $this->assertEquals($expected, $transformer->transform($team, TeamDTO::class, []));
    }
}
