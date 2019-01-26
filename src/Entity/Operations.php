<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\IpTraceable\Traits\IpTraceableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OperationsRepository")
 */
class Operations
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_execution;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type_operation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $credit_debit;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $emetteur_compte_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $destinataire_compte_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateExecution(): ?\DateTimeInterface
    {
        return $this->date_execution;
    }

    public function setDateExecution(?\DateTimeInterface $date_execution): self
    {
        $this->date_execution = $date_execution;

        return $this;
    }

    public function getTypeOperation(): ?string
    {
        return $this->type_operation;
    }

    public function setTypeOperation(?string $type_operation): self
    {
        $this->type_operation = $type_operation;

        return $this;
    }

    public function getCreditDebit(): ?string
    {
        return $this->credit_debit;
    }

    public function setCreditDebit(?string $credit_debit): self
    {
        $this->credit_debit = $credit_debit;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getEmetteurCompteId(): ?int
    {
        return $this->emetteur_compte_id;
    }

    public function setEmetteurCompteId(?int $emetteur_compte_id): self
    {
        $this->emetteur_compte_id = $emetteur_compte_id;

        return $this;
    }

    public function getDestinataireCompteId(): ?int
    {
        return $this->destinataire_compte_id;
    }

    public function setDestinataireCompteId(?int $destinataire_compte_id): self
    {
        $this->destinataire_compte_id = $destinataire_compte_id;

        return $this;
    }
}
