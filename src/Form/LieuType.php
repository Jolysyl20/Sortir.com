<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_lieu', TextType::class, [
                'label' => 'Nom du lieu :',
                'required' => true,
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue :',
                'required' => true,
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude :',
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude :',
            ])
            ->add('villes_no_ville', EntityType::class, [
                'label' => 'Ville :',
                // looks for choices from this entity
                'class' => Ville::class,
                'choice_label' => 'nom_ville',
                // used to render a select box, check boxes or radios
                'multiple' => false,
                'expanded' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
