<?php

namespace App\Repository;

use App\Entity\CustomerCalls;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomerCalls>
 *
 * @method CustomerCalls|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerCalls|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerCalls[]    findAll()
 * @method CustomerCalls[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerCallsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerCalls::class);
    }

    public function add(CustomerCalls $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomerCalls $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findStatisticsData(EntityManagerInterface $entityManagerInterface)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT 
                customer_calls.import_id,
                COUNT(customer_calls.import_id) AS total_calls,
                SUM(customer_calls.duration) AS total_duration,
                (
                    SELECT COUNT(same_continent_calls.customer_continent_code)
                    FROM customer_calls same_continent_calls
                    WHERE same_continent_calls.customer_continent_code = same_continent_calls.dialed_phone_continent_code
                    AND same_continent_calls.import_id = customer_calls.import_id
                ) AS total_calls_same_continent,
                (
                    SELECT SUM(duration_calls.duration)
                    FROM customer_calls duration_calls
                    WHERE duration_calls.customer_continent_code = duration_calls.dialed_phone_continent_code
                    AND duration_calls.import_id = customer_calls.import_id
                ) AS total_duration_same_continent
            FROM 
                customer_calls
            GROUP BY 
                customer_calls.import_id
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function findStatisticsDatas()
    {
        return $this->createQueryBuilder('customer_calls')
            ->addSelect(
                'customer_calls.import_id',
                'COUNT(customer_calls.import_id) total_calls',
                'SUM(customer_calls.duration) total_duration'
            )

            ->groupBy('customer_calls.import_id')
            ->getQuery()
            ->getArrayResult();
    }

//    /**
//     * @return CustomerCalls[] Returns an array of CustomerCalls objects
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

//    public function findOneBySomeField($value): ?CustomerCalls
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
