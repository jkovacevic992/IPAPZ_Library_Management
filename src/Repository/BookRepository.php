<?php
/**
 * Created by PhpStorm.
 * Customer: josip
 * Date: 18.02.19.
 * Time: 12:41
 */

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('count(b.id)')
            ->getQuery()
            ->getResult();
    }

    public function getAvailableBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->addSelect('r')
            ->leftJoin('b.reservation', 'r')
            ->where('b.available=true');
    }

    public function getBooks()
    {
        return $this->createQueryBuilder('b');
    }

    public function getTopBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->addSelect('count(bb.book)')
            ->innerJoin('b.borrowedBooks', 'bb')
            ->where('bb.createdAt <= :endWeek')
            ->andWhere('bb.book = b.id')
            ->groupBy('b')
            ->setParameter('endWeek', new \DateTime('now +7 day'))
            ->setMaxResults(5)
            ->orderBy('count(bb.book)', 'desc')
            ->getQuery()
            ->getResult();
    }
}
