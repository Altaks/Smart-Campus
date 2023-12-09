<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'salles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Batiment $batiment = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $etage = null;

    #[ORM\Column(length: 2)]
    private ?string $orientation = null;

    #[ORM\Column]
    private ?int $nombreFenetre = null;

    #[ORM\Column]
    private ?int $nombrePorte = null;

    #[ORM\Column]
    private ?bool $contientPc = false;

    #[ORM\OneToOne(inversedBy: 'salle', cascade: ['persist', 'remove'])]
    private ?SystemeAcquisition $systemeAcquisition = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        if($nom === null || strlen($nom) < 3)
            throw new InvalidArgumentException("Le nom de la salle doit contenir au moins 3 caractères");
        $this->nom = $nom;

        // Récupérer le chiffre des centaines et le convertir en étage
        $this->etage = intval(substr($nom, 0, 1));

        return $this;
    }

    public function getEtage(): ?int
    {
        return $this->etage;
    }

    public function setEtage(int $etage): static
    {
        $this->etage = $etage;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): static
    {
        if(strlen($orientation) < 2) throw new InvalidArgumentException("L'orientation ne peut pas avoir moins de 2 caractères");
        if(strlen($orientation) > 2) throw new InvalidArgumentException("L'orientation ne peut pas avoir plus de 2 caractères");

        if(!in_array($orientation, ['No', 'Su', 'Es', 'Ou', 'NE', 'SE', 'SO', 'NO'])){
            throw new InvalidArgumentException("L'orientation doit être Nord, Sud, Est ou Ouest");
        }

        $this->orientation = $orientation;

        return $this;
    }

    public function getNbfenetres(): ?int
    {
        return $this->nombreFenetre;
    }

    public function setNbfenetres(int $nombreFenetre): static
    {
        if($nombreFenetre < 0) throw new InvalidArgumentException("Le nombre de fenêtres ne peut pas être négatif");

        $this->nombreFenetre = $nombreFenetre;
        return $this;
    }

    public function getNbportes(): ?int
    {
        return $this->nombrePorte;
    }

    public function setNbportes(int $nombrePorte): static
    {
        if($nombrePorte < 0) throw new InvalidArgumentException("Le nombre de portes ne peut pas être négatif");

        $this->nombrePorte = $nombrePorte;
        return $this;
    }

    public function getContientpc(): ?bool
    {
        return $this->contientPc;
    }

    public function setContientPc(bool $contientPc): static
    {
        $this->contientPc = $contientPc;

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
}
