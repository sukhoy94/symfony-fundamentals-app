<?php

namespace App\Controller;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController extends AbstractController
{
    #[Route('/health/check', name: 'app_health_check')]
    public function index(ManagerRegistry $doctrine): Response
    {       
        return $this->render('health_check/index.html.twig', [
            'controller_name' => 'HealthCheckController',
            'db' => [
                'status' => $this->isDbConnected($doctrine) ? 'OK' : 'Not OK',
            ],
        ]);
    }   
    
    private function isDbConnected(ManagerRegistry $doctrine): bool
    {
        try {
            $em = $doctrine->getManager();
            $em->getConnection()->connect();
            $connected = $em->getConnection()->isConnected();
        } catch (ConnectionException $exception) {
            return false;
        }
        
        return $connected;       
    }   
}
