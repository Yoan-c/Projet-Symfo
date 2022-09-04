<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'list_personne')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
        ]);
    }

    #[Route('/all/{page?1}/{nbr?12}', name: 'all_personne')]
    public function indexAll(ManagerRegistry $doctrine, $page, $nbr): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]);
        $nbPage = ceil($nbPersonne / $nbr);
        $personnes = $repository->findBy([], [], $nbr, ($page - 1) * $nbr);
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbPage' => $nbPage,
            'page' => $page,
            'nbr' => $nbr
        ]);
    }

    // avec param converteur, on convertir le parametres en objet
    #[Route('/{id<\d+>}', name: 'detail_personne')]
    public function detail(/*ManagerRegistry $doctrine, $id*/Personne $personne = null): Response
    {
        /*
        $repository = $doctrine->getRepository(Personne::class);
        $personne = $repository->find($id);
        */
        if (!$personne) {
            // $this->addFlash('error', "La personne d'id $id n'existe pas");
            $this->addFlash('error', "La personne n'existe pas");
            return $this->redirectToRoute('list_personne');
        }
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
        ]);
    }

    #[Route('/add', name: 'add_personne')]
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
