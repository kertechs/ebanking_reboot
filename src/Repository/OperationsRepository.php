<?php

namespace App\Repository;

use App\Entity\Comptes;
use App\Entity\Operations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Operations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operations[]    findAll()
 * @method Operations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Operations::class);
    }

    /**
     * @return Operations[] Returns an array of Operations objects
     */
    public function findByCompte(Comptes $compte)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.emetteur_compte_id = :emetteur_compte_id OR o.destinataire_compte_id = :destinataire_compte_id')
            ->andWhere('o.deletedAt IS NULL')
            ->setParameter('emetteur_compte_id', $compte->getId())
            ->setParameter('destinataire_compte_id', $compte->getId())
            ->orderBy('o.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Operations[] Returns an array of Operations objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Operations
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
