<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TechnicienController extends AbstractController
{
    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/accueil/tech', name: 'accueil_technicien')]
    public function index(): Response
    {
        return $this->render('accueil/technicien.html.twig', []);
    }
}
