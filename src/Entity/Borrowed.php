<?php
/**
 * Created by PhpStorm.
 * Customer: josip
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="borrowed",cascade={"persist"})
     * @var Collection
     */
    private $books;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer")
     */
    private $customer;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Collection $books
     */
    public function setBooks(Collection $books): void
    {
        $this->books = $books;
    }


    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->addBorrowed($this);
        }
        return $this;
    }
    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            $book->removeBorrowed($this);
        }
        return $this;
    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getBorrowDate()
    {
        return $this->borrowDate;
    }

    /**
     * @param mixed $borrowDate
     */
    public function setBorrowDate($borrowDate): void
    {
        $this->borrowDate = $borrowDate;
    }

    /**
     * @return mixed
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * @param mixed $returnDate
     */
    public function setReturnDate($returnDate): void
    {
        $this->returnDate = $returnDate;
    }



    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }


}