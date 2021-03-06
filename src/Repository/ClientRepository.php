<?php

namespace App\Repository;

use App\Entity\Beneficiaires;
use App\Entity\Client;
use App\Entity\Comptes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @return Client[]
     */
    public function findAllProspects()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.deletedAt IS NULL')
            ->andWhere('c.status = 100')
            //->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Comptes[]
     */
    public function findAllComptes(Client $client)
    {
        return $client->getComptes();
    }

    /**
     * @return Beneficiaires[]
     */
    public function findAllBeneficiaires(Client $client)
    {
        return $client->getBeneficiaires();
    }

    // /**
    //  * @return Client[] Returns an array of Client objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
