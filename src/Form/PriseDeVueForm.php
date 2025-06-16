<?php

namespace App\Form;

use App\Entity\Ecole;
use App\Entity\Planche;
use App\Entity\PriseDeVue;
use App\Entity\Theme;
use App\Entity\TypePrise;
use App\Entity\TypeVente;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriseDeVueForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('nombreEleves')
            ->add('nombreClasses')
            ->add('prixEcole')
            ->add('prixParent')
            ->add('commentaire')
            ->add('frequence')
            ->add('baseDeDonneeUtilisee')
            ->add('jourDecharge')
            ->add('endroitInstallation')
            ->add('photographe', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('ecole', EntityType::class, [
                'class' => Ecole::class,
                'choice_label' => 'id',
            ])
            ->add('typePrise', EntityType::class, [
                'class' => TypePrise::class,
                'choice_label' => 'id',
            ])
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'id',
            ])
            ->add('typeVente', EntityType::class, [
                'class' => TypeVente::class,
                'choice_label' => 'id',
            ])
            ->add('planchesIndividuel', EntityType::class, [
                'class' => Planche::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('planchesFratrie', EntityType::class, [
                'class' => Planche::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PriseDeVue::class,
        ]);
    }
}
