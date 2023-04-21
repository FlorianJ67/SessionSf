<?php

namespace App\Controller;

use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sessions = $entityManager->getRepository(Session::class)->findAll();

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/session/add', name: 'add_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function add(ManagerRegistry $doctrine, Session $session = null, Request $request): Response
    {

        if(!$session){
            $session = new Session();
        }

        $form = $this->createForm(sessionType::class, $session);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $session = $form->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($session);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('app_session');
        }

        return $this->render('session/add.html.twig', [
            'formAddSession' => $form->createView(),
            'edit' => $session->getId()
        ]);

    }

    #[Route('/session/{id}', name: 'info_session')]
    public function info(Session $session): Response
    {

        return $this->render('session/info.html.twig', [
            'session' => $session
        ]);

    }

}
