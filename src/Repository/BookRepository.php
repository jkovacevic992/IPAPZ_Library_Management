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
            ->where('b.quantity != b.borrowedQuantity')
            ->getQuery()
            ->getResult();
    }

    public function getAvailableBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('b')

            ->leftJoin('b.reservation', 'r')
            ->where('b.available=true')
            ->andWhere('r.book is null');
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

    public function getBooksByUser($user)
    {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->innerJoin('App\Entity\User', 'u')
            ->innerJoin('App\Entity\Borrowed', 'bb')
            ->innerJoin('App\Entity\BorrowedBooks', 'bbb')
            ->where('bb.user = :user')
            ->andWhere('b = bbb.book')
            ->groupBy('b')
            ->setParameter('user', $user);
    }
}
