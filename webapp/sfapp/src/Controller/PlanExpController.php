<?php

namespace App\Controller;


use App\Entity\DemandeTravaux;
use App\Service\ReleveService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PlanExpController extends AbstractController
{

    /*
     * Charge de mission : Consulter les infos des salles du plan d'expérimentation
     */
    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/plan', name: 'cdm_plan')]
    public function cdm_plan(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $sallesRepository = $entityManager->getRepository('App\Entity\Salle');
        $listeSalles = $sallesRepository->findAll();

        $salleArr = Array();
        $etatArr = Array();

        for($i = 0; $i < count($listeSalles); $i++) {
            $etat = "Non installé";
            for($j = 0; $j < count($listeSalles[$i]->getDemandesTravaux()); $j++) {
                if(!$listeSalles[$i]->getDemandesTravaux()[$j]->isTerminee()) {
                    $etat = "Installation demandée";
                    break;
                }
            }
            if($etat == "Non installé") {
                if($listeSalles[$i]->getSystemeAcquisition() != null) {
                    $etat = "Opérationnel";
                } else {
                    $etat = "Non installé";
                }
            }
            $salleArr[$listeSalles[$i]->getId()] = $listeSalles[$i];
            $etatArr[$listeSalles[$i]->getId()] = $etat;

        }

        return $this->render('plan/charge_de_mission/liste_salles.html.twig', [
            'salle' => $salleArr,
            'etat' => $etatArr
        ]);
    }


    // US : Chargé de mission : Demander l'installation d'un SA à une salle
    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/plan/{id_salle}/demander-installation', name: 'cdm_demander_install')]
    public function cdm_demander_install(ManagerRegistry $doctrine, int $id_salle)
    {

        $entityManager = $doctrine->getManager();

        $salleRepository = $entityManager->getRepository('App\Entity\Salle');
        $salle = $salleRepository->findOneBy(['id' => $id_salle]);

        if ($salle == null) {
            throw $this->createNotFoundException("La salle n'existe pas");
        } else if ($salle->getSystemeAcquisition() != null) {
            throw $this->createNotFoundException("La salle dispose déjà d'un système d'acquisition");
        }

        $demandeTravaux = $entityManager->getRepository('App\Entity\DemandeTravaux');
        $demandeTravauxSalleNonTerminee = $demandeTravaux->findOneBy(['salle' => $salle->getId(), 'type' => 'Installation', 'terminee' => false]);

        if($demandeTravauxSalleNonTerminee != null){
            throw $this->createNotFoundException("La salle dispose déjà d'une demande d'installation en cours");
        }

        $demandeTravaux = new DemandeTravaux();
        $demandeTravaux->setSalle($salle);
        $demandeTravaux->setTerminee(false);
        $demandeTravaux->setDate(new \DateTime());
        $demandeTravaux->setType("Installation");

        $entityManager->persist($demandeTravaux);
        $entityManager->flush();

        return $this->redirectToRoute("cdm_plan");
    }



    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/plan/demande-travaux/{id}', name: 'demande_travaux')]
    public function demande_travaux(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $travauxRepository = $entityManager->getRepository('App\Entity\DemandeTravaux');
        $demandeTravaux = $travauxRepository->findOneBy(['id' => $id]);

        $salleRepository = $entityManager->getRepository('App\Entity\Salle');
        $salle = $salleRepository->findOneBy(['id' => $demandeTravaux->getSalle()->getId()]);

        $sysAcquiRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');

        $dictReleves = null;

        // TODO: implémenter un formulaire symfony plutôt qu'un formulaire html

        if($request->getMethod() == "POST"){
            $value = $_POST["sa"];
            if($value == "aucun"){
                $sa = $demandeTravaux->getSystemeAcquisition();
                if ($sa != null){
                    $sa->setEtat('Non installé');
                    $demandeTravaux->setSystemeAcquisition(null);
                    $entityManager->flush();
                }
            }
            else{
                $a_sa = $demandeTravaux->getSystemeAcquisition();
                $n_sa = $sysAcquiRepository->findOneBy(["id" => $value]);
                if ($a_sa != $n_sa){
                    if ($a_sa != null){
                        $a_sa->setEtat('Non installé');
                    }
                    $demandeTravaux->setSystemeAcquisition($n_sa);
                    $n_sa->setEtat('Installation');
                    $entityManager->flush();
                }
            }
        }

        $listeSysAcquiNonInstall = $sysAcquiRepository->findBy(['etat' => 'Non installé']);
        $systemeAcquisition = $demandeTravaux->getSystemeAcquisition();
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
            if (empty($dictReleves)){
                $dictReleves = null;
            }
            else{
                krsort($dictReleves);
            }

            
        }

        return $this->render('plan/technicien/demande_de_travaux.html.twig', [
            'listeReleves' => $dictReleves,
            'demandeTravaux' => $demandeTravaux,
            'listeSysAcqui' => $listeSysAcquiNonInstall,
            'salle' => $salle
        ]);
    }
}
