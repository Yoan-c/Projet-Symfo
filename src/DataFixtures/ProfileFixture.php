<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $profile = new Profile();
        $profile->setRs('Facebook');
        $profile->setUrl('https://fr-fr.facebook.com/');
        $manager->persist($profile);

        $profile1 = new Profile();
        $profile1->setRs('Twitter');
        $profile1->setUrl('https://twitter.com/?lang=fr');
        $manager->persist($profile1);

        $profile2 = new Profile();
        $profile2->setRs('Instagram');
        $profile2->setUrl('https://www.instagram.com/?hl=fr');
        $manager->persist($profile2);

        $manager->flush();
    }
}
