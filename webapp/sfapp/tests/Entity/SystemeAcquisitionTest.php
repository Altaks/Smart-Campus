<?php

namespace App\Tests\Entity;

use App\Entity\Batiment;
use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class SystemeAcquisitionTest extends TestCase
{
    public function test_classe_systeme_acquisition_existe(): void
    {
        $this->assertTrue(class_exists(SystemeAcquisition::class));
    }

    public function test_id_systeme_acquisition(): void
    {
        $sa = new SystemeAcquisition();
        $this->assertNull($sa->getId());
    }

    public function test_adresse_mac_systeme_acquisition(): void
    {
        $sa = new SystemeAcquisition();
        $sa->setAdresseMac('00:00:00:00:00:01');
        $this->assertEquals('00:00:00:00:00:01', $sa->getAdresseMac());
    }

    public function test_adresse_mac_systeme_acquisition_taille(): void
    {
        $sa = new SystemeAcquisition();
        $this->expectException(\InvalidArgumentException::class);
        $sa->setAdresseMac('00:00:00:00:00:0');
        $this->assertEquals(null, $sa->getAdresseMac());
    }

    public function test_adresse_mac_systeme_acquisition_format(): void
    {
        $sa = new SystemeAcquisition();
        $this->expectException(\InvalidArgumentException::class);
        $sa->setAdresseMac('00:00:00:00:00:0G');
        $this->assertEquals(null, $sa->getAdresseMac());
    }

}
