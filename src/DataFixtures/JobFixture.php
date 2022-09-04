<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $jobs = [
            'Data scientist',
            'Statisticient',
            'Analyste cyber-sécurité',
            'Médecin ORL',
            'Échographiste',
            'Mathématicien',
            'Ingénieur logiciel',
            'Analyste informnatique',
            'Pathologiste du discours / langage',
            'Acutaire',
            'Ergothérapeute',
            'Directeur des Ressources Humaines',
            'Hygiéniste dentaire',
        ];
        for ($i = 0; $i < count($jobs); $i++) {
            $job = new Job();
            $job->setDesignation($jobs[$i]);
            $manager->persist($job);
        }
        $manager->flush();
    }
}
