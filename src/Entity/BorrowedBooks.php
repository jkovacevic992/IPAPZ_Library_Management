<?php
/**
 * Created by PhpStorm.
 * User: ipa
 * Date: 21.02.19.
 * Time: 17:26
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BorrowedBooks
 * @package App\Entity
 * @ORM\Entity()
 */
class BorrowedBooks
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book", inversedBy="borrowedBooks")
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Borrowed", inversedBy="borrowedBooks")
     */
    private $borrowed;

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
     * @param mixed $book
     */
    public function setBook($book): void
    {
        $this->book = $book;
    }

    /**
     * @return mixed
     */
    public function getBorrowed()
    {
        return $this->borrowed;
    }

    /**
     * @param mixed $borrowed
     */
    public function setBorrowed($borrowed): void
    {
        $this->borrowed = $borrowed;
    }

}