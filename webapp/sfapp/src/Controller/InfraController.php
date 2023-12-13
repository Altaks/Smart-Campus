<?php

namespace App\Controller;



use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/infra/technicien/systemes-acquisition', name: 'app_infra_technicien_systeme_acquisition')]
    public function technicien_systemes_acquisition(ManagerRegistry $doctrine, releveService $service): Response
    {
        $listeSA = $doctrine->getManager()->getRepository('App\Entity\SystemeAcquisition')->findAll();

        $listeSAFonctionnels = array();
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

            }
        }

        return $this->render('infra/systemes-acquisition.html.twig', [
            'listeSAFonctionnels' => $listeSAFonctionnels,
            'listeSANonConnectes' => $listeSANonConnectes,
        ]);
    }
}
