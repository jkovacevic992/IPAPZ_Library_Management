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
 *
 * @package                     App\Entity
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="decimal", precision=7,scale=2)
     */
    private $lateFee = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $paid = false;

    /**
     * @return mixed
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param mixed $paid
     */
    public function setPaid($paid): void
    {
        $this->paid = $paid;
    }

    /**
     * @param mixed $lateFee
     */
    public function setLateFee($lateFee): void
    {
        $this->lateFee = $lateFee;
    }

    /**
     * @return mixed
     */
    public function getLateFee()
    {
        return $this->lateFee;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime('now');
    }

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
