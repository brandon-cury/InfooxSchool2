<?php

namespace App\Twig\Runtime;

use App\Service\CalculatePriceBookService;
use Twig\Extension\RuntimeExtensionInterface;

class CalculatePriceBookExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private CalculatePriceBookService $calculatePriceBookService)
    {

    }

    public function doSomething(int $price, ?string $time = null)
    {
        return $this->calculatePriceBookService->calculate($price, $time);
    }
}
