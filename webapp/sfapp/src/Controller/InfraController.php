<?php

namespace App\Controller;



use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class InfraController extends AbstractController
{
    #[IsGranted("ROLE_ROLE_CHARGE_DE_MISSION")]
    #[Route('/infra/charge-de-mission/salle', 'app_infra_technicien')]
    public function infraTechnicien(ManagerRegistry $managerRegistry ):Response{

        $salleRepository = $managerRegistry->getRepository('Salle');
        $salles = $salleRepository->findAll();

        return $this->render('/infra/chargeDeMission/salle.html.twig', ['listeSalles'=> $salles]);
    }
}
