<?php

namespace App\Tests\Modules\UserManagement\ApiPlatform;

use App\Contracts\UserManagement\Enum\UserStatus;
use App\Modules\UserManagement\ApiPlatform\UserOutputDataTransformer;
use App\Modules\UserManagement\Model\UserDTO;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PHPUnit\Framework\TestCase;
use App\Entity\Security\User;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class UserOutputDataTransformerTest extends TestCase
{
    use ProphecyTrait;

    public function testItTransformsTheUserSuccess()
    {
        $storage = $this->prophesize(UploaderHelper::class);
        $cache = $this->prophesize(CacheManager::class);

        $user = User::create(
            Uuid::uuid4(),
            'test',
            'last',
            '1234',
            'test@test.com'
        );

        $storage->asset($user, 'profilePictureFile')
            ->shouldBeCalled()
            ->willReturn('test.png');

        $cache->getBrowserPath('test.png', 'user_profile_picture')
            ->shouldBeCalled()
            ->willReturn('cached.png');

        $transformer = new UserOutputDataTransformer();
        $transformer->setImageCache($cache->reveal());
        $transformer->setUploaderHelper($storage->reveal());

        $expected = new UserDTO();
        $expected->firstName = 'test';
        $expected->lastName = 'last';
        $expected->email = 'test@test.com';
        $expected->status = UserStatus::get(UserStatus::SIGNED_UP);
        $expected->id = $user->getId();
        $expected->profilePictureUrl = 'cached.png';

        $this->assertEquals($expected, $transformer->transform($user, UserDTO::class, []));
    }
}
