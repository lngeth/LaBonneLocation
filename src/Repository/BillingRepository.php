<?php

namespace App\Repository;

use App\Entity\Billing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Billing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Billing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Billing[]    findAll()
 * @method Billing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Billing::class);
    }

    // /**
    //  * @return Billing[] Returns an array of Billing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Billing
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllClientBy($idOwner) {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->innerJoin('b.idCar', 'c', 'WITH', 'c.id = b.idCar')
            ->innerJoin('b.idUser', 'u', 'WITH', 'u.id = b.idUser')
            ->addSelect('u')
            ->where('c.idOwner = :idOwner')
            ->andWhere('b.paid = 1')
            ->setParameter(':idOwner', $idOwner)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllClientAndTheCarRentedOwnedBy($idOwner) {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->innerJoin('b.idCar', 'c', 'WITH', 'c.id = b.idCar')
            ->innerJoin('b.idUser', 'u', 'WITH', 'u.id = b.idUser')
            ->addSelect('u')
            ->addSelect('c')
            ->where('c.idOwner = :idOwner')
            ->andWhere('b.paid = 1')
            ->setParameter(':idOwner', $idOwner)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllBillsOfAClientById($idOwner, $idClient) {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->innerJoin('b.idCar', 'c', 'WITH', 'c.id = b.idCar')
            ->innerJoin('b.idUser', 'u', 'WITH', 'u.id = b.idUser')
            ->addSelect('c')
            ->addSelect('u')
            ->where('c.idOwner = :idOwner')
            ->andWhere('u.id = :idClient')
            ->andWhere('b.paid = 1')
            ->setParameter(':idOwner', $idOwner)
            ->setParameter(':idClient', $idClient)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllClientAndTheCarRentedOfTheMonthOwnedBy($idOwner) {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->innerJoin('b.idCar', 'c', 'WITH', 'c.id = b.idCar')
            ->innerJoin('b.idUser', 'u', 'WITH', 'u.id = b.idUser')
            ->addSelect('u')
            ->addSelect('c')
            ->where('c.idOwner = :idOwner')
            ->andWhere('MONTH(b.startDate) = MONTH(CURRENT_DATE())')
            ->andWhere('b.paid = 1')
            ->setParameter(':idOwner', $idOwner)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllRentedCarsByIdUser($idUser) {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->innerJoin('b.idCar', 'c', 'WITH', 'c.id = b.idCar')
            ->innerJoin('b.idUser', 'u', 'WITH', 'u.id = b.idUser')
            ->addSelect('c')
            ->where('u.id = :idUser')
            ->andWhere('b.paid = 1')
            ->setParameter(':idUser', $idUser)
            ->orderBy('b.returned', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
