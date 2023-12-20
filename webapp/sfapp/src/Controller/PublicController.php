<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Service\ReleveService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PublicController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion')]
    public function connexion(AuthenticationUtils $authenticationUtils, Security $security, Request $request, ManagerRegistry $registry): Response
    {
        $erreur = $authenticationUtils->getLastAuthenticationError();

        $dernierIdentifiant = $authenticationUtils->getLastUsername();

        return $this->render('connexion/index.html.twig', [
            'dernier_identifiant' => $dernierIdentifiant,
            'erreur' => $erreur,
        ]);
    }

    #[Route('/auth-Success', name: 'auth-success')]
    public function onAuthenticationSuccess(Security $security, Request $request)
    {
        return $this->redirect('/accueil');
    }


    #[Route('/accueil/', name: 'personel_accueil')]
    public function indexTechnicien(): Response
    {
        if ($this->isGranted("ROLE_TECHNICIEN")){
            return $this->render('accueil/technicien.html.twig', []);
        }
        elseif ($this->isGranted("ROLE_CHARGE_DE_MISSION")){
            return $this->render('accueil/charge-de-mission.html.twig', []);
        }
        else{
            throw $this->createNotFoundException("Page non implémentée pour le moment");
        }
    }

    #[Route('/releves', name: 'app_releves')]
    public function releves(ManagerRegistry $managerRegistry, Request $request): Response
    {
        $sallesRepository = $managerRegistry->getRepository('App\Entity\Salle');

        // Récupération de toutes les salles ayant des relevés avec du DQL
        $salles = $sallesRepository->findAll();

        $builder = $this->createFormBuilder()
            ->add('salle', ChoiceType::class, [
                'choices' => $salles,
                'choice_label' => function(?Salle $salle) {
                    return $salle ? $salle->getBatiment()->getNom() . " - " . $salle->getNom() : '';
                },
                'choice_value' => function(?Salle $salle) {
                    return $salle ? $salle->getId() : '';
                },
                'label' => 'Salle',
                'required' => true,
                'placeholder' => 'Choisir une salle',
            ])
            ->getForm();


        $builder->handleRequest($request);

        if($builder->isSubmitted() && $builder->isValid()) {

            $id = $builder->getData()['salle'];
            $salle = $sallesRepository->findOneBy(["id"=>$id]);

            $service = new ReleveService();

            $sa = $salle->getSystemeAcquisition();
            $releves = [];

            if (!is_null($sa)){
                $releves = $service->getAll($sa->getTag());
            }

            return $this->render('releves/index.html.twig', [
                'salles' => $salles,
                'salle_actuelle' => $salle,
                'releves' => $releves,
                'form' => $builder->createView(),
            ]);
        }

        return $this->render('releves/index.html.twig', [
            'salles' => $salles,
            'form' => $builder->createView(),
        ]);
    }
}