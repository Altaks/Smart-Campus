<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Service\releveService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RelevesController extends AbstractController
{
    #[Route('/releves', name: 'app_releves')]
    public function index(ManagerRegistry $managerRegistry, Request $request): Response
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

            $service = new releveService();

            $tag = $salle->getSystemeAcquisition()->getTag();
            $releves = [];

            if (!is_null($tag)){
                $releves = $service->getAll($salle->getSystemeAcquisition()->getTag());
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
