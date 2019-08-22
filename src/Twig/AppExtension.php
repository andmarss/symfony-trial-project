<?php


namespace App\Twig;

use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigTest;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var string
     */
    private $locale;

    /**
     * AppExtension constructor.
     * @param string $locale
     */
    public function __construct(string $locale)
    {

        $this->locale = $locale;
    }

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

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals(): array
    {
        return [
            'locale' => $this->locale
        ];
    }

    public function getTests()
    {
       return [
           new TwigTest('like', function ($obj){
               return $obj instanceof LikeNotification;
           })
       ];
    }
}