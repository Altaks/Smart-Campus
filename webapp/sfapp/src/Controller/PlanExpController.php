<?php

namespace App\Controller;


use App\Entity\DemandeTravaux;
use App\Entity\Salle;
use App\Entity\Seuil;
use App\Entity\SystemeAcquisition;
use App\Service\ReleveService;
use DateTime;
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

    private static function comparaison_etat_salle($salle1,$salle2){
        $mapEtatValue = ["Réparation"=>0, "Installation"=>1, "Non installé"=>2, "Opérationnel"=>3];
        $value1 = $mapEtatValue[($salle1->getSystemeAcquisition() == null) ? 'Non installé' : $salle1->getSystemeAcquisition()->getEtat()];
        $value2 = $mapEtatValue[($salle2->getSystemeAcquisition() == null) ? 'Non installé' : $salle2->getSystemeAcquisition()->getEtat()];
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
    #[Route('/plan/lister-salles', name: 'cdm_lister_salles')]
    public function cdm_lister_salles(ManagerRegistry $doctrine): Response
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
                    if($listeSalles[$i]->getDemandesTravaux()[$j]->getType() == 'Installation') {
                        $etat = "Installation demandée";
                    }
                    elseif($listeSalles[$i]->getDemandesTravaux()[$j]->getType() == 'Réparation') {
                        $etat = "Réparation demandée";
                    }
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

        usort($salleArr, "self::comparaison_etat_salle");

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
            return $this->render('bundles/TwigBundle/Exception/error405.html.twig', [
                "message" => "Vous ne pouvez pas demander l'installation d'un système d'acquisition à une salle qui n'existe pas"
            ], new Response("La salle n'existe pas", 405));
        } else if ($salle->getSystemeAcquisition() != null) {
            return $this->render('bundles/TwigBundle/Exception/error405.html.twig', [
                "message" => "Vous ne pouvez pas demander l'installation d'un système d'acquisition à une salle qui en possède déjà un"
            ], new Response("La salle dispose déjà d'un système d'acquisition", 405));
        }

        $demandeTravaux = $entityManager->getRepository('App\Entity\DemandeTravaux');
        $demandeTravauxSalleNonTerminee = $demandeTravaux->findOneBy(['salle' => $salle->getId(), 'type' => 'Installation', 'terminee' => false]);

        if($demandeTravauxSalleNonTerminee != null){
            return $this->render('bundles/TwigBundle/Exception/error405.html.twig', [
                "message" => "Vous ne pouvez pas demander l'installation d'un système d'acquisition à une salle qui a déjà une demande d'installation en cours"
            ], new Response("Une demande d'installation est déjà en cours pour cette salle", 405));
        }

        $demandeTravaux = new DemandeTravaux();
        $demandeTravaux->setSalle($salle);
        $demandeTravaux->setTerminee(false);
        $demandeTravaux->setDate(new \DateTime());
        $demandeTravaux->setType("Installation");

        $entityManager->persist($demandeTravaux);
        $entityManager->flush();

        return $this->redirectToRoute("cdm_lister_salles");
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
                'label' => 'Ajouter la salle'
            ])
            ->getForm();

        $formSalle->handleRequest($request);

        if($formSalle->isSubmitted() && $formSalle->isValid()) {
            $salle = $formSalle->getData();
            $entityManager->persist($salle);
            $entityManager->flush();
            return $this->redirectToRoute('cdm_lister_salles');
        }

        return $this->render('plan/charge_de_mission/ajouter_salle.html.twig', [
            'formSalle' => $formSalle
        ]);
    }

    #[Route('/plan/retirer-salle/{id}', name: 'cdm_retirer_salle')]
    public function cdm_retirer_salle(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $salleRepository = $entityManager->getRepository('App\Entity\Salle');
        $salle = $salleRepository->findOneBy(['id' => $id]);

        if($salle != null){
            $demandesSalle = $salle->getDemandesTravaux();
            foreach ($demandesSalle as $demande){
                $entityManager->remove($demande);
            }
            $sa = $salle->getSystemeAcquisition();
            if($sa != null)
            {   $sa->setSalle(null);
                $sa->setEtat("Non installé");
            }

            $salle->setSystemeAcquisition(null);

            $entityManager->remove($salle);
            $entityManager->flush();
        }
        else{
            return $this->render('bundles/TwigBundle/Exception/error405.html.twig', [
                "message" => "Vous ne pouvez pas supprimer une salle qui n'existe pas"
            ], new Response("La salle n'existe pas", 405));
        }
        return $this->redirectToRoute('cdm_lister_salles');
    }

    #[Route('/plan/modifier-salle/{id}', name: 'cdm_modifier_salle')]
    public function cdm_modifier_salle(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $salleRepository = $entityManager->getRepository('App\Entity\Salle');
        $salle = $salleRepository->findOneBy(['id' => $id]);

        if ($salle == null) {
            return $this->render('bundles/TwigBundle/Exception/error405.html.twig', [
                "message" => "Vous ne pouvez pas modifier une salle qui n'existe pas"
            ], new Response("La salle n'existe pas", 405));
        }

        $formSalle = $this->createFormBuilder($salle)
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
            ->add('modifier', SubmitType::class,[
                'label' => 'Modifier la salle'
            ])
            ->getForm();

        $formSalle->handleRequest($request);

        if($formSalle->isSubmitted() && $formSalle->isValid()) {
            $salle = $formSalle->getData();
            $entityManager->flush();
            return $this->redirectToRoute('cdm_lister_salles');
        }

        return $this->render('plan/charge_de_mission/modifier_salle.html.twig', [
            'formSalle' => $formSalle,
            'salle' => $salle
        ]);
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

            if($seuil_temp_min_val >= $seuil_temp_max_val)
            {
                $erreurs['temp'] = "Le seuil bas ne peut pas être plus grand que le seuil haut";
            }
            if($seuil_co2_premier_palier_val >= $seuil_co2_second_palier_val)
            {
                $erreurs['co2'] = "Le seuil moyen ne peut pas être plus grand que le seuil haut";
            }
        }
        return $this->render('plan/charge_de_mission/seuils_alertes.html.twig', [
            'erreurs' => $erreurs,
            'form' => $form
        ]);
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
                if ($systemeAcquisition->getSalle() != null){
                    $systemeAcquisition->getSalle()->setSystemeAcquisition(null);
                }
            }

            $id = $formSystemeAcqui->getData()['sa'];
            if ($id == null){
                $demandeTravaux->setSystemeAcquisition(null);
                $entityManager->flush();
                return $this->redirect('/plan/demande-travaux/' . $demandeTravaux->getId());
            }

            $sa = $sysAcquiRepository->findOneBy(['id' => $id]);
            $demandeTravaux->setSystemeAcquisition($sa);
            $sa->setEtat($demandeTravaux->getType());
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
    #[Route('/plan/ajouter-sa', name: 'technicien_ajouter_sa')]
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

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/plan/retirer-sa/{id}', name: 'technicien_retirer_sa')]
    public function technicien_retirer_sa(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $saRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');
        $sa = $saRepository->findOneBy(['id' => $id]);

        if($sa != null){
            $salle = $sa->getSalle();

            // Si le SA était associé à une salle, on le retire de la salle
            if($salle != null){
                $salle->setSystemeAcquisition(null);
            }

            // Si le SA était associé à une demande de travaux, on le retire de la demande de travaux
            $demandesTravaux = $sa->getDemandesTravaux();
            foreach ($demandesTravaux as $demande){
                $demande->setSystemeAcquisition(null);
            }

            // On supprime le S.A
            $entityManager->remove($sa);
            $entityManager->flush();
            return $this->redirect("/plan/lister-sa");
        }
        else{
            return $this->render('bundles/TwigBundle/Exception/error405.html.twig', [
                "message" => "Vous ne pouvez pas supprimer un système d'acquisition qui n'existe pas"
            ], new Response("Le système d'acquisition n'existe pas", 405));
        }

    }


    private function recupererEtatsCapteurs(array $listeSa, $dateMoins5minet3secondes, ReleveService $service) : array
    {
        $listeEtatsCapteurs = [];
        foreach ($listeSa as $sa)
        {
            if ($sa->getEtat() == "Opérationnel")
            {
                $listeEtatsCapteurs[$sa->getId()] = $service->verifierEtatCapteurs($sa);
            }
            else
            {
                $listeEtatsCapteurs[$sa->getId()] = null;
            }
        }
        return $listeEtatsCapteurs;
    }

    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/plan/lister-sa', name: 'technicien_liste_sa')]
    public function technicien_liste_sa(ManagerRegistry $doctrine, releveService $service, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $saRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');
        $demandeTravauxRepository = $entityManager->getRepository('App\Entity\DemandeTravaux');


        $listeSalleSaIntall = [];
        $choixParDefault = 0;
        date_default_timezone_set("Europe/Paris");

        $listeSa = $saRepository->findAll();
        $dateMoins5minet30secondes = time() - 5*60 - 30;
        $listeEtatsCapteurs = $this->recupererEtatsCapteurs($listeSa, $dateMoins5minet30secondes, $service);


        foreach ($listeSa as $sa)
        {
            if ($sa->getEtat() == "Installation" || $sa->getEtat() == "Réparation")
            {
                $dt = $demandeTravauxRepository->findOneBy(['systemeAcquisition' => $sa, 'type' => $sa->getEtat(), 'terminee' => false]);

                if ($dt != null) {
                    $listeSalleSaIntall[$sa->getId()] = $dt->getSalle();
                }
            }
        }


        usort($listeSa, "self::comparaison_etat_sa");


        return $this->render('plan/technicien/liste_sa.html.twig', [
            'listeSa' => $listeSa,
            'defaut' => $choixParDefault,
            'listeEtatCapteurs' => $listeEtatsCapteurs,
            'listeSalleSaDT' => $listeSalleSaIntall
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

        if ($systemeAcquisition == null){
            return $this->render('bundles/TwigBundle/Exception/error405.html.twig', [
                "message" => "Vous ne pouvez pas déclarer opérationnel un système d'acquisition qui n'existe pas"
            ], new Response("Le système d'acquisition n'existe pas", 405));
        }

        $systemeAcquisition->setEtat("Opérationnel");

        $salleRepository = $entityManager->getRepository('App\Entity\Salle');
        $salle = $salleRepository->findOneBy(['id' => $demandeTravaux->getSalle()]);
        $salle->setSystemeAcquisition($systemeAcquisition);

        $entityManager->flush();

        return $this->redirectToRoute("technicien_liste_sa");
    }
}
