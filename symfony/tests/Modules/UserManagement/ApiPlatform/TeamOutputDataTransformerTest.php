<?php

namespace App\Tests\Modules\UserManagement\ApiPlatform;

use App\Entity\Security\User;
use App\Modules\UserManagement\ApiPlatform\TeamOutputDataTransformer;
use App\Modules\UserManagement\Model\TeamDTO;
use PHPUnit\Framework\TestCase;
use App\Entity\UserManagement\Team;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;

class TeamOutputDataTransformerTest extends TestCase
{
    use ProphecyTrait;

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
        $team->setCreatedAt($creationDate)->setOwner($user);

        $transformer = new TeamOutputDataTransformer();

        $expected = new TeamDTO();
        $expected->name = 'team';
        $expected->owner = $user;
        $expected->id = $team->getId();
        $expected->createdAt = $creationDate;

        $this->assertEquals($expected, $transformer->transform($team, TeamDTO::class, []));
    }
}
