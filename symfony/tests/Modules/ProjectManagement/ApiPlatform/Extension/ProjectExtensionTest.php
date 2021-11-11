<?php

namespace App\Tests\Modules\ProjectManagement\ApiPlatform\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Contracts\Security\Enum\Permission;
use App\Entity\ProjectManagement\Project;
use App\Entity\Security\User;
use App\Modules\ProjectManagement\ApiPlatform\Extension\ProjectExtension;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Security\Core\Security;

class ProjectExtensionTest extends TestCase
{
    use ProphecyTrait;

    public function testItAddsTheWhereStatementsOnItemAndCollection()
    {
        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $security = $this->prophesize(Security::class);
        $user = $this->prophesize(User::class);

        $security->isGranted(Permission::PROJECT_VIEW)->shouldBeCalledTimes(2)->willReturn(true);
        $security->getUser()->shouldBeCalledTimes(2)->willReturn($user->reveal());

        $queryBuilder->getRootAliases()->shouldBeCalledTimes(2)->willReturn(['o']);
        $queryBuilder->andWhere('o.creator = :current_user')->shouldBeCalledTimes(2);
        $queryBuilder->setParameter('current_user', $user->reveal())->shouldBeCalledTimes(2);

        $extension = new ProjectExtension();
        $extension->setSecurity($security->reveal());

        $extension->applyToCollection($queryBuilder->reveal(), $this->prophesize(QueryNameGeneratorInterface::class)->reveal(), Project::class);
        $extension->applyToItem($queryBuilder->reveal(), $this->prophesize(QueryNameGeneratorInterface::class)->reveal(), Project::class, []);
    }
}
