<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{   
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $em,
        private PaginatorInterface $paginator
    ){}


    #[Route('/', name: 'app_product')]
    public function index(Request $request): Response
    {
        $productEntities = $this->productRepository->getAllActiveProducts();
        $qb = $this->productRepository->queryBuilder();
        // dd($productEntities);
        
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('product/index.html.twig', [
            'pagination'=>$pagination,
            // 'products'=>$productEntities
        ]);
    }

    #[Route('/product/add', name: 'app_product_add')]
    public function add( Request $request): Response
    {
        $user = $this->getUser();
        $product = new Product;
        $form= $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){
            $product->setCreatedAt(new DateTime());
            $product->setCreatedBy($user);
            $product->setIsActive(true);
            $this->em->persist($product);
            $this->em->flush();
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/add.html.twig', [
            'form'=>$form->createView(),
            'product'=>$product
        ]);
    }


    #[Route('/product/edit/{id}', name: 'app_product_edit')]
    public function edit(Product $product, Request $request): Response
    {
        /** @var User */
        $user = $this->getUser();

        $form= $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($product);
            $this->em->flush();
            return $this->redirectToRoute('app_user_products', ['id'=>$user->getId()]);
        }

        return $this->render('product/add.html.twig', [
            'form'=>$form->createView(),
            'product'=>$product
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete')]
    public function delete(Product $product, Request $request): Response
    {
        /** @var User */
        $user = $this->getUser();
        if($product->getCreatedBy() == $user){
            $this->em->remove($product);
            $this->em->flush();
            return $this->redirectToRoute('app_user_products', ['id'=>$user->getId()]);
        }else{
            return $this->redirectToRoute('app_user_products', ['id'=>$user->getId()]);
        }
        

    }

    #[Route('/product/{id}', name: 'app_product_active')]
    public function show(Product $product): Response
    {
        $product->setIsActive(false);
        $this->em->persist($product);
        $this->em->flush();
        return $this->redirectToRoute('app_product');
    }




}
