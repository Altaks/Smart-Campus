<?php

namespace App\Tests\Entity;

use App\Entity\Batiment;
use App\Entity\Releve;
use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ReleveTest extends TestCase
{
    public function test_classe_systeme_acquisition_existe(): void
    {
        $this->assertTrue(class_exists(Releve::class));
    }

    public function test_id_releve(): void
    {
        $releve = new Releve();
        $this->assertNull($releve->getId());
    }

    public function test_systeme_acquisition_releve_vide() : void
    {
        $releve = new Releve();
        $this->assertNull($releve->getSystemeAcquisition());
    }

    public function test_systeme_acquisition_releve() : void
    {
        $releve = new Releve();
        $sa = new SystemeAcquisition();
        $releve->setSystemeAcquisition($sa);
        $this->assertEquals($sa, $releve->getSystemeAcquisition());
    }

    public function test_retrait_systeme_acquisition_releve() : void
    {
        $releve = new Releve();
        $sa = new SystemeAcquisition();
        $releve->setSystemeAcquisition($sa);
        $releve->setSystemeAcquisition(null);
        $this->assertNull($releve->getSystemeAcquisition());
    }

    public function test_temperature_non_recue_releve() : void
    {
        $releve = new Releve();
        $this->assertNull($releve->getTemperature());
    }

    public function test_humidite_non_recue_releve() : void
    {
        $releve = new Releve();
        $this->assertNull($releve->getHumidite());
    }

    public function test_qualite_air_non_recue_releve() : void
    {
        $releve = new Releve();
        $this->assertNull($releve->getQualiteAir());
    }

    public function test_horodatage_vide_releve() : void
    {
        $releve = new Releve();
        $this->assertNull($releve->getHorodatage());
    }

    public function test_temperature_releve() : void
    {
        $releve = new Releve();
        $releve->setTemperature(20.6);
        $this->assertEquals(20.6, $releve->getTemperature());
    }

    public function test_humidite_releve() : void
    {
        $releve = new Releve();
        $releve->setHumidite(50);
        $this->assertEquals(50, $releve->getHumidite());
    }

    public function test_qualite_air_releve() : void
    {
        $releve = new Releve();
        $releve->setQualiteAir(400);
        $this->assertEquals(400, $releve->getQualiteAir());
    }

    public function test_salle_releve() : void
    {
        $releve = new Releve();
        $salle = new Salle();
        $releve->setSalle($salle);
        $this->assertEquals($salle, $releve->getSalle());
    }

    public function test_salle_ne_peut_pas_etre_null() : void
    {
        $releve = new Releve();
        $this->expectException(\InvalidArgumentException::class);
        $releve->setSalle(null);
        $this->assertEquals(null, $releve->getSalle());
    }



}
