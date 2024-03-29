<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\User;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\PdfService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[
    Route('/personne'),
    IsGranted('ROLE_USER')
]
class PersonneController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private Helpers $helpers,
        private EventDispatcherInterface $dispatcher
    ) {
    }
    #[Route('/', name: 'list_personne')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes
        ]);
    }

    #[Route('/all/age/{ageMin}/{ageMax}', name: 'listAge_personne')]
    public function PersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonneByAgeInterval($ageMin, $ageMax);

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
        ]);
    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'listAgeStat_personne')]
    public function StatPersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statPersonneByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMin' => $ageMin,
            'ageMax' => $ageMax
        ]);
    }

    #[
        Route('/all/{page?1}/{nbr?12}', name: 'all_personne'),
        IsGranted("ROLE_USER")
    ]
    public function indexAll(ManagerRegistry $doctrine, $page, $nbr): Response
    {
        // echo ($this->helpers->sayCc());
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]);
        $nbPage = ceil($nbPersonne / $nbr);
        $personnes = $repository->findBy([], [], $nbr, ($page - 1) * $nbr);
        $listAllPersonneEvent = new ListAllPersonneEvent(count($personnes));
        $this->dispatcher->dispatch($listAllPersonneEvent, ListAllPersonneEvent::LIST_ALL_PERSONNE_EVENT);
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
            return $this->redirectToRoute('all_personne');
        }
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne
        ]);
    }

    #[Route('/pdf/{id}', name: 'pdf_personne')]
    public function generatePdf(Personne $personne = null, PdfService $pdf)
    {
        $html = $this->render('personne/detail.html.twig', ['personne' => $personne]);
        $pdf->showPdfFile($html);
    }

    #[Route('/edit/{id?0}', name: 'edit_personne')]
    public function addPersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        Request $req,
        UploaderService $uploader,
        MailerService $mailer
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new = false;
        if (!$personne) {
            $personne = new Personne();
            $new = true;
        }
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove("createdAt");
        $form->remove("updatedAt");
        // mon formulaire va aller traiter la requete
        // ca permet d'associer toute la requete au formulaire (les champs etc...)
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            // formulaire nom + prenom + message  juste un msg faire  $form->getData(); 
            $manager = $doctrine->getManager();

            //récuperation du champs photo du formulaire et defini dans personneType
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($photo) {
                $directory = $this->getParameter('personnes_directory');
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $personne->setImage($uploader->uploadFile($photo, $directory));
            }

            if ($new) {
                $message = " a été ajouté avec succes";
                // $user = new User();
                // $user = $this->getUser();
                $personne->setCreatedBy($this->getUser());
            } else {
                $message = " a été mis a jour avec success";
            }

            $manager->persist($personne);
            $manager->flush();
            if ($new) {
                // on a crée notre evenement 
                $addPersonneEvent = new AddPersonneEvent($personne);
                // on va maintenant le dispatcher
                $this->dispatcher->dispatch($addPersonneEvent, AddPersonneEvent::ADD_PERSONNE_EVENT);
            }
            $this->addFlash('success', $personne->getName() . " " . $message);
            return $this->redirectToRoute("all_personne");
        } else {

            return $this->render('personne/add-personne.html.twig', [
                'varform' => $form->createView()
            ]);
        }
    }

    #[
        Route('/delete/{id}', name: "del_personne"),
        IsGranted('ROLE_ADMIN')
    ]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($personne) {
            $manager = $doctrine->getManager();
            // ajoute la suppression dans la transaction
            $manager->remove($personne);
            // execute la transaction (suppression au niveau de la BDD)
            $manager->flush();
            $this->addFlash('success', "La personne a été supprimé");
        } else {
            $this->addFlash('error', "La personne n'existe pas");
        }
        return $this->redirectToRoute("all_personne");
    }

    #[Route('/update/{id}/{name}/{firstname}/{age}', name: 'update_personne')]
    public function updatePesonne(Personne $personne = null,  ManagerRegistry $doctrine, $name, $firstname, $age)
    {
        if ($personne) {
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash('success', "La personne a été mis à jour avec success");
        } else {
            $this->addFlash('error', "La personne n'existe pas");
        }
        return $this->redirectToRoute("all_personne");
    }
}
