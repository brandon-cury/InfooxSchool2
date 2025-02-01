<?php

namespace App\Controller\Admin;

use App\Entity\Examen;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\File;

class ExamenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Examen::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            ImageField::new('image')
                ->setLabel('image')
                ->setBasePath('/examens')
                ->setUploadDir('public/examens')
                ->setUploadedFileNamePattern('[name]_[timestamp].[extension]')
                ->setFileConstraints(new File([
                    //'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF)', ])),
            NumberField::new('sort'),
        ];
    }

}
