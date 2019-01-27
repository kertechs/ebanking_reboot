<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\IpTraceable\Traits\IpTraceableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AgencesRepository")
 */
class Agences
{
    use IpTraceableEntity;
    use BlameableEntity;
    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code_agence;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Banques", inversedBy="agences")
     */
    private $banque_id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comptes", mappedBy="agence_id")
     */
    private $comptes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comptes")
     */
    private $agence_compte_id;

    public function __construct()
    {
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCompteId(): ?int
    {
        return $this->compte_id;
    }

    public function setCompteId(?int $compte_id): self
    {
        $this->compte_id = $compte_id;

        return $this;
    }

    public function getCodeAgence(): ?string
    {
        return $this->code_agence;
    }

    public function setCodeAgence(?string $code_agence): self
    {
        $this->code_agence = $code_agence;

        return $this;
    }

    public function getBanqueId(): ?Banques
    {
        return $this->banque_id;
    }

    public function setBanqueId(?Banques $banque_id): self
    {
        $this->banque_id = $banque_id;

        return $this;
    }

    /**
     * @return Collection|Comptes[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Comptes $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setAgenceId($this);
        }

        return $this;
    }

    public function removeCompte(Comptes $compte): self
    {
        if ($this->comptes->contains($compte)) {
            $this->comptes->removeElement($compte);
            // set the owning side to null (unless already changed)
            if ($compte->getAgenceId() === $this) {
                $compte->setAgenceId(null);
            }
        }

        return $this;
    }

    public function getAgenceCompteId(): ?Comptes
    {
        return $this->agence_compte_id;
    }

    public function setAgenceCompteId(?Comptes $agence_compte_id): self
    {
        $this->agence_compte_id = $agence_compte_id;

        return $this;
    }
}
