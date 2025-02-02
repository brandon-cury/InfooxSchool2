<?php

namespace App\Twig\Runtime;

use App\Entity\Bord;
use App\Service\BookInBasketService;
use Twig\Extension\RuntimeExtensionInterface;

class BookInBasketExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private BookInBasketService $bookInBasketService)
    {
        // Inject dependencies if needed
    }

    public function doSomething(Bord $bord)
    {
        return $this->bookInBasketService->basketAndActive($bord);
    }
}
