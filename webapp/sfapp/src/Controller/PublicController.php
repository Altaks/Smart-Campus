<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Service\EnvironnementExterieurAPIService;
use App\Service\ReleveService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

            return $this->render('public/accueil.html.twig', [
                "demandesInstall" => $demandesInstall
            ]);
        }
        return $this->render('public/accueil.html.twig');

        //l'usager et le chargé de mission n'ont pas de données en plus sur leurs pages
    }

    #[Route('/releves', name: 'app_releves')]
    public function releves(ManagerRegistry $managerRegistry, ReleveService $releveService, Request $request)
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
                'label' => 'Salle',
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

            $last_temps_diff = $relevesTemp[count($relevesTemp) - 1] - $relevesTemp[count($relevesTemp) - 2];
            $last_humidity_diff = $relevesHum[count($relevesHum) - 1] - $relevesHum[count($relevesHum) - 2];
            $last_co2_diff = $relevesCo2[count($relevesCo2) - 1] - $relevesCo2[count($relevesCo2) - 2];

            return $this->render('public/releves.html.twig', [
                // data
                'form' => $form->createView(),
                'temp_dates' => $datesTemp,
                'temp_releves' => $relevesTemp,
                'temp_diff' => $last_temps_diff,
                'hum_dates' => $datesHum,
                'hum_releves' => $relevesHum,
                'hum_diff' => $last_humidity_diff,
                'co2_dates' => $datesCo2,
                'co2_releves' => $relevesCo2,
                'co2_diff' => $last_co2_diff,
            ]);
        }

        return $this->render('public/releves.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}