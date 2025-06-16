<?php

namespace App\Form;

use App\Entity\Ecole;
use App\Entity\Planche;
use App\Entity\PriseDeVue;
use App\Entity\Theme;
use App\Entity\TypePrise;
use App\Entity\TypeVente;
use App\Entity\User;
use App\Repository\EcoleRepository;
use App\Repository\PlancheRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriseDeVueForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $canEditFull = $options['can_edit_full'];
        
        $builder
            ->add('date', DateTimeType::class, [
                'label' => 'Date de la prise de vue',
                'widget' => 'single_text',
                'disabled' => !$canEditFull,
            ])
            ->add('ecole', EntityType::class, [
                'class' => Ecole::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une école',
                'disabled' => !$canEditFull,
                'query_builder' => function (EcoleRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.active = :active')
                        ->setParameter('active', true)
                        ->orderBy('e.nom', 'ASC');
                },
            ])
            ->add('photographe', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'placeholder' => 'Sélectionner un photographe',
                'disabled' => !$canEditFull,
                'query_builder' => function (UserRepository $ur) {
                    return $ur->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_PHOTOGRAPHE%')
                        ->orderBy('u.email', 'ASC');
                },
            ])
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un thème',
                'required' => false,
                'disabled' => !$canEditFull,
            ])
            ->add('typePrise', EntityType::class, [
                'class' => TypePrise::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un type de prise',
                'required' => false,
                'disabled' => !$canEditFull,
            ])
            ->add('typeVente', EntityType::class, [
                'class' => TypeVente::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un type de vente',
                'required' => false,
                'disabled' => !$canEditFull,
            ])
            ->add('nombreEleves', IntegerType::class, [
                'label' => "Nombre d'élèves",
                'required' => false,
                'disabled' => !$canEditFull,
            ])
            ->add('nombreClasses', IntegerType::class, [
                'label' => 'Nombre de classes',
                'required' => false,
                'disabled' => !$canEditFull,
            ])
            ->add('planchesIndividuel', EntityType::class, [
                'class' => Planche::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'disabled' => !$canEditFull,
                'query_builder' => function (PlancheRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->where('p.categorie = :type')
                        ->setParameter('type', Planche::CATEGORIE_INDIVIDUEL)
                        ->orderBy('p.nom', 'ASC');
                },
            ])
            ->add('planchesFratrie', EntityType::class, [
                'class' => Planche::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'disabled' => !$canEditFull,
                'query_builder' => function (PlancheRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->where('p.categorie = :type')
                        ->setParameter('type', Planche::CATEGORIE_FRATRIE)
                        ->orderBy('p.nom', 'ASC');
                },
            ])
            ->add('prixEcole', MoneyType::class, [
                'label' => "Prix facturé à l'école",
                'required' => false,
                'currency' => 'EUR',
                'disabled' => !$canEditFull,
            ])
            ->add('prixParent', MoneyType::class, [
                'label' => 'Prix pour les parents',
                'required' => false,
                'currency' => 'EUR',
                'disabled' => !$canEditFull,
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaires / Notes internes',
                'required' => false,
                'attr' => ['rows' => 5],
                // Le commentaire est toujours éditable, même pour les photographes
            ])
            ->add('frequence', TextType::class, [
                'required' => false,
                'disabled' => !$canEditFull,
            ])
            ->add('baseDeDonneeUtilisee', TextType::class, [
                'required' => false,
                'disabled' => !$canEditFull,
            ])
            ->add('jourDecharge', TextType::class, [
                'required' => false,
                'disabled' => !$canEditFull,
            ])
            ->add('endroitInstallation', TextType::class, [
                'required' => false,
                'disabled' => !$canEditFull,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PriseDeVue::class,
            'can_edit_full' => false,
        ]);
    }
}
