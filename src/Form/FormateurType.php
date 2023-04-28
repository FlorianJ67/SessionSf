<?php

namespace App\Form;

use App\Entity\Formateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class, [
            'label' => 'Nom'
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Prenom'
        ])
        ->add('sex', TextType::class, [
            'label' => 'Sex'
        ])
        ->add('tel', TextType::class, [
            'label' => 'Téléphone'
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville'
        ])
        ->add('birthday', DateType::class, [
            'label' => 'Date de naissance'
        ])
        ->add('email', EmailType::class, [
            'label' => 'eMail'
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Ajouter'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formateur::class,
        ]);
    }
}
