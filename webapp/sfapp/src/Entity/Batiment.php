<?php

namespace App\Entity;

use App\Repository\BatimentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Batiment entité représentant un batiment de l'IUT dans la base de données
 */
#[ORM\Entity(repositoryClass: BatimentRepository::class)]
class Batiment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null $nom Nom du batiment
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection|ArrayCollection $salles Salles du batiment
     */
    #[ORM\OneToMany(mappedBy: 'batiment', targetEntity: Salle::class, orphanRemoval: true)]
    private Collection $salles;


    /**
     * Batiment constructeur.
     */
    public function __construct()
    {
        $this->salles = new ArrayCollection();
    }

    /**
     * @return int|null ID du batiment
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return string|null Nom du batiment
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }


    /**
     * @param string $nom Nom du batiment
     * @return $this Batiment
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Salle> Salles du batiment
     */
    public function getSalles(): Collection
    {
        return $this->salles;
    }

    /**
     * @param Salle $salle Salle à ajouter
     * @return $this Batiment
     */
    public function addSalle(Salle $salle): static
    {
        if (!$this->salles->contains($salle)) {
            $this->salles->add($salle);
            $salle->setBatiment($this);
        }

        return $this;
    }

    /**
     * @param Salle $salle Salle à supprimer
     * @return $this Batiment
     */
    public function removeSalle(Salle $salle): static
    {
        if ($this->salles->removeElement($salle)) {
            // set the owning side to null (unless already changed)
            if ($salle->getBatiment() === $this) {
                $salle->setBatiment(null);
            }
        }

        return $this;
    }
}
