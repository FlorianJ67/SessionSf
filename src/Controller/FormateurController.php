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
    public function index(EntityManagerInterface $entityManager): Response
    {
        $formateurs = $entityManager->getRepository(formateur::class)->findAll();
        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs
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

    #[Route('/formateur/{id}', name: 'info_formateur')]
    public function info(Formateur $formateur): Response
    {

        return $this->render('formateur/info.html.twig', [
            'formateur' => $formateur
        ]);

    }
}
