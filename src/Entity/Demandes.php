<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\IpTraceable\Traits\IpTraceableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandesRepository")
 */
class Demandes
{
    use IpTraceableEntity;
    use BlameableEntity;
    use SoftDeleteableEntity;
    use TimestampableEntity;

    const STATUS_NEW = 100;
    const STATUS_GRANTED = 200;
    const STATUS_REFUSED = 300;
    const STATUS_LABELS = [
        self::STATUS_NEW => "Demande enregistrée",
        self::STATUS_GRANTED => "Demande acceptée",
        self::STATUS_REFUSED => "Demande refusée",
    ];

    const DEMANDE_CHEQUIER = "DEMANDE_CHEQUIER";
    const DEMANDE_CB = "DEMANDE_CB";
    const DEMANDE_COMPTE_EPARGNE = "DEMANDE_COMPTE_EPARGNE";
    const DEMANDE_COMPTE_JOINT = "DEMANDE_COMPTE_JOINT";
    const DEMANDE_DECOUVERT_AUTORISE = "DEMANDE_DECOUVERT_AUTORISE";
    const DEMANDE_DESTINATAIRE_VIREMENT = "DEMANDE_DESTINATAIRE_VIREMENT";
    const DEMANDES_LABELS = [
        self::DEMANDE_CHEQUIER => "Demande de chéquier",
        self::DEMANDE_CB => "Demande de carte bleue",
        self::DEMANDE_DESTINATAIRE_VIREMENT => "Demande de compte destinataire de virement",
        self::DEMANDE_COMPTE_EPARGNE => "Demande de création de compte épargne",
        self::DEMANDE_COMPTE_JOINT => "Demande de création de compte joint",
        self::DEMANDE_DECOUVERT_AUTORISE => "Demande d'autorisation de découvert",
    ];

    public function __construct($article_demande)
    {
        dump($article_demande);

        switch ($article_demande)
        {
            case 'chequier':
                $this->setType(self::DEMANDE_CHEQUIER);
                $this->setDetails(null);
                break;

            case 'cb':
                $this->setType(self::DEMANDE_CB);
                $this->setDetails(null);
                break;

            case 'autorisation-decouvert':
                $this->setType(self::DEMANDE_DECOUVERT_AUTORISE);
                $this->setDetails(null);
                break;

            case 'compte-epargne':
                $this->setType(self::DEMANDE_COMPTE_EPARGNE);
                $this->setDetails(null);
                break;

            case 'compte-joint':
                $this->setType(self::DEMANDE_COMPTE_JOINT);
                $this->setDetails(null);
                break;

            case 'beneficiaire-virement':
                $this->setType(self::DEMANDE_DESTINATAIRE_VIREMENT);
                $this->setDetails(null);
                break;
        }

        $this->createdAt = new \DateTime();
        $this->status = self::STATUS_NEW;
    }
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $details = [];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="demandes")
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
    public function getTypeLbl($type=""): ?string
    {
        if ($type && isset(self::DEMANDES_LABELS[$type]))
        {
            return self::DEMANDES_LABELS[$type];
        }
        elseif ($type)
        {
            return "";
        }

        return (isset(self::DEMANDES_LABELS[$this->getType()]))?self::DEMANDES_LABELS[$this->getType()]:'';
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }
    public function getStatusLbl($status=""): ?string
    {
        if ($status && isset(self::STATUS_LABELS[$status]))
        {
            return self::STATUS_LABELS[$status];
        }
        elseif ($status)
        {
            return "";
        }

        return (isset(self::STATUS_LABELS[$this->getStatus()]))?self::STATUS_LABELS[$this->getStatus()]:'';
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(?array $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
