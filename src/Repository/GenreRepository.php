<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 24.02.19.
 * Time: 21:32
 */

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GenreRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    public function getAvailableGenres()
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.name', 'ASC');
    }

    public function findGenresAscName()
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
