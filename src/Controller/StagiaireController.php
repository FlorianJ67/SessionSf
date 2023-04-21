<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Stagiaire;
use App\Form\StagiaireType;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $stagiaires = $entityManager->getRepository(stagiaire::class)->findAll();

        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }

    #[Route('/stagiaire/add', name: 'add_stagiaire')]
    #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')]
    public function add(ManagerRegistry $doctrine, Stagiaire $stagiaire = null, Request $request): Response
    {

        if(!$stagiaire){
            $stagiaire = new Stagiaire();
        }

        $form = $this->createForm(stagiaireType::class, $stagiaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $stagiaire = $form->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($stagiaire);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('app_stagiaire');
        }

        return $this->render('stagiaire/add.html.twig', [
            'formAddStagiaire' => $form->createView(),
            'edit' => $stagiaire->getId()
        ]);
    }
    
    #[Route('/stagiaire/{id}', name: 'info_stagiaire')]
    public function info(Stagiaire $stagiaire): Response
    {

        return $this->render('stagiaire/info.html.twig', [
            'stagiaire' => $stagiaire
        ]);

    }
}
