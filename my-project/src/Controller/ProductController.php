<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $product->setTitle('Iphone8');
        $product->setPrice(2000);
        $product->setDescription('Ergonomic and stylish!');
        
        $category = new Category();
        $category->setName('Phones');
        
        $product->setCategory($category);
        
        $errors = $validator->validate($product);
        
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        
        $entityManager->persist($category);
        $entityManager->persist($product);
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
    
    #[Route('/product/form')]
    public function productForm(Request $request, ManagerRegistry $doctrine)
    {
        $product = new Product();
        $product->setTitle('New title');
        $product->setDescription('Description');
        $product->setPrice(200);
        
        $form = $this->createForm(ProductType::class, $product);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $product = $form->getData();
            $entityManager->persist($product);
            $entityManager->flush($product);
            
            return $this->redirectToRoute('product_successfully_saved_from_form');
        }
    
        return $this->render('product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/product/form/saved', name: 'product_successfully_saved_from_form')]
    public function productSuccessfullySavedFromForm(): Response
    {
        return new Response('Product was saved');
    }
}
