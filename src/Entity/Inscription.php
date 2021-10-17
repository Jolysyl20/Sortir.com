<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionRepository::class)
 */
class Inscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateInscription;

    /**
     * @ORM\ManyToOne(targetEntity=Sortie::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $noSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $noParticipant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getNoSortie(): ?Sortie
    {
        return $this->noSortie;
    }

    public function setNoSortie(?Sortie $noSortie): self
    {
        $this->noSortie = $noSortie;

        return $this;
    }

    public function getNoParticipant(): ?Participant
    {
        return $this->noParticipant;
    }

    public function setNoParticipant(?Participant $noParticipant): self
    {
        $this->noParticipant = $noParticipant;

        return $this;
    }


}
