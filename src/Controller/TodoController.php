<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo')]
class TodoController extends AbstractController
{

    #[Route('/', name: 'todo')]
    public function index(Request $req): Response
    {
        $session = $req->getSession();
        //Afficher notre tableau de Todo
        if (!$session->has('todos')) {
            $todos = [
                'achat' => 'Acheter une clé USB',
                'cours' => 'Finaliser mon cours',
                'correction' => 'Corriger mes examens'
            ];
            $session->set('todos', $todos);
            $this->addFlash('info', "La liste des todo viens d'être initialisé");
        }


        return $this->render('todo/index.html.twig', [
            'controller_name' => 'TodoController',
        ]);
    }

    #[Route('/add/{name}/{content}', name: "add", defaults: ['content' => 'contenu par defaut'])]
    public function addTodo(Request $req, $name, $content)
    {
        $session = $req->getSession();
        if ($session->has('todos')) {
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                $this->addFlash('error', "Le todo d'id $name existe déjà dans la liste");
            } else {
                $todos[$name] = $content;
                $this->addFlash('success', "Le todo d'id $name à été ajouté avec success");
                $session->set('todos', $todos);
            }
        } else {

            $this->addFlash('error', "La liste des todos n'est pas encore initialisé");
        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/update/{name?achat}/{content?test}', name: "update")]
    public function updateTodo(Request $req, $name, $content)
    {
        $session = $req->getSession();
        if ($session->has('todos')) {
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                $this->addFlash('success', "Le todo d'id $name a bien été mis à jour");
                $todos[$name] = $content;
                $session->set('todos', $todos);
            } else {
                $this->addFlash('error', "Le todo d'id $name existe pas");
            }
        } else {

            $this->addFlash('error', "La liste des todos n'est pas encore initialisé");
        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/delete/{name}', name: "delete")]
    public function deleteTodo(Request $req, $name)
    {
        $session = $req->getSession();
        if ($session->has('todos')) {
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                $this->addFlash('success', "Le todo d'id $name a bien été supprimer");
                unset($todos[$name]);
                $session->set('todos', $todos);
            } else {
                $this->addFlash('error', "Le todo d'id $name existe pas");
            }
        } else {

            $this->addFlash('error', "La liste des todos n'est pas encore initialisé");
        }
        return $this->redirectToRoute('todo');
    }

    #[Route('/reset', name: "reset")]
    public function resetTodo(Request $req)
    {
        $session = $req->getSession();
        $session->remove('todos');
        return $this->redirectToRoute('todo');
    }

    #[Route(
        '/multi/{nb1}/{nb2}',
        name: "multi",
        requirements: ['nb1' => '/d+', 'nb2' => '/d+']
    )]
    public function multiplication($nb1, $nb2)
    {
        $result = $nb1 * $nb2;
        return new Response("<h1>$result</h1>");
    }

    #[Route(
        '/multi2/{nb1<\d+>}/{nb2<\d+>}',
        name: "multi"
    )]
    public function multiplication2($nb1, $nb2)
    {
        $result = $nb1 * $nb2;
        return new Response("<h1>$result</h1>");
    }
}
