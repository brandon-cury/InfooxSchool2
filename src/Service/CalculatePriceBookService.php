<?php
namespace App\Service;

use App\Entity\Bord;
use App\Entity\User;
use App\Repository\UserBordRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CalculatePriceBookService
{
    public function __construct(
    ) {}

    public function calculate(int $book_price, ?string $time = null)
    {
        $price = null;
        if ($time == '1 an') {
            //$price = (ceil($book_price + ($book_price / 4)) > 100) ? ceil($book_price + ($book_price / 4)) : 100;
            $price = (ceil($book_price / 2) > 600) ? ceil($book_price / 2) : 600;
        } elseif ($time == '3 mois') {
            //return (ceil(($this->prix + ($this->prix/4))/4 + 200) > 100)?ceil(($this->prix + ($this->prix/4))/4 + 200). ' Fcfa':100 . ' Fcfa';
            $price = (ceil(($book_price + ($book_price / 4)) / 4 + 200) > 500) ? ceil(($book_price + ($book_price / 4)) / 4 + 200) : 500 ;
        } elseif ($time == '1 mois') {
            //return (ceil((($this->prix + ($this->prix/4))/4 + 200)/3 + 150) > 100)?ceil((($this->prix + ($this->prix/4))/4 + 200)/3 + 150). ' Fcfa':100 . ' Fcfa';
            $price = (ceil((($book_price + ($book_price / 4)) / 4 + 200) / 3 + 150) > 250) ? ceil((($book_price + ($book_price / 4)) / 4 + 200) / 3 + 150) : 250;
        } elseif ($time == '1 semaine') {
            $price = (ceil(((($book_price + ($book_price / 4)) / 4 + 200) / 3 + 150) / 4 + 100) > 150) ? ceil(((($book_price + ($book_price / 4)) / 4 + 200) / 3 + 150) / 4 + 100) : 150;
        }else{
            $price = (ceil((((($book_price + ($book_price / 4)) / 4 + 200) / 3 + 150) / 4 + 100) / 7 + 50) > 100) ? ceil((((($book_price + ($book_price / 4)) / 4 + 200) / 3 + 150) / 4 + 100) / 7 + 50)  : 100;
        }
        return $price;
    }
}