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
    private $dateEtHeure;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\ManyToOne(targetEntity=Tournoi::class, inversedBy="creneau")
     */
    private $tournoi;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="creneau")
     */
    private $user;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\OneToOne(targetEntity=Partie::class, cascade={"persist", "remove"})
     */
    private $partie;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEtHeure(): ?\DateTimeInterface
    {
        return $this->dateEtHeure;
    }

    public function setDateEtHeure(?\DateTimeInterface $dateEtHeure): self
    {
        $this->dateEtHeure = $dateEtHeure;

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

    public function getTournoi(): ?Tournoi
    {
        return $this->tournoi;
    }

    public function setTournoi(?Tournoi $tournoi): self
    {
        $this->tournoi = $tournoi;

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
    
    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getPartie(): ?Partie
    {
        return $this->partie;
    }

    public function setPartie(?Partie $partie): self
    {
        $this->partie = $partie;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getCommentaire();
    }
}
