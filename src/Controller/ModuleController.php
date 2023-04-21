<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Module;
use App\Entity\Categorie;
use App\Form\ModuleType;
use App\Form\CategorieType;

class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $modules = $entityManager->getRepository(Module::class)->findAll();

        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    #[Route('/module/add', name: 'add_module')]
    #[Route('/module/{id}/edit', name: 'edit_module')]
    public function add(ManagerRegistry $doctrine, module $module = null, categorie $categorie = null, Request $request): Response
    {

        if(!$module){
            $module = new Module();
        }
        if(!$categorie){
            $categorie = new Categorie();
        }

        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        $formCategorie = $this->createForm(CategorieType::class, $categorie);
        $formCategorie->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $module = $form->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($module);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('app_module');
        }

        if($formCategorie->isSubmitted() && $formCategorie->isValid()){

            $categorie = $formCategorie->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($categorie);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('add_module');
        }

        return $this->render('module/add.html.twig', [
            'formAddModule' => $form->createView(),
            'formAddCategorie' => $formCategorie->createView(),
            'edit' => $module->getId()
        ]);
    }

    #[Route('/module/{id}', name: 'info_module')]
    public function info(Module $module): Response
    {

        return $this->render('module/info.html.twig', [
            'module' => $module
        ]);

    }
}
