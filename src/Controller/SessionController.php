<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    #[Route('/session/{idsession}/removeFromSession/{idstagiaire}/', name: 'remove_from_session')]
    #[ParamConverter("session", options:["mapping" => ["idsession" => "id"]])]
    #[ParamConverter("stagiaire", options:["mapping" => ["idstagiaire" => "id"]])]
    public function removeFromSession(ManagerRegistry $doctrine, Session $session, Stagiaire $stagiaire, Request $request): Response{
        
        $entityManager = $doctrine->getManager();
        $session->removeInscrit($stagiaire);
        $entityManager->flush();

        return $this->redirectToRoute('info_session', ['id'=>$session->getId()]);
    }

    #[Route('/session/{idsession}/addToSession/{idstagiaire}/', name: 'add_to_session')]
    #[ParamConverter("session", options:["mapping" => ["idsession" => "id"]])]
    #[ParamConverter("stagiaire", options:["mapping" => ["idstagiaire" => "id"]])]
    public function addToSession(ManagerRegistry $doctrine, Session $session, Stagiaire $stagiaire, Request $request): Response{
        
        $entityManager = $doctrine->getManager();
        $session->addInscrit($stagiaire);
        $entityManager->flush();

        return $this->redirectToRoute('info_session', ['id'=>$session->getId()]);
    }

    #[Route('/session/{id}', name: 'info_session')]
    // #[IsGranted("ROLE_USER")]
    public function info(Session $session, SessionRepository $sr): Response
    {
        $session_id = $session->getId();
        $nonInscrits = $sr->findNonInscrits($session_id);
        // $nonProgrammes = $sr->findNonProgrammes($session_id);

        return $this->render('session/info.html.twig', [
            'session' => $session,
            'nonInscits' => $nonInscrits,
            // 'nonProgrammes' => $nonProgrammes
        ]);
    }
}
