<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
    
    #[Route('/product/create', name: 'app_create_product')]
    public function createProduct(ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        $entityManager = $doctrine->getManager();
        
        $product = new Product();
        $product->setTitle('Keyboard');
        $product->setPrice(1999);
        $product->setDescription('Ergonomic and stylish!');
        
        $errors = $validator->validate($product);
        
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);
        
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        
        return new Response('Saved new product with id ' . $product->getId());
    }
    
    #[Route('/product/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProductRepository $productRepository)
    {
        $product = $productRepository
            ->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }
        
        return new Response('Check out this great product: ' . $product->getTitle());
    }
    
    #[Route('/product/evr/{id}', name: 'app_product_entity_valuer_resolver_show')]
    public function showWithEntityValueResolver(Product $product)
    {
        return new Response('Check out this great product: ' . $product->getTitle());
    }
    
    #[Route('/product/edit/{id}')]
    public function update(ManagerRegistry $doctrine, int $id)
    {
        $entityManager = $doctrine->getManager();
        
        /**@var Product $product*/
        $product = $entityManager->getRepository(Product::class)->find($id);
    
        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
    
        $product->setTitle('New product name!');
        $entityManager->flush();
    
        return $this->redirectToRoute('app_product_show', [
            'id' => $product->getId()
        ]);
    }
    
    #[Route('/product/test')]
    public function productTestRoute(ManagerRegistry $doctrine)
    {
        $minPrice = 2000;
        $products = $doctrine->getRepository(Product::class)->findAllGreaterThanPrice($minPrice);
        dd($products);
    }
}
