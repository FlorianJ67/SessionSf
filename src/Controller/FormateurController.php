<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Formateur;
use App\Form\FormateurType;
use Symfony\Component\HttpFoundation\Request;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(): Response
    {
        return $this->render('formateur/index.html.twig', [
            'controller_name' => 'FormateurController',
        ]);
    }

    #[Route('/formateur/add', name: 'add_formateur')]
    #[Route('/formateur/{id}/edit', name: 'edit_formateur')]
    public function add(ManagerRegistry $doctrine, Formateur $formateur = null, Request $request): Response
    {

        if(!$formateur){
            $formateur = new Formateur();
        }

        $form = $this->createForm(formateurType::class, $formateur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $formateur = $form->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($formateur);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('app_formateur');
        }

        return $this->render('formateur/add.html.twig', [
            'formAddFormateur' => $form->createView(),
            'edit' => $formateur->getId()
        ]);

    }
}
