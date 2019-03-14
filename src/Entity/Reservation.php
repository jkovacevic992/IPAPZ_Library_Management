<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/14/19
 * Time: 1:00 PM
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Class Reservation
 * @package App\Entity
 * @ORM\Entity()
 */
class Reservation
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Book", inversedBy="reservation")
     */
    private $book;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reservation")
     */
    private $user;


    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $book
     */
    public function setBook($book): void
    {
        $this->book = $book;
    }

    /**
     * @return mixed
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}