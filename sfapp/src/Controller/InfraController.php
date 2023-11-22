<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfraController extends AbstractController
{
    
    #[Route('/infra/systemes-acquisition', name: 'infra_liste_systeme-acquisition')]
    public function systemesAcquisition(ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();
        $dispositifsRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');

        $listeDispositifs = $dispositifsRepository->findAll();

        return $this->render('infra/liste-dispositifs.html.twig', [
            'dispositifs' => $listeDispositifs
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
