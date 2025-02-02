<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\CalculatePriceBookExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CalculatePriceBookExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [CalculatePriceBookExtensionRuntime::class, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('calculate_price_book', [CalculatePriceBookExtensionRuntime::class, 'doSomething']),
        ];
    }
}
