<?php

namespace App\Tests\Entity;

use App\Entity\Batiment;
use App\Entity\Salle;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BatimentTest extends TestCase
{

    public function test_un_batiment_a_un_nom(): void
    {
        $batiment = new Batiment();
        $batiment->setNom("Batiment A");
        $this->assertEquals("Batiment A", $batiment->getNom());
    }

    public function test_un_batiment_a_une_liste_de_salle(): void
    {
        $batiment = new Batiment();
        $salle1 = $this->createMock(Salle::class);
        $batiment->addSalle($salle1);
        $salle2 = $this->createMock(Salle::class);
        $batiment->addSalle($salle2);
        $listeSalles = new ArrayCollection();
        $listeSalles->add($salle1);
        $listeSalles->add($salle2);
        $this->assertEquals($listeSalles, $batiment->getSalles());
    }
}
