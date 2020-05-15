<?php

namespace App\Entity;

use App\Repository\CreneauRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CreneauRepository::class)
 */
class Creneau
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $laDate;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\ManyToOne(targetEntity=Tournoi::class, inversedBy="creneau")
     */
    private $tournoi;

  

    /**
     * @ORM\OneToOne(targetEntity=Partie::class, mappedBy="creneau", cascade={"persist", "remove"})
     */
    private $partie;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="creneau")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLaDate(): ?\DateTimeInterface
    {
        return $this->laDate;
    }

    public function setLaDate(?\DateTimeInterface $laDate): self
    {
        $this->laDate = $laDate;

        return $this;
    }

    public function getHeureDebut(): ?string
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?string $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getMinuteDebut(): ?string
    {
        return $this->minuteDebut;
    }

    public function setMinuteDebut(?string $minuteDebut): self
    {
        $this->minuteDebut = $minuteDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
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

  

    public function getPartie(): ?Partie
    {
        return $this->partie;
    }

    public function setPartie(?Partie $partie): self
    {
        $this->partie = $partie;

        // set (or unset) the owning side of the relation if necessary
        $newCreneau = null === $partie ? null : $this;
        if ($partie->getCreneau() !== $newCreneau) {
            $partie->setCreneau($newCreneau);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
