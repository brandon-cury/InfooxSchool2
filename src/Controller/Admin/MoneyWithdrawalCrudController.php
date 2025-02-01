<?php

namespace App\Controller\Admin;

use App\Entity\MoneyWithdrawal;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class MoneyWithdrawalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MoneyWithdrawal::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user')
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
                )->setLabel('Utilisateurs'),
            NumberField::new('total')
                ->setLabel('Montant (Fcfa)'),
            DateTimeField::new('created_at')
                ->onlyOnIndex(),
        ];
    }
    public function createEntity(string $entityFqcn)
    {
        $money = new MoneyWithdrawal();
        $money->setCreatedAt(new \DateTimeImmutable());
        return $money;
    }
}
