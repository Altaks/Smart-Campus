<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InfraControllerTest extends WebTestCase
{
    public function testListeSalles(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/infra/batiments-et-salles');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Salles disponibles');

        $th = $crawler->filter("th");
        $this->assertEquals($th->eq(0)->text(), 'Bâtiment');
        $this->assertEquals($th->eq(1)->text(), 'Etage');
        $this->assertEquals($th->eq(2)->text(), 'Numéro');
        $this->assertEquals($th->eq(3)->text(), 'Orientation');
        $this->assertEquals($th->eq(4)->text(), 'Nombre de fenêtres');
        $this->assertEquals($th->eq(5)->text(), 'Nombre de portes');
        $this->assertEquals($th->eq(6)->text(), 'Contient des ordinateurs');
    }
}
