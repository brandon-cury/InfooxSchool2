<?php
namespace App\Service;

use App\Entity\Bord;
use App\Entity\User;
use App\Repository\UserBordRepository;
use Symfony\Bundle\SecurityBundle\Security;

class BookInBasketService
{
    public function __construct(
        private UserBordRepository $repository,
        private Security $security
    ) {}

    public function basketAndActive(Bord $book):bool
    {
        $user = $this->security->getUser();
        if($user){
            $user_bord = $this->repository->findOneBy(
                [
                    'user' => $user,
                    'bord' => $book,
                    'is_visible' => true
                ]
            );
            if($user_bord){
                return true;
            }
        }
        return false;
    }
}