<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use App\Form\RoleType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CurrencyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ParticipantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Participant::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            BooleanField::new('actif'),

            IdField::new('id')
                -> onlyOnDetail(),

            //TextField::new(VichFileType::class)
            //    -> onlyWhenCreating(),

            ImageField::new('imageFile')
                -> setBasePath('/images/products/')
                -> onlyOnDetail(),

            ChoiceField::new('roles')
                ->  setLabel("Rôle")
                ->  setChoices([
                    'Administrateur'    =>  'ROLE_ADMIN',
                    'Editeur'           =>  'ROLE_EDITOR',
                    'Participant'       =>  'ROLE_USER'
                ])
                    ->  allowMultipleChoices(false)
                    ->  renderExpanded(true)
                    ->  setFormType(RoleType::class),

            TextField::new('pseudo'),

            TextField::new('nom'),

            TextField::new('prenom'),

            TextField::new('telephone'),

            EmailField::new('mail'),


        ];
    }

}
