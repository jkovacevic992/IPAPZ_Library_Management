<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/13/19
 * Time: 2:16 PM
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Class Wishlist
 * @package App\Entity
 * @ORM\Entity()
 */
class Wishlist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book", inversedBy="wishlist")
     */
    private $book; /** User $user */

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="wishlist")
     */
    private $user;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $book
     */
    public function setBook($book): void
    {
        $this->book = $book;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }



}