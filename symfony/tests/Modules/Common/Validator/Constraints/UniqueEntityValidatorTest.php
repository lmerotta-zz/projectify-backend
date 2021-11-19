<?php

namespace App\Tests\Modules\Common\Validator\Constraints;

use App\Entity\Security\User;
use App\Modules\Common\Validator\Constraints\UniqueEntity;
use App\Modules\Common\Validator\Constraints\UniqueEntityValidator;
use App\Modules\UserManagement\Messenger\Commands\SignUserUp;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class UniqueEntityValidatorTest extends TestCase
{

    use ProphecyTrait;

    public function testItTriggersAnErrorIfEntityIsFoundInDatabase()
    {
        $constraint = new UniqueEntity(className: User::class, fields: ['email' => 'email'], propertyPath: 'email');

        $context = $this->prophesize(ExecutionContextInterface::class);
        $violationBuilder = $this->prophesize(ConstraintViolationBuilderInterface::class);
        $em = $this->prophesize(EntityManagerInterface::class);
        $repo = $this->prophesize(UserRepository::class);
        $user = $this->prophesize(User::class);

        $em->getRepository(User::class)->shouldBeCalled()->willReturn($repo->reveal());
        $repo->findOneBy(['email' => 'test@test.com'])->shouldBeCalled()->willReturn($user->reveal());
        $context->buildViolation($constraint->message)->shouldBeCalled()->willReturn($violationBuilder->reveal());
        $violationBuilder->atPath($constraint->propertyPath)->shouldBeCalled()->willReturn($violationBuilder->reveal());
        $violationBuilder->setParameter('{{ fields }}', implode(',', array_keys($constraint->fields)))->shouldBeCalled()->willReturn($violationBuilder->reveal());
        $violationBuilder->addViolation()->shouldBeCalled();

        $bag = new SignUserUp('test@test.com', 'test', 'test', 'test');
        $validator = new UniqueEntityValidator();
        $validator->initialize($context->reveal());
        $validator->setEntityManager($em->reveal());

        $validator->validate($bag, $constraint);
    }

    public function testItShouldPassValidationIfNoDuplicate()
    {
        $constraint = new UniqueEntity(className: User::class, fields: ['email' => 'email'], propertyPath: 'email');


        $context = $this->prophesize(ExecutionContextInterface::class);
        $em = $this->prophesize(EntityManagerInterface::class);
        $repo = $this->prophesize(UserRepository::class);

        $em->getRepository(User::class)->shouldBeCalled()->willReturn($repo->reveal());
        $repo->findOneBy(['email' => 'test@test.com'])->shouldBeCalled()->willReturn(null);
        $context->buildViolation($constraint->message)->shouldNotBeCalled();

        $bag = new SignUserUp('test@test.com', 'test', 'test', 'test');
        $validator = new UniqueEntityValidator();
        $validator->initialize($context->reveal());
        $validator->setEntityManager($em->reveal());

        $validator->validate($bag, $constraint);
    }
}
