<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 18.02.19.
 * Time: 09:26
 */

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Borrowed
 * @package App\Entity
 * @ORM\Entity()
 */
class Borrowed
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="datetime")
     */
    private $borrowDate;
    /**
     * @ORM\Column(type="datetime")
     */
    private $returnDate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="borrowed")
     * @ORM\JoinTable(name="books_borrowed")
     */
    private $book;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;
}