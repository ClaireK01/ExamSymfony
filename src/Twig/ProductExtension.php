<?php

namespace App\Twig;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ProductExtension extends AbstractExtension
{

    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $em
    )
    { }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('get_non_active_products', [$this, 'get_non_active_products']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_non_active_products', [$this, 'get_non_active_products']),
        ];
    }

    public function get_non_active_products($user)
    {
        $productEntities = $this->productRepository->getAllNonActiveProductsByUser($user);
        $nbProduct= 0 ;
        foreach($productEntities as $product){
            $nbProduct++;
        }

        return $nbProduct;
    }
}
