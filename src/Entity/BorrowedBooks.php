<?php
/**
 * Created by PhpStorm.
 * User: ipa
 * Date: 21.02.19.
 * Time: 17:26
 */

namespace App\Entity;

/**
 * Class BorrowedBooks
 *
 * @package                     App\Entity
 * @Doctrine\ORM\Mapping\Entity()
 * @Doctrine\ORM\Mapping\HasLifecycleCallbacks()
 */
class BorrowedBooks
{

    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Book", inversedBy="borrowedBooks")
     */
    private $book;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Borrowed", inversedBy="borrowedBooks")
     */
    private $borrowed;


    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Doctrine\ORM\Mapping\Column(type="decimal", precision=7,scale=2)
     */
    private $lateFee = 0;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
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
     * @Doctrine\ORM\Mapping\PrePersist()
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
