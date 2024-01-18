<?php

namespace App\Entity;

use App\Repository\SystemeAcquisitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SystemeAcquisitionRepository::class)]
#[UniqueEntity('nom', message: 'Nom déjà enregistré')]
#[UniqueEntity('baseDonnees', message: 'Base de données déjà enregistré')]
class SystemeAcquisition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'systemeAcquisition', cascade: ['persist'])]
    private ?Salle $salle = null;

    #[ORM\Column(length: 7, unique: true)]
    #[Assert\Regex(pattern: '/^ESP\-[0-9]{3}$/', message: 'Nom invalide. Acceptés ESP-XXX avec X un chiffre')]
    private ?string $nom = null;

    #[ORM\Column(length: 12, unique: true)]
    private ?string $baseDonnees = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\OneToMany(mappedBy: 'systemeAcquisition', targetEntity: DemandeTravaux::class)]
    private Collection $demandesTravaux;

    public function __construct()
    {
        $this->demandesTravaux = new ArrayCollection();
    }

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getBaseDonnees(): ?string
    {
        return $this->baseDonnees;
    }

    public function setBaseDonnees(string $baseDonnees): static
    {
        $this->baseDonnees = $baseDonnees;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

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
            $demandesTravaux->setSystemeAcquisition($this);
        }

        return $this;
    }

    public function removeDemandesTravaux(DemandeTravaux $demandesTravaux): static
    {
        if ($this->demandesTravaux->removeElement($demandesTravaux)) {
            // set the owning side to null (unless already changed)
            if ($demandesTravaux->getSystemeAcquisition() === $this) {
                $demandesTravaux->setSystemeAcquisition(null);
            }
        }

        return $this;
    }
}
