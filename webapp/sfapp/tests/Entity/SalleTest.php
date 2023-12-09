<?php

namespace App\Tests\Entity;

use App\Entity\Batiment;
use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class SalleTest extends TestCase
{
    public function test_classe_salle_existe(): void
    {
        $this->assertTrue(class_exists(Salle::class));
    }

    public function test_id_vide_batiment(): void
    {
        $salle = new Salle();
        $this->assertNull($salle->getId());
    }

    public function test_nom_salle(): void
    {
        $salle = new Salle();
        $salle->setNom('302');
        $this->assertEquals('302', $salle->getNom());
    }

    public function test_taille_min_nom_salle(): void
    {
        $salle = new Salle();
        $this->expectException(\InvalidArgumentException::class);
        $salle->setNom('30');
        $this->assertEquals(null, $salle->getNom());
    }

    public function test_etage_salle(): void
    {
        $salle = new Salle();
        $salle->setNom('302');
        $this->assertEquals('3', $salle->getEtage());
    }

    public function test_orientation_salle_Nord(): void
    {
        $salle = new Salle();
        $salle->setOrientation("No");
        $this->assertEquals('No', $salle->getOrientation());
    }

    public function test_orientation_salle_Nord_Est(): void
    {
        $salle = new Salle();
        $salle->setOrientation("NE");
        $this->assertEquals('NE', $salle->getOrientation());
    }

    public function test_orientation_salle_Est(): void
    {
        $salle = new Salle();
        $salle->setOrientation("Es");
        $this->assertEquals('Es', $salle->getOrientation());
    }

    public function test_orientation_salle_Sud_Est(): void
    {
        $salle = new Salle();
        $salle->setOrientation("SE");
        $this->assertEquals('SE', $salle->getOrientation());
    }

    public function test_orientation_salle_Sud(): void
    {
        $salle = new Salle();
        $salle->setOrientation("Su");
        $this->assertEquals('Su', $salle->getOrientation());
    }

    public function test_orientation_salle_Sud_Ouest(): void
    {
        $salle = new Salle();
        $salle->setOrientation("SO");
        $this->assertEquals('SO', $salle->getOrientation());
    }

    public function test_orientation_salle_Ouest(): void
    {
        $salle = new Salle();
        $salle->setOrientation("Ou");
        $this->assertEquals('Ou', $salle->getOrientation());
    }

    public function test_orientation_salle_Nord_Ouest(): void
    {
        $salle = new Salle();
        $salle->setOrientation("NO");
        $this->assertEquals('NO', $salle->getOrientation());
    }

    public function test_orientation_salle_ne_peut_pas_faire_plus_de_deux_caracteres(): void
    {
        $salle = new Salle();
        $this->expectException(\InvalidArgumentException::class);
        $salle->setOrientation("Plus de deux caracteres");
        $this->assertEquals(null, $salle->getOrientation());
    }

    public function test_orientation_salle_ne_peut_pas_faire_moins_de_deux_caracteres(): void
    {
        $salle = new Salle();
        $this->expectException(\InvalidArgumentException::class);
        $salle->setOrientation("1");
        $this->assertEquals(null, $salle->getOrientation());
    }

    public function test_orientation_salle_ne_peut_pas_etre_autre_que_directions_rose_des_vents(): void
    {
        $salle = new Salle();
        $this->expectException(\InvalidArgumentException::class);
        $salle->setOrientation("NA");
        $this->assertEquals(null, $salle->getOrientation());
    }

    public function test_nombre_fenetres() : void
    {
        $salle = new Salle();
        $salle->setNbfenetres(6);
        $this->assertEquals(6, $salle->getNbfenetres());
    }

    public function test_nombre_fenetres_ne_peut_pas_etre_negatif() : void
    {
        $salle = new Salle();
        $this->expectException(\InvalidArgumentException::class);
        $salle->setNbfenetres(-1);
        $this->assertEquals(null, $salle->getNbfenetres());
    }

    public function test_nombre_portes() : void
    {
        $salle = new Salle();
        $salle->setNbportes(2);
        $this->assertEquals(2, $salle->getNbportes());
    }

    public function test_nombre_portes_ne_peut_pas_etre_negatif() : void
    {
        $salle = new Salle();
        $this->expectException(\InvalidArgumentException::class);
        $salle->setNbportes(-1);
        $this->assertEquals(null, $salle->getNbportes());
    }

    public function test_ne_contient_pas_pc_par_defaut() : void
    {
        $salle = new Salle();
        $this->assertFalse($salle->getContientpc());
    }

    public function test_contient_pc() : void
    {
        $salle = new Salle();
        $salle->setContientpc(true);
        $this->assertTrue($salle->getContientpc());
    }

    public function test_systeme_acquisition_peut_etre_null() : void
    {
        $salle = new Salle();
        $this->assertNull($salle->getSystemeAcquisition());
    }

    public function test_systeme_acquisition() : void
    {
        $salle = new Salle();
        $sa = new SystemeAcquisition();
        $salle->setSystemeAcquisition($sa);
        $this->assertEquals($sa, $salle->getSystemeAcquisition());
    }

    public function test_systeme_acquisition_peut_etre_retire()
    {
        $salle = new Salle();
        $sa = new SystemeAcquisition();
        $salle->setSystemeAcquisition($sa);
        $this->assertEquals($sa, $salle->getSystemeAcquisition());
        $salle->setSystemeAcquisition(null);
        $this->assertNull($salle->getSystemeAcquisition());
    }

    public function test_salle_a_batiment()
    {
        $salle = new Salle();
        $batiment = new Batiment();
        $salle->setBatiment($batiment);
        $this->assertEquals($batiment, $salle->getBatiment());
    }



}
