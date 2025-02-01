<?php

namespace App\Controller\Admin;

use App\Entity\Bord;
use App\Entity\UserBord;
use App\Repository\BordRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserBordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserBord::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('bord')
                ->setLabel('Book')
                ->setFormTypeOptions([
                    'by_reference' => false,
                    'query_builder' => function (BordRepository $er) {
                        return $er->createQueryBuilder('b')
                            ->where('b.is_published = :is_published')
                            ->setParameter('is_published', true);
                    },
                ])
                ->setRequired(true),
            AssociationField::new('user')
                ->setLabel('Utilisateur')
                ->setFormTypeOptions([
                    'by_reference' => false,
                    'query_builder' => function (UserRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.isVisible = :visible')
                            ->setParameter('visible', true);
                    },
                ])
                ->setRequired(true),
            DateTimeField::new('recorded_at')
                ->setLabel('Enregistré le')
                ->onlyOnIndex(),
            DateTimeField::new('end_at'),
            BooleanField::new('visible'),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $userBord = new UserBord();
        $userBord->setVisible(true)
            ->setRecordedAt(new \DateTimeImmutable());

        return $userBord;
    }

}
