<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccueilController extends AbstractController
{
    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/accueil/charge-de-mission', name: 'accueil_charge_de_mission')]
    public function indexChargeDeMission(): Response
    {
        return $this->render('accueil/charge-de-mission.html.twig', []);
    }

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/accueil/tech', name: 'accueil_technicien')]
    public function indexTechnicien(): Response
    {
        return $this->render('accueil/technicien.html.twig', []);
    }
}