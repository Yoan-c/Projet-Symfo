<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $datas = [
            'Yoga',
            'Cuisine',
            'Patisserie',
            'Photographie',
            'Blogging',
            'Lecture',
            'Apprendre une langue',
            'Construction Lego',
            'Dessin',
            'Coloriage',
            'Peinture',
            'Se lancer dans le tissage de tapis',
            'Créer des vetements ou des cosplay',
            'Fabriquer des bijoux',
            'Travailler le metal',
            'Décorer des galets',
            'Faire des puzzleds avec de plus en plus de pièces',
            'Créer des miniature (maison, voiture, trains, bateaux...)',
            'Améliorer son espace de vie',
            'Apprendre à jongler',
            "Faire partie d'un club de lecture",
            'Apprendre la programmation informatique',
            'En apprendre plus sur le survivalisme',
            'Créer une chaine youtube',
            'Jouer au fléchette',
            'Apprendre à chanter',

        ];
        for ($i = 0; $i < count($datas); $i++) {
            $hobby = new Hobby();
            $hobby->setDesignation($datas[$i]);
            $manager->persist($hobby);
        }
        $manager->flush();
    }
}
