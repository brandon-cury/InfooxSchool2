<?php

namespace App\Twig\Runtime;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Twig\Extension\RuntimeExtensionInterface;

class SlugExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function doSomething($string):string
    {
        return (new AsciiSlugger())->slug($string)->lower();
    }
}
