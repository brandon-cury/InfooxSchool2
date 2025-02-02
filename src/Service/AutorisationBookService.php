<?php
namespace App\Service;

use App\Entity\Bord;
use App\Entity\User;
use App\Repository\UserBordRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AutorisationBookService
{
    public function __construct(
        private UserBordRepository $repository,
        private Security $security
    ) {}

    public function autorisationBord(Bord $book):bool
    {
        $user = $this->security->getUser();
        if($book->getPrice()){
            if($user){
                $user_bord = $this->repository->findOneBy(
                    [
                        'user' => $user,
                        'bord' => $book
                    ]
                );
                if($user_bord){
                    $now = new \DateTimeImmutable();
                    if($user_bord->getEndAt() > $now){
                        return true;
                    }
                }
            }
        }else{
            return true;
        }
        return false;
    }
}