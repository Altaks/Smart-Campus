<?php

namespace App\Tests\Entity;

use App\Entity\Batiment;
use App\Entity\Salle;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BatimentTest extends TestCase
{
    public function test_classe_batiment_existe(): void
    {
        $this->assertTrue(class_exists(Batiment::class));
    }

    public function test_id_batiment(): void
    {
        $batiment = new Batiment();
        $this->assertNull($batiment->getId());
    }

    public function test_nom_batiment(): void
    {
        $batiment = new Batiment();
        $batiment->setNom('Batiment A');
        $this->assertEquals('Batiment A', $batiment->getNom());
    }

    public function test_batiment_a_salles(): void
    {
        $batiment = new Batiment();
        $this->assertInstanceOf(ArrayCollection::class, $batiment->getSalles());
    }

    public function test_batiment_ajout_salle() : void
    {
        $batiment = new Batiment();
        $salle = new Salle();
        $batiment->addSalle($salle);
        $this->assertTrue($batiment->getSalles()->contains($salle));
    }

    public function test_batiment_retirer_salles() : void
    {
        $batiment = new Batiment();
        $salle = new Salle();
        $batiment->removeSalle($salle);
        $this->assertFalse($batiment->getSalles()->contains($salle));
    }

    public function test_batiment_a_departement(): void
    {
        $batiment = new Batiment();
        $batiment->setDepartement('Informatique');
        $this->assertEquals('Informatique', $batiment->getDepartement());
    }

}
