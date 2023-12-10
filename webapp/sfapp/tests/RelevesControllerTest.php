<?php

namespace App\Tests;

use App\Entity\Batiment;
use App\Entity\Salle;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RelevesControllerTest extends WebTestCase
{
    public function test_route_releves_existe(): void
    {
        $client = static::createClient();
        $client->request('GET', '/releves');
        $this->assertResponseIsSuccessful();
    }

    public function test_route_releves_contient_formulaire_recherche() : void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/releves');
        $this->assertSelectorExists('form');

        $input_filter = $crawler->filter("form > input");
        $this->assertEquals(2, $input_filter->count());

        $submit_filter = $crawler->filter("form > button");
        $this->assertEquals(1, $submit_filter->count());
    }

    public function test_route_releves_de_salle_contient_tableau() : void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Réinitialiser les salles
        $salleRepository = $entityManager->getRepository('App\Entity\Salle');
        foreach ($salleRepository->findAll() as $salle) {
            $entityManager->remove($salle);
        }

        $batiment = new Batiment();
        $batiment->setNom('Batiment Z');

        $salle = new Salle();
        $salle->setNom('302');
        $salle->setBatiment($batiment);
        $salle->setOrientation('No');
        $salle->setNbfenetres(2);
        $salle->setNbportes(1);
        $salle->setContientPc(true);

        $entityManager->persist($batiment);
        $entityManager->persist($salle);

        $entityManager->flush();

        // récupération de la salle
        $salle = $salleRepository->findOneBy(['nom' => '302']);
        $salle_id = $salle->getId();

        $crawler = $client->request('GET', '/releves/' . $salle_id);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table');
    }

    public function test_route_releves_de_salle_contient_tableau_avec_colonnes() : void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Réinitialiser les salles
        $salleRepository = $entityManager->getRepository('App\Entity\Salle');
        foreach ($salleRepository->findAll() as $salle) {
            $entityManager->remove($salle);
        }

        $batiment = new Batiment();
        $batiment->setNom('Batiment Z');

        $salle = new Salle();
        $salle->setNom('302');
        $salle->setBatiment($batiment);
        $salle->setOrientation('No');
        $salle->setNbfenetres(2);
        $salle->setNbportes(1);
        $salle->setContientPc(true);

        $entityManager->persist($batiment);
        $entityManager->persist($salle);

        $entityManager->flush();

        // récupération de la salle
        $salle = $salleRepository->findOneBy(['nom' => '302']);
        $salle_id = $salle->getId();

        $crawler = $client->request('GET', '/releves/' . $salle_id);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table');

        $table_headers = $crawler->filter('table > th');
        $this->assertSelectorTextSame($table_headers->eq(0), 'Horodatage');
        $this->assertSelectorTextSame($table_headers->eq(1), 'Température (°C)');
        $this->assertSelectorTextSame($table_headers->eq(2), "Qualité de l'air (ppm)");
        $this->assertSelectorTextSame($table_headers->eq(3), 'Humidité (%)');
    }
}