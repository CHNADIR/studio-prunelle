<?php

namespace App\Form;

use App\Entity\Planche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PlancheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la planche',
                'attr' => [
                    'placeholder' => 'Ex: Portrait 13x18 noir et blanc',
                    'class' => 'form-control'
                ],
                'help' => 'Entrez un nom descriptif pour la planche'
            ])
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Individuel' => Planche::CATEGORIE_INDIVIDUEL,
                    'Fratrie' => Planche::CATEGORIE_FRATRIE,
                    'Groupe classe' => Planche::CATEGORIE_GROUPE_CLASSE
                ],
                'placeholder' => 'Choisissez une catégorie',
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-select']
            ])
            ->add('descriptionContenu', TextareaType::class, [
                'label' => 'Description du contenu',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Décrivez le contenu de la planche...'
                ]
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
                'required' => false,
                'attr' => [
                    'placeholder' => '0.00'
                ],
                'help' => 'Prix en euros'
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image de la planche',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG)',
                    ])
                ],
                'help' => 'Téléchargez une image d\'exemple de la planche (max 2Mo)'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planche::class,
        ]);
    }
}
