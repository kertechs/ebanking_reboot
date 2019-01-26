<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\IpTraceable\Traits\IpTraceableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ComptesRepository")
 */
class Comptes
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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
}
