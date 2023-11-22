<?php

namespace App\Tests\Entity;

use App\Entity\Salle;
use App\Entity\SystemeAcquisition;
use PHPUnit\Framework\TestCase;

class SystemeAcquisitionTest extends TestCase
{
    public function test_un_systeme_d_acquisition_a_une_adresse_mac(): void
    {
        $systeme = new SystemeAcquisition();
        $systeme->setAdresseMac("44:55:66:77:88:99");
        $this->assertEquals("44:55:66:77:88:99",$systeme->getAdresseMac());
    }

    public function test_un_systeme_d_acquisition_est_dans_une_salle(): void
    {
        $systeme = new SystemeAcquisition();
        $salle = $this->createMock(Salle::class);
        $systeme->setSalle($salle);
        $this->assertEquals($salle,$systeme->getSalle());
    }
}
