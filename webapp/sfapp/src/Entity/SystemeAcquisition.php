<?php

namespace App\Entity;

use App\Repository\SystemeAcquisitionRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: SystemeAcquisitionRepository::class)]
class SystemeAcquisition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'systemeAcquisition', cascade: ['persist', 'remove'])]
    private ?Salle $salle = null;

    #[ORM\Column(length: 17)]
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
        if(strlen($adresseMac) != 17) {
            throw new InvalidArgumentException("L'adresse MAC doit contenir 17 caractères");
        }

        // Vérifier si l'adresse mac correspond a la regex : ([a-fA-F0-9]{2}\:){5}[a-fA-F0-9]{2}
        if (!preg_match('/([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}/', $adresseMac)) {
            throw new InvalidArgumentException("L'adresse mac n'est pas valide");
        }
        $this->adresseMac = $adresseMac;

        return $this;
    }
}
