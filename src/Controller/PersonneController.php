<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonneController extends AbstractController
{
    #[Route('/personne/add', name: 'add_personne')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $personne = new Personne();
        $personne->setFirstname("yoan");
        $personne->setName("toto");
        $personne->setAge("25");

        $personne2 = new Personne();
        $personne2->setFirstname("soso");
        $personne2->setName("test");
        $personne2->setAge("18");

        // ajouter insertion dans la transaction
        $entityManager->persist($personne);
        $entityManager->persist($personne2);

        //exécuter la transaction
        $entityManager->flush();

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
        ]);
    }
}
