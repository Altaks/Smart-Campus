<?php

namespace App\Controller;


use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use App\Service\ReleveService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PlanExpController extends AbstractController
{

    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/infra/charge-de-mission/', name: 'infra_nav_charge_mission')]
    public function nav_charge_de_mission(): Response
    {
        return $this->render('infra/index.html.twig', []);
    }

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/infra/technicien/', name: 'infra_nav_technicien')]
    public function nav_technicien(): Response
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

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/infra/technicien/batiments', name: 'app_infra_technicien_batiments')]
    public function technicien_batiments(ManagerRegistry $doctrine): Response
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

        $form->handleRequest($request);

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

    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/infra/charge-de-mission/systemes-acquisition', name: 'app_infra_charge_de_mission_systeme_acquisition')]
    public function charge_de_mission_systemes_acquisition(ManagerRegistry $doctrine, releveService $service): Response
    {
        $listeSA = $doctrine->getManager()->getRepository('App\Entity\SystemeAcquisition')->findAll();

        $listeSAConnecte = array();
        $listeSANonConnectes = array();

        foreach($listeSA as $SA)
        {
            $dernierReleve = $service->getDernier($SA->getTag());

            date_default_timezone_set('Europe/Paris');
            $dateCourante = new \DateTime(date('Y-m-d H:i:s', time()-6*60));
            $dateReleve = new \DateTime($dernierReleve["date"]);

            if($dateCourante->diff($dateReleve)->invert == 1) {
                $listeSANonConnectes[] = $SA;
            }
            else
            {
                $listeSAConnecte[] = $SA;
            }
        }

        return $this->render('infra/systemes-acquisition.html.twig', [
            'listeSAFonctionnels' => $listeSAConnecte,
            'listeSANonConnectes' => $listeSANonConnectes
        ]);
    }

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/infra/technicien/systemes-acquisition', name: 'app_infra_technicien_systeme_acquisition')]
    public function technicien_systemes_acquisition(ManagerRegistry $doctrine, releveService $service): Response
    {
        $listeSA = $doctrine->getManager()->getRepository('App\Entity\SystemeAcquisition')->findAll();

        $listeSAFonctionnels = array();
        $listeSANonFonctionnels = array();
        $listeSANonConnectes = array();

        foreach($listeSA as $SA)
        {
            $dernierReleve = $service->getDernier($SA->getTag());

            date_default_timezone_set('Europe/Paris');
            $dateCourante = new \DateTime(date('Y-m-d H:i:s', time()-6*60));

            if($dernierReleve["date"] != null and
               $dernierReleve["co2"]  != null and
               $dernierReleve["temp"] != null and
               $dernierReleve["hum"]  != null    )
            {
                date_default_timezone_set('Europe/Paris');
                $dateCourante = new \DateTime(date('Y-m-d H:i:s', time()-6*60));
                $dateReleve = new \DateTime($dernierReleve["date"]);
                
                if($dateCourante->diff($dateReleve)->invert == 0)
                {
                    $listeSAFonctionnels[] = $SA;
                }
                else
                {
                    $listeSANonConnectes[] = $SA;
                }
            }
            else
            {
                date_default_timezone_set('Europe/Paris');
                $dateCourante = new \DateTime(date('Y-m-d H:i:s', time()-6*60));
                $dateReleve = new \DateTime($dernierReleve["date"]);

                if($dateCourante->diff($dateReleve)->invert == 1) {
                    $listeSANonConnectes[] = $SA;
                }
                else
                {
                    $listeSANonFonctionnels[] = $SA;
                }

            }
        }

        return $this->render('infra/systemes-acquisition.html.twig', [
            'listeSAFonctionnels' => $listeSAFonctionnels,
            'listeSANonConnectes' => $listeSANonConnectes,
            'listeSANonFonctionnels' => $listeSANonFonctionnels
        ]);
    }

    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/infra/charge-de-mission/salles', name: 'app_infra_charge_de_mission_salles')]
    public function charge_de_mission_salle(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository('App\Entity\Salle');
        $listeSalles = $repository->findAll();
        return $this->render('infra/salle.html.twig', [
            'listeSalles' => $listeSalles
        ]);
    }


    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/infra/technicien/salles', name: 'app_infra_technicien_salles')]
    public function technicien_salle(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository('App\Entity\Salle');
        $listeSalles = $repository->findAll();
        return $this->render('infra/salle.html.twig', [
            'listeSalles' => $listeSalles
        ]);
    }

    /*
     * Charge de mission : Consulter les infos des salles du plan d'expérimentation
     */
    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/plan', name: 'cpm_plan')]
    public function cdm_plan(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $sallesRepository = $entityManager->getRepository('App\Entity\Salle');
        $listeSalles = $sallesRepository->findAll();

        $salleArr = Array();
        $etatArr = Array();

        for($i = 0; $i < count($listeSalles); $i++) {
            $etat = "-";
            for($j = 0; $j < count($listeSalles[$i]->getDemandesTravaux()); $j++) {
                if(!$listeSalles[$i]->getDemandesTravaux()[$j]->isTerminee()) {
                    $etat = "Installation demandée";
                }
                if($etat == "-") {
                    $etat = "Opérationnel";
                }
            }
            $salleArr[$listeSalles[$i]->getId()] = $listeSalles[$i];
            $etatArr[$listeSalles[$i]->getId()] = $etat;

        }

        return $this->render('cdm/plan.html.twig', [
            'salle' => $salleArr,
            'etat' => $etatArr
        ]);
    }

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/plan/demande-travaux/{id}', name: 'app_demande_travaux')]
    public function demande_travaux(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $demandeTravauxRepository = $entityManager->getRepository('App\Entity\DemandeTravaux');
        $demandeTravaux = $demandeTravauxRepository->find($id);
        $systemeAcquisition = $demandeTravaux->getSystemeAcquisition();

        $listeReleves = null;

        if($systemeAcquisition != null)
        {
            date_default_timezone_set('Europe/Paris');
            $dateDemain = new \DateTime(date('Y-m-d H:i:s', time() + 24 * 60 * 60 ));
            $dateHier = new \DateTime(date('Y-m-d H:i:s', time() - 24 * 60 * 60 ));
            $service = new ReleveService();

            $dictReleves = $service->getEntre($systemeAcquisition, $dateHier, $dateDemain);
            $listeDatesReleves = array_keys($dictReleves);

            $dateMoisDeUneHeure = new \DateTime(date('Y-m-d H:i:s', time() - 1 * 60 * 60));
            foreach ($listeDatesReleves as $dateReleve) {
                if ($dateMoisDeUneHeure->diff(new \DateTime($dateReleve))->invert == 1)
                    unset($dictReleves[$dateReleve]);
            }
            krsort($dictReleves);
            
        }

        return $this->render('demande-travaux.html.twig', [
            'listeReleves' => $dictReleves,
            'salle' => $demandeTravaux->getSalle(),
        ]);
    }
}
