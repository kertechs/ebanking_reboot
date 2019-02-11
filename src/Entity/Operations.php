<?php

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;
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

    const TYPE_RETRAIT= "TYPE_RETRAIT";
    const TYPE_VIREMENT = "TYPE_VIREMENT";
    const TYPE_VIREMENT_BACKOFFICE = "TYPE_VIREMENT_BACKOFFICE";
    const TYPE_CHEQUE = "TYPE_CHEQUE";
    const TYPE_CB = "TYPE_CB_IMMEDIAT";
    const TYPE_CB_DIFFERE = "TYPE_CB_DIFFERE";
    const TYPE_FRAIS = "TYPE_FRAIS";

    const TYPES_LABELS = [
        self::TYPE_RETRAIT => "Retrait espèces",
        self::TYPE_VIREMENT => "Virement bancaire",
        self::TYPE_VIREMENT_BACKOFFICE => "Virement bancaire (gestionnaire de compte)",
        self::TYPE_CHEQUE => "Chèque",
        self::TYPE_CB => "Paiement CB",
        self::TYPE_CB_DIFFERE => "Paiement CB débit différé",
        self::TYPE_FRAIS => "Frais bancaires",
    ];

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Comptes")
     */
    private $compte_emetteur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comptes")
     */
    private $compte_destinataire;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $emetteur_compte_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Beneficiaires")
     */
    private $beneficiaire;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $destinataire_compte_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

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

    public function getTypeOperationLbl(): ?string
    {
        return self::TYPES_LABELS[$this->type_operation];
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

    public function getCompteEmetteur(): ?Comptes
    {
        return $this->compte_emetteur;
    }

    public function setCompteEmetteur(?Comptes $compte):self
    {
        $this->compte_emetteur = $compte;
        $this->setEmetteurCompteId($compte->getId());
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

    public function getCompteDestinataire(): ?Comptes
    {
        return $this->compte_destinataire;
    }

    public function setCompteDestinataire(?Comptes $compte):self
    {
        $this->compte_destinataire = $compte;
        $this->setDestinataireCompteId($compte->getId());
        return $this;
    }

    public function getBeneficiaire(): ?Beneficiaires
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(?Beneficiaires $beneficiaires):self
    {
        $this->beneficiaire = $beneficiaires;
        $this->setDestinataireCompteId($beneficiaires->getCompte()->getId());
        return $this;
    }

    public function execute(EntityManagerInterface $entityManager): bool
    {
        if (
            $this->type_operation
            && $this->emetteur_compte_id
            && $this->destinataire_compte_id
            && $this->destinataire_compte_id != $this->emetteur_compte_id
            && $this->date_execution
            && $this->montant
        )
        {
            $compte_emetteur = $entityManager->find(Comptes::class, $this->emetteur_compte_id);
            //dump($compte_emetteur);

            $compte_destinataire = $entityManager->find(Comptes::class, $this->destinataire_compte_id);
            //dump($compte_destinataire);

            //Check solde compte emetteur
            if ( $this->montant <= ( $compte_emetteur->getSolde() + ($compte_emetteur->getDecouvertAutorise()?$compte_emetteur->getDecouvertAutorise():0)) )
            {
                //Debiter compte emetteur
                $compte_emetteur->setSolde($compte_emetteur->getSolde() - $this->montant);

                //Crediter compte destinataire
                $compte_destinataire->setSolde($compte_destinataire->getSolde() + $this->montant);

                //dump($compte_emetteur);
                //dump($compte_destinataire);
                //dd("virement ok");
                return true;
            }
            else
            {
                //dump($this->montant);
                //dump($this->montant <= ( $compte_emetteur->getSolde() + ($compte_emetteur->getDecouvertAutorise()?$compte_emetteur->getDecouvertAutorise():0)));
                //dd("virement k0");

                return false;
            }
        }

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }
}
