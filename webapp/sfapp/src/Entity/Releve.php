<?php

namespace App\Entity;

use App\Repository\ReleveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Releve entité représentant un relevé de température, d'humidité et de qualité de l'air, avec horodatage dans la base de données
 */
#[ORM\Entity(repositoryClass: ReleveRepository::class)]
class Releve
{
    /**
     * @var int|null $id ID du relevé
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    /**
     * @var SystemeAcquisition|null $systemeAcquisition Système d'acquisition du relevé
     */
    #[ORM\ManyToOne(inversedBy: 'releves')]
    private ?SystemeAcquisition $systemeAcquisition = null;

    /**
     * @var float|null $temperature Température du relevé
     */
    #[ORM\Column(nullable: true)]
    private ?float $temperature = null;

    /**
     * @var int|null $humidite Humidité du relevé
     */
    #[ORM\Column(nullable: true)]
    private ?int $humidite = null;

    /**
     * @var int|null $qualiteAir Qualité de l'air du relevé
     */
    #[ORM\Column(nullable: true)]
    private ?int $qualiteAir = null;

    /**
     * @var Salle|null $salle Salle du relevé
     */
    #[ORM\ManyToOne(inversedBy: 'releves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Salle $salle = null;

    /**
     * @var \DateTimeInterface|null $horodatage Horodatage du relevé
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $horodatage = null;

    /**
     * @return int|null ID du relevé
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return SystemeAcquisition|null Système d'acquisition du relevé
     */
    public function getSystemeAcquisition(): ?SystemeAcquisition
    {
        return $this->systemeAcquisition;
    }

    /**
     * @param SystemeAcquisition|null $systemeAcquisition Système d'acquisition du relevé
     * @return $this Relevé
     */
    public function setSystemeAcquisition(?SystemeAcquisition $systemeAcquisition): static
    {
        $this->systemeAcquisition = $systemeAcquisition;

        return $this;
    }

    /**
     * @return float|null Température du relevé
     */
    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    /**
     * @param float|null $temperature Température du relevé
     * @return $this Relevé
     */
    public function setTemperature(?float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * @return int|null Humidité du relevé
     */
    public function getHumidite(): ?int
    {
        return $this->humidite;
    }

    /**
     * @param int|null $humidite Humidité du relevé
     * @return $this Relevé
     */
    public function setHumidite(?int $humidite): static
    {
        $this->humidite = $humidite;

        return $this;
    }

    /**
     * @return int|null Qualité de l'air du relevé
     */
    public function getQualiteAir(): ?int
    {
        return $this->qualiteAir;
    }

    /**
     * @param int|null $qualiteAir Qualité de l'air du relevé
     * @return $this Relevé
     */
    public function setQualiteAir(?int $qualiteAir): static
    {
        $this->qualiteAir = $qualiteAir;

        return $this;
    }

    /**
     * @return Salle|null Salle du relevé
     */
    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    /**
     * @param Salle|null $salle Salle du relevé
     * @return $this Relevé
     */
    public function setSalle(?Salle $salle): static
    {
        if($salle === null) {
            throw new \InvalidArgumentException('La salle ne peut pas être nulle');
        }
        $this->salle = $salle;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null Horodatage du relevé
     */
    public function getHorodatage(): ?\DateTimeInterface
    {
        return $this->horodatage;
    }

    /**
     * @param \DateTimeInterface $horodatage Horodatage du relevé
     * @return $this Relevé
     */
    public function setHorodatage(\DateTimeInterface $horodatage): static
    {
        $this->horodatage = $horodatage;

        return $this;
    }
}
