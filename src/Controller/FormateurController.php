<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Formateur;
use App\Form\FormateurType;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    #[Route('/formateur/{id}/edit', name: 'edit_formateur')]
    public function index(EntityManagerInterface $entityManager, ManagerRegistry $doctrine, Formateur $formateur = null, Request $request): Response
    {
        $formateurs = $entityManager->getRepository(formateur::class)->findAll();

        if(!$formateur){
            $formateur = new Formateur();
        }

        $form = $this->createForm(FormateurType::class, $formateur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $formateur = $form->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($formateur);
            // insert into (execute)
            $entityManager->flush();

        }

        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs,
            'formAddFormateur' => $form->createView(),
            'edit' => $formateur->getId()
        ]);
    }

    #[Route('/formateur/{id}', name: 'info_formateur')]
    public function info(Formateur $formateur): Response
    {

        return $this->render('formateur/info.html.twig', [
            'formateur' => $formateur
        ]);

    }
}
