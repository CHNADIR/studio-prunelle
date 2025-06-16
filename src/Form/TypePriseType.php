<?php

namespace App\Form;

use App\Entity\TypePrise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypePriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Type de prise',
                'attr' => [
                    'placeholder' => 'Ex: Individuel, Groupe, Individuel+Groupe...',
                    'class' => 'form-control'
                ],
                'help' => 'Entrez un type de prise de vue'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypePrise::class,
        ]);
    }
}
