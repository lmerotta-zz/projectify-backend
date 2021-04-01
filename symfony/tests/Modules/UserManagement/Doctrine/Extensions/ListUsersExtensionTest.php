<?php

namespace App\Tests\Modules\UserManagement\Doctrine\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Contracts\Security\Enum\Permission;
use App\Entity\Security\User;
use App\Modules\UserManagement\Doctrine\Extensions\ListUsersExtension;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Security;
use Prophecy\PhpUnit\ProphecyTrait;


class ListUsersExtensionTest extends TestCase
{

    use ProphecyTrait;

    public function testItAddsWhereConditionOnCollectionIfUserHasVIEW_LISTOnCollection()
    {
        $id = Uuid::uuid4();
        $security = $this->prophesize(Security::class);
        $user = $this->prophesize(User::class);
        $qb = $this->prophesize(QueryBuilder::class);

        $user->getPermissions()->shouldBeCalled()->willReturn(Permission::get(Permission::USER_VIEW_LIST));
        $user->getId()->shouldBeCalled()->willReturn($id);
        $security->getUser()->shouldBeCalled()->willReturn($user->reveal());
        $qb->andWhere('u.id = :current_user')->shouldBeCalled()->willReturn($qb->reveal());
        $qb->setParameter('current_user', $id)->shouldBeCalled()->willReturn($qb->reveal());
        $qb->getRootAliases()->shouldBeCalled()->willReturn(['u']);

        $extension = new ListUsersExtension();
        $extension->setSecurity($security->reveal());
        $extension->applyToCollection($qb->reveal(), $this->prophesize(QueryNameGeneratorInterface::class)->reveal(), User::class);
    }

    public function testItAddsWhereConditionOnCollectionIfUserHasVIEW_SELFOnItem()
    {
        $id = Uuid::uuid4();
        $security = $this->prophesize(Security::class);
        $user = $this->prophesize(User::class);
        $qb = $this->prophesize(QueryBuilder::class);

        $user->getPermissions()->shouldBeCalled()->willReturn(Permission::get(Permission::USER_VIEW_SELF));
        $user->getId()->shouldBeCalled()->willReturn($id);
        $security->getUser()->shouldBeCalled()->willReturn($user->reveal());
        $qb->andWhere('u.id = :current_user')->shouldBeCalled()->willReturn($qb->reveal());
        $qb->setParameter('current_user', $id)->shouldBeCalled()->willReturn($qb->reveal());
        $qb->getRootAliases()->shouldBeCalled()->willReturn(['u']);

        $extension = new ListUsersExtension();
        $extension->setSecurity($security->reveal());
        $extension->applyToItem($qb->reveal(), $this->prophesize(QueryNameGeneratorInterface::class)->reveal(), User::class, []);
    }
}
