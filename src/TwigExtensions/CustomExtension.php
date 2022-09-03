<?php

namespace App\TwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CustomExtension extends AbstractExtension
{

    public function getFilters()
    {
        // retour le nom du filtre, et en tableau , la class (donc celle-ci $this) et le nom de la fonction
        return [
            new TwigFilter('defaultImage', [$this, 'defautImg'])
        ];
    }

    // prend en parametre la variable qui va être  pipé
    public function defautImg(string $path): string
    {
        if (strlen(trim($path)) == 0) {
            return 'stylo.png';
        }
        return $path;
    }
}
