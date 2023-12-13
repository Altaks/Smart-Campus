<?php

namespace App\Controller;

use App\Entity\Batiment;
use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\releveService;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class InfraController extends AbstractController
{
    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/infra/charge-de-mission/', name: 'infra_nav_charge_mission')]
    public function nav_charge_de_mission(): Response
    {
        return $this->render('infra/index.html.twig', []);
    }

    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/infra/charge-de-mission/batiments', name: 'app_infra_charge_de_mission_batiments')]
    public function charge_de_mission_batiments(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository('App\Entity\Batiment');
        $listeBatiments = $repository->findAll();
        return $this->render('infra/batiments.html.twig', [
            'listeBatiments' => $listeBatiments
        ]);
    }

    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/infra/charge-de-mission/salles/{id}/ajouter-sa', name: 'app_infra_ajouter_sa')]
    public function charge_de_mission_ajouter_sa(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        $salle = $doctrine->getManager()->getRepository('App\Entity\Salle')->findOneBy(['id' => $id]);

        if($salle == null){
            throw $this->createNotFoundException("La salle n'existe pas");
        } else if($salle->getSystemeAcquisition() != null){
            throw $this->createNotFoundException("La salle dispose déjà d'un système d'acquisition");
        }

        $systemesAcquisition = $doctrine->getManager()->getRepository('App\Entity\SystemeAcquisition')->findAll();

        $sa_non_utilises = [];

        foreach ($systemesAcquisition as $sa){
            if($sa->getSalle() == null){
                $sa_non_utilises[] = $sa;
            }
        }

        $form = $this->createFormBuilder()
            ->add('sa', EntityType::class, [
                'label' => "Sélectionner le système d'acquisition :",
                'class' => SystemeAcquisition::class,
                'choice_label' => 'tag',
                'choices' => $sa_non_utilises
            ])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            $sa_choisi = $data['sa'];
            $salle->setSystemeAcquisition($sa_choisi);

            $doctrine->getManager()->persist($salle);
            $doctrine->getManager()->flush();

            return $this->redirect('/infra/charge-de-mission/salles/');
        }

        return $this->render('infra/ajouter-sa.html.twig', [
            'salle_nom' => $salle->getNom(),
            'form' => $form
        ]);
    }
}
