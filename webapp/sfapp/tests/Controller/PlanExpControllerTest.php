<?php

namespace App\tests\Controller;

use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlanExpControllerTest extends WebTestCase
{
    public function test_cdm_demander_installation_sur_salle_valide(): void
    {
        $client = static::createClient();

        // Vérifier si la salle D001 existe
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $sallesRepository = $entityManager->getRepository(Salle::class);

        $salleD = $sallesRepository->findOneBy(['nom' => 'D001']);

        if($salleD == null){

            $salle = new Salle();
            $salle->setNom("D001");
            $salle->setBatiment("Bâtiment D");
            $salle->setOrientation("No");
            $salle->setNombrePorte(1);
            $salle->setNombreFenetre(6);
            $salle->setSystemeAcquisition(null);
            $salle->setContientPc(false);

            $entityManager->persist($salle);
            $entityManager->flush();
        }

        $salleD = $sallesRepository->findOneBy(['nom' => 'D001']);

        $client->request('GET', '/plan/' . $salleD->getId() . '/demander-installation');
        $this->assertResponseRedirects("/plan");
    }

    public function test_cdm_demander_installation_sur_salle_invalide(): void
    {
        $client = static::createClient();

        // La salle d'id -1 n'existe pas, le serveur doit renvoyer une erreur 404
        $client->request('GET', '/plan/-1/demander-installation');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_cdm_demander_installation_sur_salle_avec_sa() : void
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $systemeAcquisition = new SystemeAcquisition();
        $systemeAcquisition->setNom("ESP-123");
        $systemeAcquisition->setBaseDonnees("sae34bdm2eq3");

        $salle = new Salle();
        $salle->setNom("X001");
        $salle->setBatiment("Bâtiment X");
        $salle->setOrientation("No");
        $salle->setNombrePorte(1);
        $salle->setNombreFenetre(6);
        $salle->setSystemeAcquisition($systemeAcquisition);
        $salle->setContientPc(false);

        $entityManager->persist($systemeAcquisition);
        $entityManager->persist($salle);
        $entityManager->flush();

        $sa = $entityManager->getRepository(SystemeAcquisition::class)->findOneBy(['nom' => 'ESP-123']);
        $this->assertNotNull($sa);

        // La salle d'id -1 n'existe pas, le serveur doit renvoyer une erreur 404
        $client->request('GET', '/plan/' . $sa->getId() . '/demander-installation');
        $this->assertResponseStatusCodeSame(404);
    }

}
