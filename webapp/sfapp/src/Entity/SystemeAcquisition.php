<?php

namespace App\Entity;

use App\Repository\SystemeAcquisitionRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * Class SystemeAcquisition entité représentant un système d'acquisition dans la base de données
 */
#[ORM\Entity(repositoryClass: SystemeAcquisitionRepository::class)]
class SystemeAcquisition
{
    /**
     * @var int|null $id ID du système d'acquisition
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Salle|null $salle Salle du système d'acquisition
     */
    #[ORM\OneToOne(mappedBy: 'systemeAcquisition', cascade: ['persist', 'remove'])]
    private ?Salle $salle = null;

    /**
     * @var string|null $adresseMac Adresse MAC du système d'acquisition
     */
    #[ORM\Column(length: 17)]
    private ?string $adresseMac = null;

    /**
     * @return int|null ID du système d'acquisition
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Salle|null Salle du système d'acquisition
     */
    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    /**
     * @param Salle|null $salle Salle du système d'acquisition
     * @return SystemeAcquisition
     */
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

    /**
     * @return string|null Adresse MAC du système d'acquisition
     */
    public function getAdresseMac(): ?string
    {
        return $this->adresseMac;
    }

    /**
     * @param string $adresseMac Adresse MAC du système d'acquisition
     * @return SystemeAcquisition
     */
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
