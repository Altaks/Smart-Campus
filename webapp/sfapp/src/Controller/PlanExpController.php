<?php

namespace App\Controller;


use App\Entity\DemandeTravaux;
use App\Entity\Salle;
use App\Entity\Seuil;
use App\Entity\SystemeAcquisition;
use App\Service\ReleveService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PlanExpController extends AbstractController
{

    private static function comparaison_etat_sa($sa1,$sa2){
        $mapEtatValue = ["Réparation"=>0, "Installation"=>1, "Non installé"=>2, "Opérationnel"=>3];
        $value1 = $mapEtatValue[$sa1->getEtat()];
        $value2 = $mapEtatValue[$sa2->getEtat()];
        if ($value1 == $value2) {
            return 0;
        }
        return ($value1 < $value2) ? -1 : 1;
    }

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

    #[Route('/plan/ajouter-salle', name: 'cdm_ajouter_salle')]
    public function cdm_ajouter_salle(ManagerRegistry $doctrine, Request $request): Response
    {
        $salle = new Salle();

        $entityManager = $doctrine->getManager();

        $listeOrientation = [
            'Nord' => 'No',
            'Sud' => 'Su',
            'Est' => 'Es',
            'Ouest' => 'Ou',
            'Nord-Est' => 'NE',
            'Nord-Ouest' => 'NO',
            'Sud-Est' => 'SE',
            'Sud-Ouest' => 'SO'
        ];

        $formSalle = $this->createFormBuilder($salle)
            ->add('nom',TextType::class, [
                'label' => 'Nom de la salle'
            ])
            ->add('batiment',TextType::class, [
                'label' => 'Nom du bâtiment'
            ])
            ->add('orientation', ChoiceType::class, [
                'choices' => $listeOrientation,
                'label' => 'Orientation'
            ])
            ->add('nombreFenetre', IntegerType::class, [
                'label' => 'Nombre de fenêtre'
            ])
            ->add('nombrePorte', IntegerType::class, [
                'label' => 'Nombre de porte'
            ])
            ->add('contientPc', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Contient des PCs',
                'required' => true
            ])
            ->add('ajouter', SubmitType::class,[
                'label' => 'Ajouter salle'
            ])
            ->getForm();

        $formSalle->handleRequest($request);

        if($formSalle->isSubmitted() && $formSalle->isValid()) {
            $salle = $formSalle->getData();
            $entityManager->persist($salle);
            $entityManager->flush();
            return $this->redirectToRoute('cdm_plan');
        }

        return $this->render('plan/charge_de_mission/ajouter_salle.html.twig', [
            'formSalle' => $formSalle
        ]);
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

    #[IsGranted("ROLE_CHARGE_DE_MISSION")]
    #[Route('/plan/seuils-alertes', name: 'cdm_seuils_alertes')]
    public function cdm_seuils_alertes(ManagerRegistry $doctrine, Request $request): Response
    {

        $entityManager = $doctrine->getManager();
        $seuilRepository = $entityManager->getRepository(Seuil::class);
        $seuil_temp_min = $seuilRepository->findOneBy(['nom' => 'temp_min']);
        $seuil_temp_max = $seuilRepository->findOneBy(['nom' => 'temp_max']);
        $seuil_humidite_max = $seuilRepository->findOneBy(['nom' => 'humidite_max']);
        $seuil_co2_premier_palier = $seuilRepository->findOneBy(['nom' => 'co2_premier_palier']);
        $seuil_co2_second_palier = $seuilRepository->findOneBy(['nom' => 'co2_second_palier']);

        $form = $this->createFormBuilder()
            ->add('temp_min',IntegerType::class, [
                'label' => 'Seuil bas',
                'data' => $seuil_temp_min->getValeur()
            ])
            ->add('temp_max',IntegerType::class, [
                'label' => 'Seuil haut',
                'data' => $seuil_temp_max->getValeur()
            ])
            ->add('humidite_max',IntegerType::class, [
                'label' => 'Seuil haut',
                'data' => $seuil_humidite_max->getValeur()
            ])
            ->add('co2_premier_palier',IntegerType::class, [
                'label' => 'Seuil moyen',
                'data' => $seuil_co2_premier_palier->getValeur()
            ])
            ->add('co2_second_palier',IntegerType::class, [
                'label' => 'Seuil haut',
                'data' => $seuil_co2_second_palier->getValeur()
            ])
            ->add('ajouter', SubmitType::class,[
                'label' => 'Valider les modifications'
            ])
            ->getForm();

        $form->handleRequest($request);
        $erreurs = [];
        if($form->isSubmitted()) {
            $seuil_temp_min_val = $form->getData()['temp_min'];
            $seuil_temp_max_val = $form->getData()['temp_max'];
            $seuil_humidite_max_val = $form->getData()['humidite_max'];
            $seuil_co2_premier_palier_val = $form->getData()['co2_premier_palier'];
            $seuil_co2_second_palier_val = $form->getData()['co2_second_palier'];
            if($seuil_temp_min_val < $seuil_temp_max_val && $seuil_co2_premier_palier_val < $seuil_co2_second_palier_val)
            {
                $seuil_temp_min->setValeur($seuil_temp_min_val);
                $seuil_temp_max->setValeur($seuil_temp_max_val);
                $seuil_humidite_max->setValeur($seuil_humidite_max_val);
                $seuil_co2_premier_palier->setValeur($seuil_co2_premier_palier_val);
                $seuil_co2_second_palier->setValeur($seuil_co2_second_palier_val);
                $entityManager->flush();
                return $this->redirectToRoute('accueil');
            }

            if($seuil_temp_min_val > $seuil_temp_max_val)
            {
                $erreurs['temp'] = "Le seuil bas ne peut pas être plus grand que le seuil haut";
            }
            if($seuil_co2_premier_palier_val > $seuil_co2_second_palier_val)
            {
                $erreurs['co2'] = "Le seuil moyen ne peut pas être plus grand que le seuil haut";
            }
        }
        return $this->render('plan/charge_de_mission/seuils_alertes.html.twig', [
            'erreurs' => $erreurs,
            'form' => $form
        ]);
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

        if($demandeTravaux->isTerminee()){
            throw $this->createNotFoundException('La demande de travaux est déjà terminée.');
        }

        $salleRepository = $entityManager->getRepository('App\Entity\Salle');
        $salle = $salleRepository->findOneBy(['id' => $demandeTravaux->getSalle()->getId()]);

        $sysAcquiRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');

        $dictReleves = null;

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


        $formSystemeAcqui = $this->createFormBuilder()
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

        $formSystemeAcqui->handleRequest($request);

        if($formSystemeAcqui->isSubmitted() && $formSystemeAcqui->isValid()) {

            if ($systemeAcquisition != null){
                $systemeAcquisition->setEtat("Non installé");
            }

            $id = $formSystemeAcqui->getData()['sa'];
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
            'formSystemeAcqui' => $formSystemeAcqui,
            'salle' => $salle
        ]);
    }

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/plan/ajouter_sa', name: 'technicien_ajouter_sa')]
    public function technicien_ajouter_sa(ManagerRegistry $doctrine, Request $request): Response
    {
        $sa = new SystemeAcquisition();

        $form = $this->createFormBuilder($sa)
            ->add('nom', TextType::class)
            ->add('baseDonnees', TextType::class)
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save'],
            ])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $sa = $form->getData();
            $sa->setEtat("Non installé");
            $service = new ReleveService();
            if(!$service->verifierNomBaseDeDonnees($sa->getBaseDonnees()))
            {
                $errorsString = "Base de données non valide.";
                return $this->render('plan/technicien/ajouter_sa.html.twig', [
                    'form' => $form,
                    'errorBaseDonnees' => $errorsString,
                ]);
            }
            else
            {

                $entityManager = $doctrine->getManager();
                $entityManager->persist($sa);
                $entityManager->flush();

                return $this->redirectToRoute('technicien_liste_sa',[]);
            }
        }

        return $this->render('plan/technicien/ajouter_sa.html.twig', [
            'form' => $form,
            'errorBaseDonnees' => null,
        ]);
    }

    #[Route('/plan/retirer_sa/<id>', name: 'technicien_retirer_sa')]
    public function technicien_retirer_sa(ManagerRegistry $doctrine, Request $request): Response
    {
        // full redirect
        throw $this->createNotFoundException('Page ou US non implémentée pour le moment');
    }

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/plan/lister_sa', name: 'technicien_liste_sa')]
    public function technicien_liste_sa(ManagerRegistry $doctrine, releveService $service, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $saRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');

        $listeChoix = ["Tous les SA" , "En cours d'installation", "Non installés", "Opérationnels", "Réparations"];

        $formEtats = $this->createFormBuilder()->add('choix', ChoiceType::class, [
            'choices' => array(
                "Tous les SA" => 0,
                "En cours d'installation" => 1,
                "Non installés" => 2,
                "Opérationnels" => 3,
                "Réparations" => 4),
            'required' => true,
        ])->getForm();


        $formEtats->handleRequest($request);
        $listeSa = [];
        $choix = "Tous les SA";
        $choixParDefault = 0;

        if($formEtats->isSubmitted() && $formEtats->isValid())
        {
            $choixParDefault = $formEtats->getData()['choix'];
            $choix = $listeChoix[$choixParDefault];
            switch($choix)
            {
                case "Tous les SA":
                    $listeSa = $saRepository->findAll();
                    break;
                case "En cours d'installation":
                    $listeSa = $saRepository->findBy(['etat' => "Installation"]);
                    break;
                case "Non installés":
                    $listeSa = $saRepository->findBy(['etat' => "Non installé"]);
                    break;
                case "Opérationnels":
                    $listeSa = $saRepository->findBy(['etat' => "Opérationnel"]);
                    break;
                case "Réparations":
                    $listeSa = $saRepository->findBy(['etat' => "Réparation"]);
                    break;
            }
        }
        else
        {
            $listeSa = $saRepository->findAll();
        }

        usort($listeSa, "self::comparaison_etat_sa");

        return $this->render('plan/technicien/liste_sa.html.twig', [
            'listeSa' => $listeSa,
            'form' => $formEtats,
            'choix' => $choix,
            'defaut' => $choixParDefault,
        ]);
    }

    #[Route('/plan/{id}/declarer-operationnel', name: 'technicien_declarer_operationnel')]
    public function technicien_declarer_operationnel(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $travauxRepository = $entityManager->getRepository('App\Entity\DemandeTravaux');
        $demandeTravaux = $travauxRepository->findOneBy(['id' => $id]);
        $demandeTravaux->setTerminee(true);

        $systemeAcquisitionRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');
        $systemeAcquisition = $systemeAcquisitionRepository->findOneBy(['id' => $demandeTravaux->getSystemeAcquisition()]);
        $systemeAcquisition->setEtat("Opérationnel");

        $salleRepository = $entityManager->getRepository('App\Entity\Salle');
        $salle = $salleRepository->findOneBy(['id' => $demandeTravaux->getSalle()]);
        $salle->setSystemeAcquisition($systemeAcquisition);

        $entityManager->flush();

        return $this->redirectToRoute("technicien_liste_sa");
    }
}
