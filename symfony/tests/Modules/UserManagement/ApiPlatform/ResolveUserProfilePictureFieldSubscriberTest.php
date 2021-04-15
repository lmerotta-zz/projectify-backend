<?php

namespace App\Tests\Modules\UserManagement\ApiPlatform;

use ApiPlatform\Core\GraphQl\Resolver\Stage\SerializeStageInterface;
use App\Entity\Security\Role;
use App\Entity\Security\User;
use App\Modules\UserManagement\ApiPlatform\ResolveUserProfilePictureFieldSubscriber;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ResolveUserProfilePictureFieldSubscriberTest extends TestCase
{
    use ProphecyTrait;

    public function testInvokeDoesNotAssignContentUrlOnItemIfNotUser()
    {
        $item = $this->prophesize(Role::class);
        $stage = $this->prophesize(SerializeStageInterface::class);

        $stage->__invoke($item, Role::class, '', [])
            ->shouldBeCalled()
            ->willReturn([$item->reveal()]);

        $resolver = new ResolveUserProfilePictureFieldSubscriber();
        $resolver->setSerializeStage($stage->reveal());
        $resolver($item, Role::class, '', []);
    }

    public function testInvokeAssignsContentUrl()
    {
        $item = User::create(Uuid::uuid4(), '', '', '', '');
        $stage = $this->prophesize(SerializeStageInterface::class);
        $storage = $this->prophesize(UploaderHelper::class);
        $cache = $this->prophesize(CacheManager::class);

        $storage->asset($item, 'profilePictureFile')
            ->shouldBeCalled()
            ->willReturn('test.png');

        $cache->getBrowserPath('test.png', 'user_profile_picture')
            ->shouldBeCalled()
            ->willReturn('cached.png');

        $stage->__invoke($item, User::class, '', [])
            ->shouldBeCalled()
            ->willReturn([$item]);

        $resolver = new ResolveUserProfilePictureFieldSubscriber();
        $resolver->setSerializeStage($stage->reveal());
        $resolver->setUploaderHelper($storage->reveal());
        $resolver->setImageCache($cache->reveal());

        $result = $resolver($item, User::class, '', []);
        $this->assertEquals('cached.png', $result[0]->profilePictureUrl);
    }

    public function testInvokeDoesNotAssignsContentUrlIfProfilePictureIsEmpty()
    {
        $item = User::create(Uuid::uuid4(), '', '', '', '');
        $stage = $this->prophesize(SerializeStageInterface::class);
        $storage = $this->prophesize(UploaderHelper::class);
        $cache = $this->prophesize(CacheManager::class);

        $storage->asset($item, 'profilePictureFile')
            ->shouldBeCalled()
            ->willReturn(null);

        $stage->__invoke([$item], User::class, '', [])
            ->shouldBeCalled()
            ->willReturn([$item]);

        $resolver = new ResolveUserProfilePictureFieldSubscriber();
        $resolver->setSerializeStage($stage->reveal());
        $resolver->setUploaderHelper($storage->reveal());
        $resolver->setImageCache($cache->reveal());

        $result = $resolver([$item], User::class, '', []);
        $this->assertEquals(null, $result[0]->profilePictureUrl);
    }
}
