<?php

namespace App\Repository;

use App\Entity\Currency;
use App\Model\Currency\Filter;
use App\Model\Currency\Graph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Currency::class);
        $this->paginator = $paginator;
    }

    // /**
    //  * @return Currency[] Returns an array of Currency objects
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
    public function findOneBySomeField($value): ?Currency
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * Получение данных для графика
     *
     * @param string $numCode
     * @param \DateTimeInterface $dateFrom
     * @param \DateTimeInterface $dateTill
     * @return mixed
     */
    public function getGraph(string $numCode, \DateTimeInterface $dateFrom, \DateTimeInterface $dateTill)
    {
        return $this->createQueryBuilder('c')
            ->where('c.num_code = :num_code AND c.dateofadded >= :date_from AND c.dateofadded <= :date_till')
            ->setParameter('num_code', $numCode)
            ->setParameter('date_from', $dateFrom)
            ->setParameter('date_till', $dateTill)
            ->orderBy('c.dateofadded')
            ->getQuery()
            ->getResult();
    }

    /**
     * Получение кодов валют для вывода формы списка.
     * Используется в фильтре
     *
     * @return array
     */
    public function findNumCodes(): array
    {
        return $this->createQueryBuilder('c')
            ->select(['c.num_code', 'c.char_code'])
            ->groupBy('c.num_code')
            ->addGroupBy('c.char_code')
            ->orderBy('c.char_code')
            ->getQuery()
            ->getResult();
    }

    /**
     * Вывод списка валют с пагинацией и сортировкой
     *
     * @param Filter\Filter $filter
     * @param int $page
     * @param int $size
     * @return PaginationInterface
     */
    public function all(Filter\Filter $filter, int $page, int $size): PaginationInterface
    {
        $qb = $this->createQueryBuilder('c');

        if ($filter->num_code) {
            $qb->andWhere('LOWER(c.num_code) = :num_code');
            $qb->setParameter('num_code', mb_strtolower($filter->num_code));
        }

        if ($filter->dateofadded) {
            $qb->andWhere('c.dateofadded = :dateofadded');
            $qb->setParameter('dateofadded', $filter->dateofadded);
        }

        //$qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc')->getQuery();

        return $this->paginator->paginate($qb, $page, $size, [
            PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'c.dateofadded',
            PaginatorInterface::DEFAULT_SORT_DIRECTION => 'desc'
        ]);
    }

    /**
     * Получение данных за период времени определенной валюты
     *
     * @param Graph\Filter $filter
     * @return array
     */
    public function period(Graph\Filter $filter): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($filter->num_code) {
            $qb->andWhere('LOWER(c.num_code) = :num_code');
            $qb->setParameter('num_code', mb_strtolower($filter->num_code));
        }

        if ($filter->date_from) {
            $qb->andWhere('c.dateofadded >= :date_from');
            $qb->setParameter('date_from', $filter->date_from);
        }

        if ($filter->date_till) {
            $qb->andWhere('c.dateofadded <= :date_till');
            $qb->setParameter('date_till', $filter->date_till);
        }

        return $qb->orderBy('c.dateofadded', 'asc')->getQuery()->getResult();

    }
}
