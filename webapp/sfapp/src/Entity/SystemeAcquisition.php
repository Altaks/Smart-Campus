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

    #[ORM\OneToOne(mappedBy: 'systemeAcquisition', cascade: ['persist', 'remove'])]
    private ?Salle $salle = null;

    #[ORM\Column(length: 11)]
    private ?string $adresseMac = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static
    {
        // unset the owning side of the relation if necessary
        if ($salle === null && $this->salle !== null) {
            $this->salle->setSystemeAcquisition(null);
        }

        // set the owning side of the relation if necessary
        if ($salle !== null && $salle->getSystemeAcquisition() !== $this) {
            $salle->setSystemeAcquisition($this);
        }

        $this->salle = $salle;

        return $this;
    }

    public function getAdresseMac(): ?string
    {
        return $this->adresseMac;
    }

    public function setAdresseMac(string $adresseMac): static
    {
        $this->adresseMac = $adresseMac;

        return $this;
    }
}
