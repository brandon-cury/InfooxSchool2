<?php

namespace App\Controller\Admin;

use App\Entity\Classe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClasseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Classe::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            AssociationField::new('examen'),
            NumberField::new('all_user')
                    ->onlyOnIndex(),
            NumberField::new('sort'),
            AssociationField::new('filiere'),
            AssociationField::new('matieres', 'Matieres')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $classe = new Classe();

        $classe->setAllUser(0);

        return $classe;
    }

}
