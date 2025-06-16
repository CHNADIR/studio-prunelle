<?php

namespace App\Form;

use App\Entity\Ecole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code',
                'attr' => ['maxlength' => 5, 'placeholder' => 'Ex: E001'],
                'help' => 'Code unique de l\'école (max 5 caractères)'
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Type d\'établissement',
                'choices' => [
                    'École maternelle' => 'Maternelle',
                    'École primaire' => 'Primaire',
                    'Collège' => 'College',
                    'Lycée' => 'Lycee',
                    'Autre' => 'Autre'
                ],
                'placeholder' => 'Choisir un type d\'établissement'
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'école',
                'attr' => ['maxlength' => 255, 'placeholder' => 'Nom complet de l\'établissement']
            ])
            ->add('rue', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['maxlength' => 255, 'placeholder' => 'Numéro et nom de rue']
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => ['maxlength' => 100, 'placeholder' => 'Nom de la ville']
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['maxlength' => 10, 'placeholder' => '75000']
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['maxlength' => 20, 'placeholder' => '01 23 45 67 89']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email de contact',
                'required' => false,
                'attr' => ['maxlength' => 100, 'placeholder' => 'contact@ecole.fr']
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'École active',
                'required' => false,
                'help' => 'Décochez pour désactiver l\'école sans la supprimer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ecole::class,
        ]);
    }
}