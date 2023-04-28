<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formateur;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name' , TextType::class, [
                'label' => 'Nom'
            ])
            ->add('place' , IntegerType::class, [
                'label' => 'Place'
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text'
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'format' => 'yyyy-MM-dd',
                'widget' => 'single_text'
            ])
            ->add('formateur', EntityType::class, [
                'label' => 'Formateur',
                'class' => Formateur::class
            ])
            ->add('formation', EntityType::class, [
                'label' => 'Formation',
                'class' => Formation::class
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
