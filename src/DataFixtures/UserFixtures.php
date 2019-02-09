<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';
    public const ADMIN_REFERENCE = 'admin';
    public const SUPER_ADMIN_REFERENCE = 'super_admin';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $testPassword = 'test';

        $admin = new User();
        $admin->setFullName('Admin admin');
        $admin->setUserName('admin');
        $admin->setEmail('admin@site.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setCreatedAt(new \DateTime('now'));
        $admin->setPassword(
            $testPassword
        );

        $manager->persist($admin);
        $manager->flush();
        $this->addReference(self::ADMIN_REFERENCE, $admin);

        $super_admin = new User();
        $super_admin->setFullName('Super admin');
        $super_admin->setUserName('superadmin');
        $super_admin->setEmail('superadmin@site.com');
        $super_admin->setRoles(['ROLE_SUPER_ADMIN']);
        $super_admin->setPassword(
            $testPassword
        );

        $manager->persist($super_admin);
        $manager->flush();
        $this->addReference(self::SUPER_ADMIN_REFERENCE, $super_admin);

        $user = new User();
        $user->setFullName('Test Author1');
        $user->setUserName('Author1');
        $user->setPassword(
            $testPassword
        );
        $user->setEmail('test@author1.com');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);
        $manager->flush();
        $this->addReference(self::USER_REFERENCE, $user);
    }
}
