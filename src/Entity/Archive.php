<?php

namespace App\Entity;

use App\Repository\ArchiveRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArchiveRepository::class)
 */
class Archive
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom_lieu;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $etat_sortie;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom_site;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom_sortie;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_debut_sortie;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree_sortie;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_cloture_inscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nn_inscription_max_sortie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description_sortie;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom_organisateur;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email_organisateur;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $participants_inscrit = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomLieu(): ?string
    {
        return $this->nom_lieu;
    }

    public function setNomLieu(string $nom_lieu): self
    {
        $this->nom_lieu = $nom_lieu;

        return $this;
    }

    public function getEtatSortie(): ?string
    {
        return $this->etat_sortie;
    }

    public function setEtatSortie(string $etat_sortie): self
    {
        $this->etat_sortie = $etat_sortie;

        return $this;
    }

    public function getNomSite(): ?string
    {
        return $this->nom_site;
    }

    public function setNomSite(string $nom_site): self
    {
        $this->nom_site = $nom_site;

        return $this;
    }

    public function getNomSortie(): ?string
    {
        return $this->nom_sortie;
    }

    public function setNomSortie(string $nom_sortie): self
    {
        $this->nom_sortie = $nom_sortie;

        return $this;
    }

    public function getDateDebutSortie(): ?\DateTimeInterface
    {
        return $this->date_debut_sortie;
    }

    public function setDateDebutSortie(\DateTimeInterface $date_debut_sortie): self
    {
        $this->date_debut_sortie = $date_debut_sortie;

        return $this;
    }

    public function getDureeSortie(): ?int
    {
        return $this->duree_sortie;
    }

    public function setDureeSortie(int $duree_sortie): self
    {
        $this->duree_sortie = $duree_sortie;

        return $this;
    }

    public function getDateClotureInscription(): ?\DateTimeInterface
    {
        return $this->date_cloture_inscription;
    }

    public function setDateClotureInscription(\DateTimeInterface $date_cloture_inscription): self
    {
        $this->date_cloture_inscription = $date_cloture_inscription;

        return $this;
    }

    public function getNnInscriptionMaxSortie(): ?int
    {
        return $this->nn_inscription_max_sortie;
    }

    public function setNnInscriptionMaxSortie(int $nn_inscription_max_sortie): self
    {
        $this->nn_inscription_max_sortie = $nn_inscription_max_sortie;

        return $this;
    }

    public function getDescriptionSortie(): ?string
    {
        return $this->description_sortie;
    }

    public function setDescriptionSortie(?string $description_sortie): self
    {
        $this->description_sortie = $description_sortie;

        return $this;
    }

    public function getNomOrganisateur(): ?string
    {
        return $this->nom_organisateur;
    }

    public function setNomOrganisateur(string $nom_organisateur): self
    {
        $this->nom_organisateur = $nom_organisateur;

        return $this;
    }

    public function getEmailOrganisateur(): ?string
    {
        return $this->email_organisateur;
    }

    public function setEmailOrganisateur(string $email_organisateur): self
    {
        $this->email_organisateur = $email_organisateur;

        return $this;
    }

    public function getParticipantsInscrit(): ?array
    {
        return $this->participants_inscrit;
    }

    public function setParticipantsInscrit(array $participants_inscrit): self
    {
        $this->participants_inscrit = $participants_inscrit;

        return $this;
    }
}
