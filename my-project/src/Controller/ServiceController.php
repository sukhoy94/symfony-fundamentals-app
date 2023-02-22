<?php

namespace App\Controller;

use App\Service\HelloWorldService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function index(HelloWorldService $helloWorld): Response
    {
        $helloWorld->setName('Andrii');
        $helloWorld->sayHello();
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
}
