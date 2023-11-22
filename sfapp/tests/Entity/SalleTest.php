<?php

namespace App\Tests\Entity;

use App\Entity\Batiment;
use App\Entity\Salle;
use PHPUnit\Framework\TestCase;

class SalleTest extends TestCase
{
    public function test_une_salle_a_un_numero(): void
    {
        $salle = new Salle();
        $salle->setNumero(303);
        $this->assertEquals(303,$salle->getNumero());
    }

    public function test_une_salle_appartient_a_un_batiment(): void
    {
        $salle = new Salle();
        $batiment = $this->createMock(Batiment::class);
        $salle->setBatiment($batiment);
        $this->assertEquals($batiment,$salle->getBatiment());
    }

    // Les orientations possibles sont No, Es, Su, Ou, NE, NO, SO, SE et aucunes autres
    public function test_une_salle_a_une_orientation(): void
    {
        $salle = new Salle();
        $arr = array("No", "Es", "Su", "Ou", "NE", "NO","SO","SE");
        foreach($arr as &$value)
        {
            $salle->setOrientation($value);
            $this->assertEquals($value,$salle->getOrientation());
        }
    }

    public function test_une_salle_a_un_nombre_de_fenetre()
    {
        $salle = new Salle();
        $salle->setNbfenetres(4);
        $this->assertEquals(4,$salle->getNbfenetres());
    }

    public function test_une_salle_a_un_nombre_de_portes()
    {
        $salle = new Salle();
        $salle->setNbportes(4);
        $this->assertEquals(4,$salle->getNbportes());
    }

    public function test_une_salle_peut_posseder_des_ordinateurs()
    {
        $salle = new Salle();
        $salle->setContientpc(true);
        $this->assertEquals(true,$salle->isContientpc());
    }
}
