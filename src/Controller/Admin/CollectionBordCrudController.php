<?php

namespace App\Controller\Admin;

use App\Entity\CollectionBord;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CollectionBordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CollectionBord::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('editor')
                ->setFormTypeOption(
                    'query_builder',
                    function (UserRepository $userRepository) {
                        return $userRepository->createQueryBuilder('u')
                            ->where('u.roles LIKE :admin')
                            ->orWhere('u.roles LIKE :super_admin')
                            ->orWhere('u.roles LIKE :editor')
                            ->setParameter('admin', '%ROLE_ADMIN%')
                            ->setParameter('super_admin', '%ROLE_SUPER_ADMIN%')
                            ->setParameter('editor', '%ROLE_EDITOR%');
                    }
                )->setLabel('Éditeur'),
            TextField::new('title'),
        ];
    }
}
