<?php

namespace App\Tests\Modules\UserManagement\Messenger\Events;

use App\Entity\Security\Role;
use App\Entity\Security\User;
use App\Modules\UserManagement\Messenger\Events\AddDefaultRoleWhenUserSignedUp;
use App\Modules\UserManagement\Messenger\Events\UserSignedUp;
use App\Repository\Security\RoleRepository;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class AddDefaultRoleWhenUserSignedUpTest extends TestCase
{
    public function testItAddsTheRoleUserOnEvent()
    {
        $id = Uuid::uuid4();
        $em = $this->prophesize(EntityManagerInterface::class);
        $roleRepo = $this->prophesize(RoleRepository::class);
        $userRepo = $this->prophesize(UserRepository::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $user = $this->prophesize(User::class);
        $role = $this->prophesize(Role::class);

        $roleRepo->find('ROLE_USER')->shouldBeCalled()->willReturn($role->reveal());
        $userRepo->find($id)->shouldBeCalled()->willReturn($user->reveal());
        $user->addRole($role->reveal())->shouldBeCalled()->willReturn($user->reveal());
        $em->flush()->shouldBeCalled();

        $handler = new AddDefaultRoleWhenUserSignedUp();
        $handler->setEm($em->reveal());
        $handler->setRoleRepository($roleRepo->reveal());
        $handler->setUserRepository($userRepo->reveal());
        $handler->setLogger($logger->reveal());

        $handler(new UserSignedUp($id));
    }
}
