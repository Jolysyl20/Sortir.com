<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use App\Form\LieuType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LieuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lieu::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('nom_lieu'),
            TextField::new('rue'),
            IntegerField::new('latitude'),
            IntegerField::new('longitude'),
            AssociationField::new('villes_no_ville')
                -> setLabel('Ville'),
        ];
    }

}
