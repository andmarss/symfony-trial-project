<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
       return [
           new TwigFilter('price', [$this, 'priceFilter'])
       ];
    }

    /**
     * @param int $number
     * @return string
     */
    public function priceFilter(int $number): string
    {
       return '$' . number_format($number, 2, '.', ' ');
    }
}