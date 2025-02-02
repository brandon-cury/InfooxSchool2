<?php

namespace App\Twig\Runtime;

use App\Entity\Bord;
use App\Service\AutorisationBookService;
use Twig\Extension\RuntimeExtensionInterface;

class AutorisationBookExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private AutorisationBookService $autorisationBookService
    ) {}

    public function doSomething(Bord $bord)
    {
        return $this->autorisationBookService->autorisationBord($bord);
    }
}
