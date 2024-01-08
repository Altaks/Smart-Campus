<?php

namespace App\Controller;


use App\Entity\DemandeTravaux;
use App\Entity\SystemeAcquisition;
use App\Service\ReleveService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PlanExpController extends AbstractController
{

    // Routes du chargé de mission

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
    #[Route('/plan/{id_salle}/demander-installation', name: 'cdm_demander_installation')]
    public function cdm_demander_installation(ManagerRegistry $doctrine, int $id_salle)
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

    #[Route('/plan/ajouter_salle', name: 'cdm_ajouter_salle')]
    public function cdm_ajouter_salle(): Response
    {
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
        return $this->render('plan/charge_de_mission/ajouter_salle.html.twig', []);
    }

    #[Route('/plan/retirer_salle/{id}', name: 'cdm_retirer_salle')]
    public function cdm_retirer_salle(): Response
    {
        // full redirect
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
    }

    #[Route('/plan/modifier_salle/{id}', name: 'cdm_modifier_salle')]
    public function cdm_modifier_salle(): Response
    {
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
        return $this->render('plan/charge_de_mission/modifier_salle.html.twig', []);
    }

    #[Route('/plan/lister_salles', name: 'cdm_lister_salles')]
    public function cdm_lister_salles(ManagerRegistry $doctrine): Response
    {
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
        return $this->render('plan/charge_de_mission/lister_salles.html.twig', []);
    }

    #[Route('/plan/seuils_alertes', name: 'cdm_seuils_alertes')]
    public function cdm_seuils_alertes(ManagerRegistry $doctrine): Response
    {
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
        return $this->render('plan/charge_de_mission/seuils_alertes.html.twig', []);
    }

    #[Route('/plan/{id_salle}/demander-reparation', name: 'cdm_demander_reparation')]
    public function cdm_demander_reparation(ManagerRegistry $doctrine, int $id_salle)
    {
        // full redirect
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
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
        /*
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
        */

        $systemeAcquisition = $demandeTravaux->getSystemeAcquisition();
        $placeHolder = 'Aucun';

        $listeChoix = [];
        if ($systemeAcquisition != null){
            $listeChoix[] = $systemeAcquisition;
            $placeHolder = $systemeAcquisition->getNom();
        }

        $listeChoix[] = null;

        $listeSysAcquiNonInstall = $sysAcquiRepository->findBy(['etat' => 'Non installé']);

        foreach ($listeSysAcquiNonInstall as $sys){
            $listeChoix[] = $sys;
        }


        $form = $this->createFormBuilder()
            ->add('sa', ChoiceType::class, [
                'choices' => $listeChoix,
                'choice_label' => function(?SystemeAcquisition $listeChoix) {
                    return $listeChoix ? $listeChoix->getNom(): 'Aucun';
                },
                'choice_value' => function(?SystemeAcquisition $listeChoix) {
                    return $listeChoix ? $listeChoix->getId() : null;
                },
                'label' => "Système d'acquisition",
                'required' => true,
                'data' => $systemeAcquisition,

            ])
            ->getForm();



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

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if ($systemeAcquisition != null){
                $systemeAcquisition->setEtat("Non installé");
            }

            $id = $form->getData()['sa'];
            if ($id == null){
                $demandeTravaux->setSystemeAcquisition(null);
                $entityManager->flush();
                return $this->redirect('/plan/demande-travaux/' . $demandeTravaux->getId());
            }

            $sa = $sysAcquiRepository->findOneBy(['id' => $id]);
            $demandeTravaux->setSystemeAcquisition($sa);
            $sa->setEtat("Installation");
            $entityManager->flush();

            return $this->redirect('/plan/demande-travaux/' . $demandeTravaux->getId());
        }

        return $this->render('plan/technicien/demande_de_travaux.html.twig', [
            'listeReleves' => $dictReleves,
            'demandeTravaux' => $demandeTravaux,
            'form' => $form,
            'salle' => $salle
        ]);
    }

    #[Route('/plan/ajouter_sa', name: 'technicien_ajouter_sa')]
    public function technicien_ajouter_sa(ManagerRegistry $doctrine, Request $request): Response
    {
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
        return $this->render('plan/technicien/ajouter_sa.html.twig', []);
    }

    #[Route('/plan/retirer_sa/<id>', name: 'technicien_retirer_sa')]
    public function technicien_retirer_sa(ManagerRegistry $doctrine, Request $request): Response
    {
        // full redirect
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
    }

    #[Route('/plan/lister_sa', name: 'technicien_liste_sa')]
    public function technicien_liste_sa(ManagerRegistry $doctrine, releveService $service): Response
    {
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
        return $this->render('plan/technicien/liste_sa.html.twig', []);
    }

    #[Route('/plan/{id}/declarer-operationnel', name: 'technicien_declarer_operationnel')]
    public function technicien_declarer_operationnel(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
        // full redirect
    }
}
