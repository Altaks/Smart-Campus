<?php

namespace App\Controller;

use App\Entity\DemandeTravaux;
use App\Entity\Salle;
use App\Entity\Seuil;
use App\Service\EnvironnementExterieurAPIService;
use App\Service\ReleveService;
use App\Service\SeuilService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\DateTime;

class PublicController extends AbstractController
{

    #[Route('/', name:'index')]
    public function index(): Response
    {
        return $this->redirectToRoute("accueil");
    }

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

    #[Route('/deconnexion', name: 'app_deconnexion')]
    public function deconnexion(Security $security) : Response
    {
        $response = $security->logout();
        return $response;
    }

    #[Route('/accueil/', name: 'accueil')]
    public function indexTechnicien(ManagerRegistry $registry): Response
    {
        if ($this->isGranted("ROLE_TECHNICIEN")){

            $demandesInstall = $registry->getRepository('App\Entity\DemandeTravaux')
                ->findBy(["type"=>"Installation",
                    "terminee"=>false]);
            $demandesRepar = $registry->getRepository('App\Entity\DemandeTravaux')
                ->findBy(["type"=>"Reparation",
                    "terminee"=>false]);

            return $this->render('public/accueil.html.twig', [
                "demandesInstall" => $demandesInstall,
                "demandesRepar" => $demandesRepar
            ]);
        }
        return $this->render('public/accueil.html.twig');

        //l'usager et le chargé de mission n'ont pas de données en plus sur leurs pages
    }

