<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 25.02.19.
 * Time: 09:51
 */

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BookService
{


    protected $em;
    protected $container;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function returnBooks($request)
    {
        $em = $this->em;
        $container = $this->container;

        $query = $em->createQuery('SELECT b from App\Entity\Book b order by b.name asc'
        );
        $paginator = $container->get('knp_paginator');
        $results = $paginator->paginate(
            $query,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10)
        );
        return ($results);
    }

    public function returnFoundBooks($request, $name)
    {
        $em = $this->em;
        $container = $this->container;

        $query = $em->createQuery('SELECT b from App\Entity\Book b where b.name like :name or b.author like :name'
        )
        ->setParameter('name', '%'.$name.'%');
        $paginator = $container->get('knp_paginator');
        $results = $paginator->paginate(
            $query,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10)
        );
        return ($results);
    }
}