<?php

namespace App\Entity;

use App\Repository\SystemeAcquisitionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SystemeAcquisitionRepository::class)]
class SystemeAcquisition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 17)]
    private ?string $adresse_mac = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Salle $salle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresseMac(): ?string
    {
        return $this->adresse_mac;
    }

    public function setAdresseMac(string $adresse_mac): static
    {
        $this->adresse_mac = $adresse_mac;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static
    {
        $this->salle = $salle;

        return $this;
    }
}