    #[Route('/releves', name: 'app_releves')]
    public function releves(ManagerRegistry $managerRegistry, EnvironnementExterieurAPIService $envAPI, ReleveService $releveService, SeuilService $seuilService, Request $request)
    {

        $sallesRepository = $managerRegistry->getRepository('App\Entity\Salle');

        // Récupération de toutes les salles ayant des relevés avec du DQL
        $salles = $sallesRepository->findAllSallesAvecSAOperationnel();

        $form = $this->createFormBuilder()
            ->add('salle', ChoiceType::class, [
                'choices' => $salles,
                'choice_label' => function(?Salle $salle) {
                    return $salle ? $salle->getNom() : '';
                },
                'choice_value' => function(?Salle $salle) {
                    return $salle ? $salle->getId() : '';
                },
                'label' => 'Veuillez choisir une salle',
                'placeholder' => 'Choisir une salle',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $salle = $form->get('salle')->getData();
            $sa = $salle->getSystemeAcquisition();

            $releves30Jours = $releveService->getEntre($sa, new \DateTime('-1 days'), new \DateTime("+1 days"));
            ksort($releves30Jours);

            date_default_timezone_set("Europe/Paris");

            foreach ($releves30Jours as $date => $vals){
                $comparable_date = new \DateTime($date);
                if(date_diff($comparable_date, new \DateTime())->d > 0) unset($releves30Jours[$date]);
                if($comparable_date > new \DateTime()) unset($releves30Jours[$date]);
            }

            $datesTemp = [];
            $relevesTemp = [];

            $datesHum = [];
            $relevesHum = [];

            $datesCo2 = [];
            $relevesCo2 = [];

            $dernierTemp = $releveService->getDernier($sa,'temp')['valeur'];
            $dernierHum = $releveService->getDernier($sa,'hum')['valeur'];
            $dernierCo2 = $releveService->getDernier($sa,'co2')['valeur'];

            $seuils = $seuilService->getSeuils($managerRegistry);

            foreach ($releves30Jours as $date => $releves){
                if($releves['temp'] != null){
                    $datesTemp[] = $date;
                    $relevesTemp[] = $releves['temp'];
                }
                if($releves['hum'] != null){
                    $datesHum[] = $date;
                    $relevesHum[] = $releves['hum'];
                }
                if($releves['co2'] != null){
                    $datesCo2[] = $date;
                    $relevesCo2[] = $releves['co2'];
                }
            }

            // Vérifier l'état des données et créer des messages d'erreurs si nécessaire
            $last_temps_diff = 0;
            $temp_err = "";

            if(count($relevesTemp) <= 0){
                $temp_err = "Aucune donnée récupérée pour la température";
                $datesTemp = null;
                $relevesTemp = null;
            } else if(count($relevesTemp) > 1){
                $last_temps_diff = $relevesTemp[count($relevesTemp) - 1] - $relevesTemp[count($relevesTemp) - 2];
            }

            $last_humidity_diff = 0;
            $hum_err = "";
            if(count($relevesHum) <= 0){
                $hum_err = "Aucune donnée récupérée pour l'humidité";
                $datesHum = null;
                $relevesHum = null;
            } else if(count($relevesHum) > 1){
                $last_humidity_diff = $relevesHum[count($relevesHum) - 1] - $relevesHum[count($relevesHum) - 2];
            }

            $last_co2_diff = 0;
            $co2_err = "";
            if(count($relevesCo2) <= 0){
                $co2_err = "Aucune donnée récupérée pour le CO2";
                $datesCo2 = null;
                $relevesCo2 = null;
            } else if(count($relevesCo2) > 1){
                $last_co2_diff = $relevesCo2[count($relevesCo2) - 1] - $relevesCo2[count($relevesCo2) - 2];
            }

            $envData = $envAPI->queryDailyTempsAndHumidity();

            $envTemperatures = [];
            $envHumidity = [];

            $ttmp_now = new \DateTime("now");

            foreach ($datesTemp as $date){
                $timestamp = new \DateTime($date);
                $timestamp = $timestamp->getTimestamp();
                $timestamp = $timestamp - ($timestamp % 3600); // Arrondi à l'heure vers le bas

                $ttmp_date = new \DateTime("@" . $timestamp);

                while(!in_array($timestamp, $envData["timestamps"]) && $ttmp_now > $ttmp_date){
                    $timestamp += 3600;
                    $ttmp_date = new \DateTime("@" . $timestamp);
                }

                $ttmp_index = array_search($timestamp, $envData["timestamps"]);

                $envTemperatures[] = $envData["temperatures"][$ttmp_index];
                $envHumidity[] = $envData["humidity"][$ttmp_index];
            }

            // Renvoyer la vue
            return $this->render('public/releves.html.twig', [
                // data
                'form' => $form->createView(),
                'form_selected' => $salle,

                'temp_dates' => $datesTemp,
                'temp_releves' => $relevesTemp,
                'temp_env' => $envTemperatures,
                'temp_diff' => $last_temps_diff,
                'temp_error' => $temp_err,
                'temp_dernier' => $dernierTemp,

                'hum_dates' => $datesHum,
                'hum_releves' => $relevesHum,
                'hum_env' => $envHumidity,
                'hum_diff' => $last_humidity_diff,
                'hum_error' => $hum_err,
                'hum_dernier' => $dernierHum,

                'co2_dates' => $datesCo2,
                'co2_releves' => $relevesCo2,
                'co2_diff' => $last_co2_diff,
                'co2_error' => $co2_err,
                'co2_dernier' => $dernierCo2,

                'seuils' => $seuils,
            ]);
        }

        return $this->render('public/releves.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/signaler-erreur/{id}', name: 'app_signaler_erreur')]
    public function signalerErreur(ManagerRegistry $registry, $id, Request $request)
    {
        $salle = $registry->getRepository('App\Entity\Salle')->find($id);

        if ($salle == null || $salle->getSystemeAcquisition() == null || $salle->getSystemeAcquisition()->getEtat() != "Opérationnel") {
            return $this->redirectToRoute('accueil');
        }

        $demandeTravauxRepository = $registry->getRepository('App\Entity\DemandeTravaux');

        $demande = $demandeTravauxRepository->findOneBy([
            'salle' => $salle,
            'terminee' => false,
        ]);

        if ($demande != null) {
            return $this->redirectToRoute('accueil');
        }

        $demande = new DemandeTravaux();

        $form = $this->createFormBuilder($demande)
            ->add('emailDemandeur', TextType::class, [
                'label' => 'Email (pour vous contacter en cas de besoin)',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('commentaire', TextType::class, [
                'label' => 'Commentaire',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Décrivez le problème, exemple : "La température dépasse les 100°C"'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setSalle($salle);
            $demande->setType("Réparation");
            $demande->setSystemeAcquisition($salle->getSystemeAcquisition());
            $demande->setTerminee(false);
            $demande->setDate(new \DateTime());
            $demande->setEmailDemandeur($form->get('emailDemandeur')->getData());
            $demande->setCommentaire($form->get('commentaire')->getData());
            $manager = $registry->getManager();
            $manager->persist($demande);

            $salle->getSystemeAcquisition()->setEtat("Réparation");

            $manager->flush();

            return $this->redirectToRoute('accueil');
        }

        return $this->render('public/signaler_erreur.html.twig', [
            'form' => $form,
            'salle' => $salle,
        ]);

    }
}