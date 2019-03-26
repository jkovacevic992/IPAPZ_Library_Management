<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 23.02.19.
 * Time: 13:00
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUsers()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getResult();
    }

    public function findBook($id, $book)
    {
        return $this->createQueryBuilder('u')
            ->select('b')
            ->innerJoin('App\Entity\Book', 'b')
            ->innerJoin('App\Entity\Borrowed', 'b2')
            ->innerJoin('App\Entity\Reservation', 'r')
            ->where('u.id = :id')
            ->andWhere('r.book is not null')
            ->andWhere('b.id = :book')
            ->setParameter('book', $book)
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
