<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Formation;
use App\Form\FormationType;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager->getRepository(Formation::class)->findAll();

        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
            'formations' => $formations,
        ]);
    }

    #[Route('/formation/add', name: 'add_formation')]
    #[Route('/formation/{id}/edit', name: 'edit_formation')]
    public function add(ManagerRegistry $doctrine, Formation $formation = null, Request $request): Response
    {

        if(!$formation){
            $formation = new Formation();
        }

        $form = $this->createForm(formationType::class, $formation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $formation = $form->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($formation);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('app_formation');
        }

        return $this->render('formation/add.html.twig', [
            'formAddFormation' => $form->createView(),
            'edit' => $formation->getId()
        ]);
    }

    #[Route('/formation/{id}', name: 'info_formation')]
    public function info(Formation $formation): Response
    {

        return $this->render('formation/info.html.twig', [
            'formation' => $formation
        ]);

    }
}
