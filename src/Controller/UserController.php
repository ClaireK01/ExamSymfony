<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $user,
        private ProductRepository $productRepository
    ){}

    #[Route('/user/products/', name: 'app_user_products')]
    public function index(): Response
    {
        $user = $this->getUser();
        $products = $this->productRepository->getProductByUser($user);

        return $this->render('user/user_product.html.twig', [
            'products'=>$products,
            'user'=>$user
        ]);
    }
}
