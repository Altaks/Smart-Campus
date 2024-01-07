<?php

namespace App\Controller;

use App\Entity\DemandeTravaux;
use App\Entity\Salle;
use App\Service\ReleveService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PublicController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion')]
    public function connexion(AuthenticationUtils $authenticationUtils, Security $security, Request $request, ManagerRegistry $registry): Response
    {
        $erreur = $authenticationUtils->getLastAuthenticationError();

        $dernierIdentifiant = $authenticationUtils->getLastUsername();

        return $this->render('public/connexion.html.twig', [
            'dernier_identifiant' => $dernierIdentifiant,
            'erreur' => $erreur,
        ]);
    }

    #[Route('/accueil/', name: 'accueil')]
    public function indexTechnicien(ManagerRegistry $registry): Response
    {
        if ($this->isGranted("ROLE_TECHNICIEN")){

            $demandesInstall = $registry->getRepository('App\Entity\DemandeTravaux')
                ->findBy(["type"=>"Installation",
                    "terminee"=>false]);

            return $this->render('public/accueil.twig', [
                "demandesInstall" => $demandesInstall
            ]);
        }
        return $this->render('public/accueil.html.twig');

        //l'usager et le chargé de mission n'ont pas de données en plus sur leurs pages
    }

    #[Route('/releves', name: 'app_releves')]
    public function releves(ManagerRegistry $managerRegistry, Request $request): Response
    {
        $sallesRepository = $managerRegistry->getRepository('App\Entity\Salle');

        // Récupération de toutes les salles ayant des relevés avec du DQL
        $salles = $sallesRepository->findAll();

        $form = $this->createFormBuilder()->getForm();
        // TODO: sélectionner seulement les salles avec un SA opérationnel


        if($form->isSubmitted() && $form->isValid()) {

            return $this->render('public/releves.html.twig', [
                // data
            ]);
        }

        return $this->render('releves/index.html.twig', [
            'salles' => $salles,
            'form' => $form->createView(),
        ]);
    }
}