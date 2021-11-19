<?php

namespace App\Tests\Modules\UserManagement\GraphQL\Type;

use ApiPlatform\Core\GraphQl\Type\TypeConverterInterface;
use App\Contracts\UserManagement\Enum\UserStatus;
use App\Modules\UserManagement\GraphQL\Type\Definition\UserStatusType;
use App\Modules\UserManagement\GraphQL\Type\UserStatusTypeConverter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\PropertyInfo\Type;

class UserStatusTypeConverterTest extends TestCase
{
    use ProphecyTrait;

    public function testItReturnsTheUserStatusTypeIfTypeIsUserStatus()
    {
        $default = $this->prophesize(TypeConverterInterface::class);
        $type = $this->prophesize(Type::class);

        $type->getBuiltinType()->shouldBeCalled()->willReturn(Type::BUILTIN_TYPE_OBJECT);
        $type->getClassName()->shouldBeCalled()->willReturn(UserStatus::class);

        $converter = new UserStatusTypeConverter($default->reveal());

        $result = $converter->convertType($type->reveal(), true, null, null, null, '', '', null, 1);
        $this->assertEquals(UserStatusType::NAME, $result);
    }

    public function testItCallsTheDefaultTypeConverterIfTypeIsNotUserStatus()
{
    $default = $this->prophesize(TypeConverterInterface::class);
    $type = $this->prophesize(Type::class);

    $default->convertType($type->reveal(), true, null, null, null, '', '', null, 1)->shouldBeCalled()->willReturn('string');

    $type->getBuiltinType()->shouldBeCalled()->willReturn(Type::BUILTIN_TYPE_STRING);

    $converter = new UserStatusTypeConverter($default->reveal());

    $result = $converter->convertType($type->reveal(), true, null, null, null, '', '', null, 1);
    $this->assertEquals('string', $result);
}
}
