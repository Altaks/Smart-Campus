<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    private ?string $numero = null;

    #[ORM\Column(length: 2)]
    private ?string $orientation = null;

    #[ORM\Column]
    private ?int $nbfenetres = null;

    #[ORM\Column]
    private ?int $nbportes = null;

    #[ORM\Column]
    private ?bool $contientpc = null;

    #[ORM\ManyToOne(inversedBy: 'salles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Batiment $batiment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): static
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getNbfenetres(): ?int
    {
        return $this->nbfenetres;
    }

    public function setNbfenetres(int $nbfenetres): static
    {
        $this->nbfenetres = $nbfenetres;

        return $this;
    }

    public function getNbportes(): ?int
    {
        return $this->nbportes;
    }

    public function setNbportes(int $nbportes): static
    {
        $this->nbportes = $nbportes;

        return $this;
    }

    public function isContientpc(): ?bool
    {
        return $this->contientpc;
    }

    public function setContientpc(bool $contientpc): static
    {
        $this->contientpc = $contientpc;

        return $this;
    }

    public function getBatiment(): ?Batiment
    {
        return $this->batiment;
    }

    public function setBatiment(?Batiment $batiment): static
    {
        $this->batiment = $batiment;

        return $this;
    }
}
