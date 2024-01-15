<?php

namespace App\Entity;

use App\Repository\DemandeTravauxRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: DemandeTravauxRepository::class)]
class DemandeTravaux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 12)]
    private ?string $type = null;

    #[ORM\Column]
    private ?bool $terminee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'demandesTravaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Salle $salle = null;

    #[ORM\ManyToOne(inversedBy: 'demandesTravaux')]
    private ?SystemeAcquisition $systemeAcquisition = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: "Veuillez saisir un email valide")]
    #[Assert\NotBlank(message: "Veuillez saisir un email")]
    private ?string $emailDemandeur = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez saisir un commentaire")]
    #[Assert\Length(max: 255, maxMessage: "Le commentaire ne doit pas dépasser {{ limit }} caractères")]
    #[Assert\Length(min: 10, minMessage: "Le commentaire doit faire au moins {{ limit }} caractères")]
    private ?string $commentaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isTerminee(): ?bool
    {
        return $this->terminee;
    }

    public function setTerminee(bool $terminee): static
    {
        $this->terminee = $terminee;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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

    public function getSystemeAcquisition(): ?SystemeAcquisition
    {
        return $this->systemeAcquisition;
    }

    public function setSystemeAcquisition(?SystemeAcquisition $systemeAcquisition): static
    {
        $this->systemeAcquisition = $systemeAcquisition;

        return $this;
    }

    public function getEmailDemandeur(): ?string
    {
        return $this->emailDemandeur;
    }

    public function setEmailDemandeur(?string $emailDemandeur): static
    {
        $this->emailDemandeur = $emailDemandeur;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
