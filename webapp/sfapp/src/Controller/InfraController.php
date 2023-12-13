<?php

namespace App\Controller;

use App\Entity\Batiment;
use App\Entity\Salle;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\releveService;

class InfraController extends AbstractController
{
use Symfony\Component\Security\Http\Attribute\IsGranted;

class InfraController extends AbstractController
{
    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/infra/charge-de-mission/', name: 'infra_nav_charge_mission')]
    public function nav_charge_de_mission(): Response
    {
        return $this->render('infra/chargeDeMission/index.html.twig', []);
    }

}
