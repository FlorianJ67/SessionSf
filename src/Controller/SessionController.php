<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Entity\ContenuSession;
use App\Form\ContenuSessionType;
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

        $form = $this->createForm(SessionType::class, $session);
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

    #[Route('/session/{idsession}/removeModuleFromSession/{idmodule}/', name: 'remove_module_from_session')]
    #[ParamConverter("session", options:["mapping" => ["idsession" => "id"]])]
    #[ParamConverter("module", options:["mapping" => ["idmodule" => "id"]])]
    public function removeModuleFromSession(ManagerRegistry $doctrine, Session $session, ContenuSession $contenuSession, Request $request): Response{
        
        $entityManager = $doctrine->getManager();
        $session->removeContenuSession($contenuSession);

        $entityManager->flush();

        return $this->redirectToRoute('info_session', ['id'=>$session->getId()]);
    }

    #[Route('/session/{idsession}/addModuleToSession/{idmodule}/', name: 'add_module_to_session')]
    #[ParamConverter("session", options:["mapping" => ["idsession" => "id"]])]
    #[ParamConverter("module", options:["mapping" => ["idmodule" => "id"]])]
    public function addModuleToSession(ManagerRegistry $doctrine, Session $session, ContenuSession $contenuSession, Request $request): Response{
        
        $entityManager = $doctrine->getManager();
        $session->addContenuSession($contenuSession);
        $entityManager->flush();

        return $this->redirectToRoute('info_session', ['id'=>$session->getId()]);
    }


    #[Route('/session/{idsession}/removeStagiaireFromSession/{idstagiaire}/', name: 'remove_stagiaire_from_session')]
    #[ParamConverter("session", options:["mapping" => ["idsession" => "id"]])]
    #[ParamConverter("stagiaire", options:["mapping" => ["idstagiaire" => "id"]])]
    public function removeStagiaireFromSession(ManagerRegistry $doctrine, Session $session, Stagiaire $stagiaire, Request $request): Response{
        
        $entityManager = $doctrine->getManager();
        $session->removeInscrit($stagiaire);
        $entityManager->flush();

        return $this->redirectToRoute('info_session', ['id'=>$session->getId()]);
    }

    #[Route('/session/{idsession}/addStagiaireToSession/{idstagiaire}/', name: 'add_stagiaire_to_session')]
    #[ParamConverter("session", options:["mapping" => ["idsession" => "id"]])]
    #[ParamConverter("stagiaire", options:["mapping" => ["idstagiaire" => "id"]])]
    public function addStagiaireToSession(ManagerRegistry $doctrine, Session $session, Stagiaire $stagiaire, Request $request): Response{
        
        $entityManager = $doctrine->getManager();
        $session->addInscrit($stagiaire);
        $entityManager->flush();

        return $this->redirectToRoute('info_session', ['id'=>$session->getId()]);
    }

    #[Route('/session/{id}', name: 'info_session')]
    // #[IsGranted("ROLE_USER")]
    public function info(ManagerRegistry $doctrine,Session $session, ContenuSession $contenuSession= null, Request $request, SessionRepository $sr): Response
    {
        $session_id = $session->getId();
        $nonInscrits = $sr->findNonInscrits($session_id);
        $nonModules = $sr->findNonModule($session_id);

        $form = $this->createForm(ContenuSessionType::class, $contenuSession);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            $contenu = $form->getData();
            // On donne la session actuel dans le champ du formulaire
            $contenu->setSession($session);  

            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($contenu);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('info_session', ['id'=>$session_id]);
        }

        return $this->render('session/info.html.twig', [
            'session' => $session,
            'nonInscrits' => $nonInscrits,
            'nonModules' => $nonModules,
            'formAddContenu' => $form->createView()
        ]);
    }
}
