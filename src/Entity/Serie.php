<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SerieRepository::class)
 */
class Serie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Poule::class, mappedBy="serie", cascade={"persist"})
     */
    private $poules;

    /**
     * @ORM\ManyToOne(targetEntity=Tournoi::class, inversedBy="series", cascade={"persist"})
     */
    private $tournoi;

    public function __construct()
    {
        $this->poules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Poule[]
     */
    public function getPoules(): Collection
    {
        return $this->poules;
    }

    public function getTournoi(): ?Tournoi
    {
        return $this->tournoi;
    }

    public function setTournoi(?Tournoi $tournoi): self
    {
        $this->tournoi = $tournoi;

        return $this;
    }

    public function addPoules(Poule $poules): self
    {
        if (!$this->poules->contains($poules)) {
            $this->poules[] = $poules;
            $poules->setSerie($this);
        }

        return $this;
    }

    public function removePoules(Poule $poules): self
    {
        if ($this->poules->contains($poules)) {
            $this->poules->removeElement($poules);
            // set the owning side to null (unless already changed)
            if ($poules->getSerie() === $this) {
                $poules->setSerie(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getLibelle();
    }

    public function addPoule(Poule $poule)
    {
        $poule->setSerie($this);

        $this->poules->add($poule);
    }

    public function removePoule(Poule $poule)
    {
        $this->poules->removeElement($poule);
    }

    public function __toString()
    {
        return (string) $this->getLibelle();
    }
}
