<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
            ->add('pseudo', null,[
                'empty_data'    =>  ''
            ])
            ->add ('nom', null,[
                'empty_data'    =>  ''
            ])
            ->add ('prenom', null,[
                'empty_data'    =>  ''
            ])
            ->add ('telephone', null,[
                'empty_data'    =>  ''
            ])
            ->add ('mail', null,[
                'empty_data'    =>  ''
            ])
            ->add ('actif', null,[
                'empty_data'    =>  true
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur'       => 'ROLE_USER',
                    'Editeur'           => 'ROLE_EDITOR',
                    'Administrateur'    => 'ROLE_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles' 
            ])
            ->add('imageFile',VichImageType::class,[
                'empty_data'    =>  null
            ])
            
            ->add('site_participant',EntityType::class, [
                'label' => 'site :',
                'class' => Site::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => false,
                ])

                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options'  => [
                        'label' => 'Password'
                    ],
                    'second_options' => [
                        'label' => 'Repeat Password',
                    ]
                ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
