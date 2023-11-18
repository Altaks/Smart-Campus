<?php

namespace App\Controller;

use App\Entity\Batiment;
use App\Entity\Salle;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfraController extends AbstractController
{
    
    #[Route('/infra/systemes-acquisition', name: 'infra_liste_systemes-acquisition')]
    public function systemesAcquisition(ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();
        $dispositifsRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');

        $listeDispositifs = $dispositifsRepository->findAll();

        return $this->render('infra/liste-systemes-acquisition.html.twig', [
            'dispositifs' => $listeDispositifs
        ]);
    }

    #[Route('/infra/systemes-acquisition/modifier/{id}', name: 'infra-modifier-systemes-acquisition')]
    public function modifierSystemesAcquisition(ManagerRegistry $managerRegistry, ?int $id, Request $request) : Response
    {

        $entityManager = $managerRegistry->getManager();

        $systemeAcquisitionRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');
        $batimentRepository = $entityManager->getRepository('App\Entity\Batiment');
        $sallesRepository = $entityManager->getRepository('App\Entity\Salle');

        $systemeAcquisition = $systemeAcquisitionRepository->findOneBy(
            array('id' => $id)
        );

        if ($request->getMethod() == 'POST') {

            $systemeAcquisition->setAdresseMac($_POST['adresseMac']);

            if ($_POST['salle'] == '-1') {
                $systemeAcquisition->setSalle(null);
            } else {
                $systemeAcquisition->setSalle($sallesRepository->findOneBy(
                    array('id' => $_POST['salle'])
                ));
            }

            $entityManager->flush();

            return $this->redirectToRoute('infra_liste_systemes-acquisition');
        }

        $listeBatiments = $batimentRepository->findAll();
        $listeSalles = $sallesRepository->findAll();

        return $this->render('infra/modifier-sa.html.twig', [
            'id' => $id,
            'systemeAcquisition' => $systemeAcquisition,
            'listeBatiments' => $listeBatiments,
            'listeSalles' => $listeSalles
        ]);
    }

    #[Route('/infra/systemes-acquisition/modifier/{id}/valider', name: 'infra-valider-modifier-systemes-acquisition')]
    public function validerModifierSystemesAcquisition(ManagerRegistry $managerRegistry, int $id) : Response
    {
        $entityManager = $managerRegistry->getManager();

        $systemeAcquisitionRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');
        $batimentRepository = $entityManager->getRepository('App\Entity\Batiment');
        $sallesRepository = $entityManager->getRepository('App\Entity\Salle');

        $systemeAcquisition = $systemeAcquisitionRepository->findOneBy(
            array('id' => $id)
        );

        $listeBatiments = $batimentRepository->findAll();
        $listeSalles = $sallesRepository->findAll();

        $systemeAcquisition->setAdresseMac($_POST['adresseMac']);
        $systemeAcquisition->setSalle($sallesRepository->findOneBy(
            array('id' => $_POST['salle'])
        ));

        $entityManager->persist($systemeAcquisition);
        $entityManager->flush();

        return $this->redirectToRoute('infra_liste_systemes-acquisition');
    }

    #[Route('/infra/batiments-et-salles', name: 'app_infra_batiments-et-salles')]
    public function batimentsEtSalles(ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();

        $repository = $entityManager->getRepository('App\Entity\Salle');

        $listeSalles = $repository->findAll();

        return $this->render('infra/batiments-et-salles.html.twig', [
            'listeSalles' => $listeSalles
        ]);
    }
}
