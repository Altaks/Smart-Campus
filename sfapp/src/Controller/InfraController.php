<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfraController extends AbstractController
{
    #[Route('/infra', name: 'app_infra')]
    public function index(): Response
    {
        return $this->render('infra/index.html.twig', [
            'controller_name' => 'InfraController',
        ]);
    }

    #[Route('/infra/batiments-et-salles', name: 'app_infra_batiments-et-salles')]
    public function batimentsEtSalles(ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();

        $repository = $entityManager->getRepository('App\Entity\Salle');

        $listeSalles = $repository->findAll();

        return $this->render('infra/batiments-et-salles.html.twig', [
            'listeSalles' => $listeSalles
        ]);
    }
}
