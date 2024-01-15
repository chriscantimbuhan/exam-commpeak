<?php

namespace App\Repository;

use App\Entity\CustomerCallUploads;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomerCallUploads>
 *
 * @method CustomerCallUploads|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerCallUploads|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerCallUploads[]    findAll()
 * @method CustomerCallUploads[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerCallUploadsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerCallUploads::class);
    }

    public function add(CustomerCallUploads $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomerCallUploads $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrderedByIdDesc()
    {
        return $this->createQueryBuilder('customer_call_uploads')
            ->orderBy('customer_call_uploads.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getArrayResult();
    }

//    /**
//     * @return CustomerCallUploads[] Returns an array of CustomerCallUploads objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CustomerCallUploads
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
