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
 * @ORM\Entity(repositoryClass="App\Repository\BanquesRepository")
 */
class Banques
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
    private $code_banque;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Agences", mappedBy="banque_id")
     */
    private $agences;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comptes")
     */
    private $banque_compte_id;

    public function __construct()
    {
        $this->agences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodeBanque(): ?string
    {
        return $this->code_banque;
    }

    public function setCodeBanque(?string $code_banque): self
    {
        $this->code_banque = $code_banque;

        return $this;
    }

    /**
     * @return Collection|Agences[]
     */
    public function getAgences(): Collection
    {
        return $this->agences;
    }

    public function addAgence(Agences $agence): self
    {
        if (!$this->agences->contains($agence)) {
            $this->agences[] = $agence;
            $agence->setBanqueId($this);
        }

        return $this;
    }

    public function removeAgence(Agences $agence): self
    {
        if ($this->agences->contains($agence)) {
            $this->agences->removeElement($agence);
            // set the owning side to null (unless already changed)
            if ($agence->getBanqueId() === $this) {
                $agence->setBanqueId(null);
            }
        }

        return $this;
    }

    public function getBanqueCompteId(): ?Comptes
    {
        return $this->banque_compte_id;
    }

    public function setBanqueCompteId(?Comptes $banque_compte_id): self
    {
        $this->banque_compte_id = $banque_compte_id;

        return $this;
    }
}
