<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@gmail.com');
        $user->setPassword($this->hasher->hashPassword($user, 'test'));
        $user->setRoles(['ROLE_USER']);

        $user1 = new User();
        $user1->setEmail('admin@gmail.com');
        $user1->setPassword($this->hasher->hashPassword($user, 'test'));
        $user1->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        $manager->persist($user1);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}
