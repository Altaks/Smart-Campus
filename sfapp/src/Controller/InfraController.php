<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfraController extends AbstractController
{
    #[Route('/infra/dispositifs', name: 'infra_liste_dispositfs')]
    public function index(ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();
        $dispositifsRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');

        $listeDispositifs = $dispositifsRepository->findAll();

        return $this->render('infra/liste-dispositifs.html.twig', [
            'dispositifs' => $listeDispositifs
        ]);
    }
}
