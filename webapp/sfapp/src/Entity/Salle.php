<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * Class Salle entité représentant une salle de l'IUT dans la base de données
 */
#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    /**
     * @var int|null $id ID de la salle
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Batiment|null $batiment Batiment de la salle
     */
    #[ORM\ManyToOne(inversedBy: 'salles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Batiment $batiment = null;

    /**
     * @var string|null $nom Nom de la salle
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var int|null $etage Etage de la salle
     */
    #[ORM\Column]
    private ?int $etage = null;

    /**
     * @var string|null $orientation Orientation de la salle
     */
    #[ORM\Column(length: 2)]
    private ?string $orientation = null;

    /**
     * @var int|null $nombreFenetre Nombre de fenêtres de la salle
     */
    #[ORM\Column]
    private ?int $nombreFenetre = null;

    /**
     * @var int|null $nombrePorte Nombre de portes de la salle
     */
    #[ORM\Column]
    private ?int $nombrePorte = null;

    /**
     * @var bool|null $contientPc Indicateur de présence d'un PC dans la salle
     */
    #[ORM\Column]
    private ?bool $contientPc = false;

    /**
     * @var SystemeAcquisition|null $systemeAcquisition Système d'acquisition de la salle
     */
    #[ORM\OneToOne(inversedBy: 'salle', cascade: ['persist', 'remove'])]
    private ?SystemeAcquisition $systemeAcquisition = null;

    /**
     * @return int|null ID de la salle
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Batiment|null Batiment de la salle
     */
    public function getBatiment(): ?Batiment
    {
        return $this->batiment;
    }

    /**
     * @param Batiment|null $batiment Batiment de la salle
     * @return $this Salle
     */
    public function setBatiment(?Batiment $batiment): static
    {
        $this->batiment = $batiment;

        return $this;
    }

    /**
     * @return string|null Nom de la salle
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string $nom Nom de la salle
     * @return $this Salle
     */
    public function setNom(string $nom): static
    {
        if($nom === null || strlen($nom) < 3)
            throw new InvalidArgumentException("Le nom de la salle doit contenir au moins 3 caractères");
        $this->nom = $nom;

        // Récupérer le chiffre des centaines et le convertir en étage
        $this->etage = intval(substr($nom, 0, 1));

        return $this;
    }

    /**
     * @return int|null Etage de la salle
     */
    public function getEtage(): ?int
    {
        return $this->etage;
    }

    /**
     * @param int $etage Etage de la salle
     * @return $this Salle
     */
    public function setEtage(int $etage): static
    {
        $this->etage = $etage;

        return $this;
    }

    /**
     * @return string|null Orientation de la salle
     */
    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    /**
     * @param string $orientation Orientation de la salle
     * @return $this Salle
     */
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

    /**
     * @return int|null Nombre de fenêtres de la salle
     */
    public function getNbfenetres(): ?int
    {
        return $this->nombreFenetre;
    }

    /**
     * @param int $nombreFenetre Nombre de fenêtres de la salle
     * @return $this Salle
     */
    public function setNbfenetres(int $nombreFenetre): static
    {
        if($nombreFenetre < 0) throw new InvalidArgumentException("Le nombre de fenêtres ne peut pas être négatif");

        $this->nombreFenetre = $nombreFenetre;
        return $this;
    }

    /**
     * @return int|null Nombre de portes de la salle
     */
    public function getNbportes(): ?int
    {
        return $this->nombrePorte;
    }

    /**
     * @param int $nombrePorte Nombre de portes de la salle
     * @return $this Salle
     */
    public function setNbportes(int $nombrePorte): static
    {
        if($nombrePorte < 0) throw new InvalidArgumentException("Le nombre de portes ne peut pas être négatif");

        $this->nombrePorte = $nombrePorte;
        return $this;
    }

    /**
     * @return bool|null Indicateur de présence d'un PC dans la salle
     */
    public function getContientpc(): ?bool
    {
        return $this->contientPc;
    }

    /**
     * @param bool $contientPc Indicateur de présence d'un PC dans la salle
     * @return $this Salle
     */
    public function setContientPc(bool $contientPc): static
    {
        $this->contientPc = $contientPc;

        return $this;
    }

    /**
     * @return SystemeAcquisition|null Système d'acquisition de la salle
     */
    public function getSystemeAcquisition(): ?SystemeAcquisition
    {
        return $this->systemeAcquisition;
    }

    /**
     * @param SystemeAcquisition|null $systemeAcquisition Système d'acquisition de la salle
     * @return $this Salle
     */
    public function setSystemeAcquisition(?SystemeAcquisition $systemeAcquisition): static
    {
        $this->systemeAcquisition = $systemeAcquisition;

        return $this;
    }
}
