<?php

namespace App\Entity;

use App\Repository\PartieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PartieRepository::class)
 */
class Partie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreEquipe1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreEquipe2;

    /**
     * @ORM\ManyToMany(targetEntity=Equipe::class, mappedBy="partie")
     */
    private $equipes;

    /**
     * @ORM\OneToOne(targetEntity=Creneau::class, cascade={"persist", "remove"})
     */
    private $creneau;

 

    public function __construct()
    {
        $this->equipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScoreEquipe1(): ?int
    {
        return $this->scoreEquipe1;
    }

    public function setScoreEquipe1(?int $scoreEquipe1): self
    {
        $this->scoreEquipe1 = $scoreEquipe1;

        return $this;
    }

    public function getScoreEquipe2(): ?int
    {
        return $this->scoreEquipe2;
    }

    public function setScoreEquipe2(?int $scoreEquipe2): self
    {
        $this->scoreEquipe2 = $scoreEquipe2;

        return $this;
    }

    /**
     * @return Collection|Equipe[]
     */
    public function getEquipes(): Collection
    {
        return $this->equipes;
    }

    public function addEquipe(Equipe $equipe): self
    {
        if (!$this->equipes->contains($equipe)) {
            $this->equipes[] = $equipe;
            $equipe->addPartie($this);
        }

        return $this;
    }

    public function removeEquipe(Equipe $equipe): self
    {
        if ($this->equipes->contains($equipe)) {
            $this->equipes->removeElement($equipe);
            $equipe->removePartie($this);
        }

        return $this;
    }

    public function getCreneau(): ?Creneau
    {
        return $this->creneau;
    }

    public function __toString()
    {
        return (string) $this->getCreneau();
    }

    public function setCreneau(?Creneau $creneau): self
    {
        $this->creneau = $creneau;

        // set (or unset) the owning side of the relation if necessary
        $newPartie = null === $creneau ? null : $this;
        if ($creneau->getPartie() !== $newPartie) {
            $creneau->setPartie($newPartie);
        }

        return $this;
    }

   

}
