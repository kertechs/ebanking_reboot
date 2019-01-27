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
 * @ORM\Entity(repositoryClass="App\Repository\ComptesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comptes
{
    use IpTraceableEntity;
    use BlameableEntity;
    use SoftDeleteableEntity;
    use TimestampableEntity;

    const COMPTE_COURANT = "COMPTE_COURANT";
    const COMPTE_EPARGNE = "COMPTE_EPARGNE";
    const COMPTE_JOINT = "COMPTE_JOINT";

    const COMPTES_LABELS = [
        self::COMPTE_COURANT => "Compte Courant",
        self::COMPTE_EPARGNE => "Compte Epargne",
        self::COMPTE_JOINT => "Compte Joint",
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, columnDefinition="INT(11) UNSIGNED ZEROFILL", nullable=true)
     */
    private $num_compte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $solde;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $decouvert_autorise;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $decouvert_maximum;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Agences", fetch="EAGER", inversedBy="comptes")
     */
    private $agence_id;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true}, columnDefinition="INT(2) UNSIGNED ZEROFILL", nullable=true)
     */
    private $cle_rib;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $iban;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Client", fetch="EAGER", inversedBy="comptes")
     */
    private $compte_client_id;

    public function __construct()
    {
        $this->compte_client_id = new ArrayCollection();
        $this->decouvert_autorise = false;
        $this->decouvert_maximum = 0;
        $this->solde = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?int
    {
        return $this->num_compte;
    }

    public function setNumCompte(?int $num_compte): self
    {
        $this->num_compte = $num_compte;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getTypeLbl(): ?string
    {
        return self::COMPTES_LABELS[$this->type];
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCleRib(): ?int
    {
        return $this->cle_rib;
    }

    public function setCleRib(?int $cle_rib): self
    {
        $this->cle_rib = $cle_rib;
        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(?string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(?float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getDecouvertAutorise(): ?bool
    {
        return $this->decouvert_autorise;
    }

    public function setDecouvertAutorise(?bool $decouvert_autorise): self
    {
        $this->decouvert_autorise = $decouvert_autorise;

        return $this;
    }

    public function getDecouvertMaximum(): ?int
    {
        return $this->decouvert_maximum;
    }

    public function setDecouvertMaximum(?int $decouvert_maximum): self
    {
        $this->decouvert_maximum = $decouvert_maximum;

        return $this;
    }

    public function getAgenceId(): ?Agences
    {
        return $this->agence_id;
    }

    public function setAgenceId(?Agences $agence_id): self
    {
        $this->agence_id = $agence_id;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getCompteClientId(): Collection
    {
        return $this->compte_client_id;
    }

    public function addCompteClientId(Client $compteClientId): self
    {
        if (!$this->compte_client_id->contains($compteClientId)) {
            $this->compte_client_id[] = $compteClientId;
        }

        return $this;
    }

    public function removeCompteClientId(Client $compteClientId): self
    {
        if ($this->compte_client_id->contains($compteClientId)) {
            $this->compte_client_id->removeElement($compteClientId);
        }

        return $this;
    }
}
