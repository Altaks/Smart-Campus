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

        $erreur = null;

        $entityManager = $managerRegistry->getManager();

        $systemeAcquisitionRepository = $entityManager->getRepository('App\Entity\SystemeAcquisition');
        $batimentRepository = $entityManager->getRepository('App\Entity\Batiment');
        $sallesRepository = $entityManager->getRepository('App\Entity\Salle');

        $systemeAcquisition = $systemeAcquisitionRepository->findOneBy(
            array('id' => $id)
        );

        if ($request->getMethod() == 'POST') {

            // check if the mac address format is correct

            $address = strtoupper($_POST['adresseMac']);

            if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $_POST['adresseMac'])) {
                $erreur = ['code' => 2, 'mac' => $_POST['adresseMac']];
            }
            else {
                $tempSysteme = $systemeAcquisitionRepository->findOneBy(
                    array('adresse_mac' => $address)
                );

                if ($tempSysteme != null && ($tempSysteme->getId() != $systemeAcquisition->getId())) {
                    $erreur = ['code' => 3, 'mac' => $_POST['adresseMac'], 'systeme' => $tempSysteme->getId()];
                } elseif ($_POST['salle'] != '-1') {
                    $tempSysteme = $systemeAcquisitionRepository->findOneBy(
                        array('salle' => $_POST['salle'])
                    );

                    if ($tempSysteme != null && ($tempSysteme->getId() != $systemeAcquisition->getId())) {
                        $batiment = $tempSysteme->getSalle()->getBatiment()->getNom();
                        $salle = $tempSysteme->getSalle()->getNumero();
                        $erreur = ['code' => 1, 'batiment' => $batiment , 'salle' => $salle, 'systeme-lie' => $tempSysteme->getId()];
                    }
                }
            }

            if ($erreur == null) {
                $systemeAcquisition->setAdresseMac($address);
                if ($_POST['salle'] != '-1'){
                    $systemeAcquisition->setSalle($sallesRepository->findOneBy(
                        array('id' => $_POST['salle'])
                    ));
                } else {
                    $systemeAcquisition->setSalle(null);
                }
                $entityManager->flush();
                return $this->redirectToRoute('infra_liste_systemes-acquisition');
            }
        }

        $listeBatiments = $batimentRepository->findAll();
        $listeSalles = $sallesRepository->findAll();

        return $this->render('infra/modifier-sa.html.twig', [
            'id' => $id,
            'erreur' => $erreur,
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
