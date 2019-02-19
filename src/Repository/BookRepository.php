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

    public function getAllBooks()
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('App\Entity\Genre','g','WITH','g.id = b.genre')
            ->getQuery()
            ->getResult();
    }
}