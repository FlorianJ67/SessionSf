<?php

namespace App\Form;

use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('place' , IntegerType::class, [
                'label' => 'Place'
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'format' => 'yyyy-MM-dd'
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'format' => 'yyyy-MM-dd'
            ])
            ->add('formateur', EntityType::class, [
                'label' => 'Formateur'
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
