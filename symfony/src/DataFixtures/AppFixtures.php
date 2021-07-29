<?php

namespace App\DataFixtures;

use App\Entity\Security\Role;
use App\Entity\Security\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;
use Trikoder\Bundle\OAuth2Bundle\Model\RedirectUri;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;

class AppFixtures extends Fixture
{
    protected PasswordHasherFactoryInterface $encoderFactory;

    public function __construct(PasswordHasherFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function load(ObjectManager $manager): void
    {
        // Create a default user, and assign it the ROLE_USER role
        $role = $manager->getRepository(Role::class)->find('ROLE_USER');

        $user = User::create(
            Uuid::uuid4(),
            'default',
            'default',
            $this->encoderFactory->getPasswordHasher(User::class)->hash('default@default.com'),
            'default@default.com'
        );

        $user->addRole($role);
        $manager->persist($user);

        // oauth
        $c = new Client('123456', null);
        $c->setActive(true)
            ->setAllowPlainTextPkce(false)
            ->setGrants(new Grant('authorization_code'))
            ->setScopes(new Scope('email'))
            ->setRedirectUris(new RedirectUri('http://test.com/test'));

        $manager->persist($c);

        $manager->flush();
    }
}
