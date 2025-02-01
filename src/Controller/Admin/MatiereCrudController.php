<?php

namespace App\Controller\Admin;

use App\Entity\Matiere;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MatiereCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Matiere::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            NumberField::new('all_user')
                    ->onlyOnIndex(),
            NumberField::new('sort'),
            AssociationField::new('classe', 'Classes')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),
        ];
    }
    public function createEntity(string $entityFqcn)
    {
        $matiere = new Matiere();
        $matiere->setAllUser(0);
        return $matiere;
    }
}
