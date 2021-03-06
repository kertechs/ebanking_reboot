<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartesBancairesRepository")
 */
class CartesBancaires
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="cartesBancaires")
     */
    private $Client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comptes", inversedBy="cartesBancaires")
     */
    private $Compte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->Client;
    }

    public function setClient(?Client $Client): self
    {
        $this->Client = $Client;

        return $this;
    }

    public function getCompte(): ?Comptes
    {
        return $this->Compte;
    }

    public function setCompte(?Comptes $Compte): self
    {
        $this->Compte = $Compte;

        return $this;
    }
}
