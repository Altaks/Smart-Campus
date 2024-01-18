<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SalleRepository::class)]
#[UniqueEntity('nom', message: 'Une salle avec le même nom existe déjà')]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Une salle doit avoir un nom')]
    private ?string $nom = null;

    #[ORM\Column(length: 2)]
    private ?string $orientation = null;

    #[ORM\Column]
    private ?int $nombreFenetre = null;

    #[ORM\Column]
    private ?int $nombrePorte = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La valeur doit être \'Oui\' ou \'Non\'')]
    private ?bool $contientPc = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\NotBlank(message: 'Une salle doit avoir un bâtiment')]
    private ?string $batiment = null;

    #[ORM\OneToOne(inversedBy: 'salle', cascade: ['persist', 'remove'])]
    private ?SystemeAcquisition $systemeAcquisition = null;

    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: DemandeTravaux::class)]
    private Collection $demandesTravaux;

    public function __construct()
    {
        $this->demandesTravaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getNombreFenetre(): ?int
    {
        return $this->nombreFenetre;
    }

    public function setNombreFenetre(int $nombreFenetre): static
    {
        $this->nombreFenetre = $nombreFenetre;

        return $this;
    }

    public function getNombrePorte(): ?int
    {
        return $this->nombrePorte;
    }

    public function setNombrePorte(int $nombrePorte): static
    {
        $this->nombrePorte = $nombrePorte;

        return $this;
    }

    public function isContientPc(): ?bool
    {
        return $this->contientPc;
    }

    public function setContientPc(bool $contientPc): static
    {
        $this->contientPc = $contientPc;

        return $this;
    }

    public function getBatiment(): ?string
    {
        return $this->batiment;
    }

    public function setBatiment(?string $batiment): static
    {
        $this->batiment = $batiment;

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

    /**
     * @return Collection<int, DemandeTravaux>
     */
    public function getDemandesTravaux(): Collection
    {
        return $this->demandesTravaux;
    }

    public function addDemandesTravaux(DemandeTravaux $demandesTravaux): static
    {
        if (!$this->demandesTravaux->contains($demandesTravaux)) {
            $this->demandesTravaux->add($demandesTravaux);
            $demandesTravaux->setSalle($this);
        }

        return $this;
    }

    public function removeDemandesTravaux(DemandeTravaux $demandesTravaux): static
    {
        if ($this->demandesTravaux->removeElement($demandesTravaux)) {
            // set the owning side to null (unless already changed)
            if ($demandesTravaux->getSalle() === $this) {
                $demandesTravaux->setSalle(null);
            }
        }

        return $this;
    }
}
