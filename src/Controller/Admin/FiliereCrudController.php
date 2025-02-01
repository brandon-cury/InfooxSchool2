<?php

namespace App\Controller\Admin;

use App\Entity\Filiere;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\File;

class FiliereCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Filiere::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('image')
                ->setLabel('image(640x186)')
                ->setBasePath('/filieres')
                ->setUploadDir('public/filieres')
                ->setUploadedFileNamePattern('[name]_[timestamp].[extension]')
                ->setFileConstraints(new File([
                    //'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF)', ])),
            TextField::new('title'),
            NumberField::new('all_user')->onlyOnIndex(),
            DateTimeField::new('created_at')->onlyOnIndex(),
            NumberField::new('sort'),
            AssociationField::new('section', 'Sections')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),
            AssociationField::new('classes', 'Classes')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),


        ];
    }
    public function createEntity(string $entityFqcn)
    {
        $filiere = new Filiere();

        $filiere->setCreatedAt(new \DateTimeImmutable())
                ->setAllUser(0);

        return $filiere;
    }

}
