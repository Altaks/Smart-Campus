<?php

namespace App\Controller;



use App\Entity\Batiment;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/infra/charge-de-mission/batiments/ajouter', name: 'app_infra_charge_de_mission_ajouter_batiments')]
    public function charge_de_mission_ajouter_batiment(Request $request, ManagerRegistry $doctrine): Response
    {
        $batiment = new Batiment();
        $form = $this->createFormBuilder($batiment)
            ->add('nom', TextType::class, [
                'label' => 'Nom du bâtiment',
                'attr' => array(
                    'placeholder' => 'Bâtiment A'
            )])
            ->getForm();

        if ($form->isSubmitted()) {
            $batiment = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($batiment);
            $entityManager->flush();

            return $this->redirectToRoute('app_infra_charge_de_mission_batiments');
        }

        return $this->render('infra/batimentAjouter.html.twig', [
            'form' => $form
        ]);
    }


    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/infra/charge-de-mission/salles', name: 'app_infra_charge_de_mission_salle')]
    public function charge_de_mission_salle(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository('App\Entity\Salle');
        $listeSalles = $repository->findAll();
        return $this->render('infra/salle.html.twig', [
            'listeSalles' => $listeSalles
        ]);
    }

}
