<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLabel;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :',
                'required' => true,
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'widget' => 'single_text',
            ])
            ->add('dateCloture', DateType::class, [
                'label' => 'Date limite d\'inscription :',
                'widget' => 'single_text',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée :',
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => 'Nombre de places :',
            ])
            ->add('descriptionInfos', TextareaType::class, [
                'label' => 'Description et infos :',
            ])
            ->add('siteSortie', EntityType::class, [
                'label' => 'Site organisateur :',
                'class' => Site::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('noLieu', EntityType::class, [
                'label' => 'Lieu :',
                'class' => Lieu::class,
                'placeholder' => 'Sélectionner un lieu',
                'choice_label' => 'nom_lieu',
                'multiple' => false,
                'expanded' => false,
                // 'disabled' => true,
                'attr' => ['data-lieu' => 'datalieu'],
                // affiche par ordre alphabétique dans la liste
                'query_builder' => function(LieuRepository $lieuRepo) {
                    return $lieuRepo->createQueryBuilder('c')->orderBy('c.nom_lieu', 'ASC');
                }
            ])
            //champs non enregistré en base voir pour afficher les lieux...
            ->add('noVille',EntityType::class, [
                'label' => 'Ville :',
                'class' => Ville::class,
                'placeholder' => 'Sélectionner une ville',
                'choice_label' => 'nom_ville',
                'mapped' => true,
                'required' => false,
                'query_builder' => fn(VilleRepository $villeRepo) => $villeRepo->createQueryBuilder('c')->orderBy('c.nom_ville', 'ASC'),
                'attr' => ['data-ville' => 'dataville'],
            ])
        ;

            // $builder->get('noLieu')->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            //     $noVille = $event->getData();
            //     $form = $event->getForm();
            //     if ($noVille) {


            //         $form->add('noLieu', EntityType::class, [
            //                 'label' => 'Lieu :',
            //                 'class' => Lieu::class,
            //                 'placeholder' => 'Sélectionner un lieu',
            //                 'choices' => $form->getData()->getLieus(),
            //                 'multiple' => false,
            //                 'expanded' => false,
            //         ]);
            //     }

            // });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
