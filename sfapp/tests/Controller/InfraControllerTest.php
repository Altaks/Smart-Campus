<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InfraControllerTest extends WebTestCase
{
    public function testListeSystemesAcquisition(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/infra/systemes-acquisition');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', "Systèmes d'acquisition connectés");
        $th = $crawler->filter("th");
        $this->assertEquals($th->eq(0)->text(), 'Identifiant');
        $this->assertEquals($th->eq(1)->text(), 'Adresse Mac');
        $this->assertEquals($th->eq(2)->text(), 'Bâtiment');
        $this->assertEquals($th->eq(3)->text(), 'Salle');
        $this->assertEquals($th->eq(4)->text(), 'Modifier');
        $this->assertSelectorTextContains('nav', 'Smart Campus');
    }
}