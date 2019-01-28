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
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
{
    const PROSPECT=100;
    const CURRENT=200;
    const ON_HOLD=300;

    use TimestampableEntity;
    use SoftDeleteableEntity;
    use BlameableEntity;
    use IpTraceableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pays='France';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code_postal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status=self::PROSPECT;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $civilite;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Comptes", fetch="EAGER", mappedBy="compte_client_id")
     */
    private $comptes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", fetch="EAGER", mappedBy="client", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $has_cb;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $has_chequier;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Demandes", mappedBy="client")
     */
    private $demandes;

    private $has_compte_epargne;
    private $has_compte_joint;
    private $has_decouvert_autorise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->has_cb = false;
        $this->has_chequier = false;
        $this->setCreatedAt(new \DateTime());
        $this->comptes = new ArrayCollection();
        $this->demandes = new ArrayCollection();
        //$this->setCreatedBy();

        $this->setHasCompteJoint(false);
        $this->setHasCompteEpargne(false);
        $this->setHasDecouvertAutorise(false);

        if ($this->id)
        {
            $comptes = $this->getComptes();
            foreach ($comptes as $compte)
            {
                $type_compte = $compte->getType();
                $nb_types_found = 0;
                switch($type_compte)
                {
                    case Comptes::COMPTE_JOINT:
                        $nb_types_found++;
                        $this->setHasCompteJoint(true);
                        break;

                    case Comptes::COMPTE_EPARGNE:
                        $nb_types_found++;
                        $this->setHasCompteEpargne(true);
                        break;

                    case Comptes::COMPTE_COURANT:
                        if ($compte->getDecouvertAutorise() == true)
                        {
                            $this->setHasDecouvertAutorise(true);
                        }
                        break;
                }

                if ($nb_types_found >= 2)
                {
                    break;
                }
            }
        }
    }

    public function getHasCompteEpargne(): ?bool
    {
        return $this->has_compte_epargne;
    }
    private function setHasCompteEpargne(?bool $has_compte_epargne): self
    {
        $this->has_compte_epargne = $has_compte_epargne;
        return $this;
    }

    public function getHasCompteJoint(): ?bool
    {
        return $this->has_compte_joint;
    }
    private function setHasCompteJoint(?bool $has_compte_joint): self
    {
        $this->has_compte_joint = $has_compte_joint;
        return $this;
    }

    public function getHasDecouvertAutorise(): ?bool
    {
        return $this->has_decouvert_autorise;
    }
    private function setHasDecouvertAutorise(?bool $has_decouvert_autorise): self
    {
        $this->has_decouvert_autorise= $has_decouvert_autorise;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(?string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): self
    {
        $this->civilite = $civilite;

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
            $compte->addCompteClientId($this);
        }

        return $this;
    }

    public function removeCompte(Comptes $compte): self
    {
        if ($this->comptes->contains($compte)) {
            $this->comptes->removeElement($compte);
            $compte->removeCompteClientId($this);
        }

        return $this;
    }

    public function getHasCb(): ?bool
    {
        return $this->has_cb;
    }

    public function setHasCb(?bool $has_cb): self
    {
        $this->has_cb = $has_cb;

        return $this;
    }

    public function getHasChequier(): ?bool
    {
        return $this->has_chequier;
    }

    public function setHasChequier(?bool $has_chequier): self
    {
        $this->has_chequier = $has_chequier;

        return $this;
    }

    /**
     * @return Collection|Demandes[]
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demandes $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes[] = $demande;
            $demande->setClient($this);
        }

        return $this;
    }

    public function removeDemande(Demandes $demande): self
    {
        if ($this->demandes->contains($demande)) {
            $this->demandes->removeElement($demande);
            // set the owning side to null (unless already changed)
            if ($demande->getClient() === $this) {
                $demande->setClient(null);
            }
        }

        return $this;
    }

    /*public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newClient_id = $user === null ? null : $this;
        if ($newClient_id !== $user->getClientId()) {
            $user->setClientId($newClient_id);
        }

        return $this;
    }*/
}
