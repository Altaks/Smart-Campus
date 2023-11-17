<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfraController extends AbstractController
{
    
    #[Route('/infra/systemes-acquisition', name: 'infra_liste_systemes-acquisition')]
    public function systemesAcquisition(ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();
        $dispositifsRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');

        $listeDispositifs = $dispositifsRepository->findAll();

        return $this->render('infra/liste-systemes-acquisition.html.twig', [
            'dispositifs' => $listeDispositifs
        ]);
    }

    #[Route('/infra/systemes-acquisition/modifier/{id}', name: 'infra-modifier-systemes-acquisition')]
    public function modifierSystemesAcquisition(ManagerRegistry $managerRegistry, int $id) : Response
    {
        $entityManager = $managerRegistry->getManager();

        $dispositifsRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');
        $batimentRepository = $entityManager->getRepository('App\Entity\Batiment');
        $sallesRepository = $entityManager->getRepository('App\Entity\Salle');

        $dispositif = $dispositifsRepository->find($id);

        $listeBatiments = $batimentRepository->findAll();
        $listeSalles = $sallesRepository->findAll();

        return $this->render('infra/modifier-sa.html.twig', [
            'id' => $id,
            'dispositf' => $dispositif,
            'listeBatiments' => $listeBatiments,
            'listeSalles' => $listeSalles
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
