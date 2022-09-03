<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'name' => 'yoan',
            "prenom" => 'armand'
        ]);
    }

    //  #[Route('/hello/{name}', name: 'hello')]
    public function hello(Request $req, $name): Response
    {
        // dd($req);
        return $this->render('main/hello.html.twig', [
            'nom' => $name,
            'path' => '   '
        ]);
    }

    #[Route('/template', name: 'temp')]
    public function temp(): Response
    {
        // dd($req);
        return $this->render('template.html.twig');
    }
}
