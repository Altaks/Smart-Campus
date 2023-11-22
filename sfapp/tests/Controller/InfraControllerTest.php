<?php

namespace App\Tests\Controller;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InfraControllerTest extends WebTestCase
{
    public function testModifierSystemeAcquisition(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/infra/systemes-acquisition/modifier/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Modifier le Système d'Acquisition n°1");
        $th = $crawler->filter("label");
        $this->assertEquals($th->eq(0)->text(), "Adresse MAC du système d'acquisition :");
        $this->assertEquals($th->eq(1)->text(), "Bâtiment");
        $this->assertEquals($th->eq(2)->text(), "Salle");
    }
}
