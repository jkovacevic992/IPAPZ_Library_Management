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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @Assert\NotBlank()
     *
     */
    private $borrowDate;
    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $returnDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BorrowedBooks", mappedBy="borrowed", cascade={"persist", "remove"})
     * @var Collection
     */
    private $borrowedBooks;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer")
     */
    private $customer;
    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }
    public function __construct()
    {
        $this->borrowedBooks = new ArrayCollection();
    }



    /**
     * @return Collection
     */
    public function getBorrowedBooks(): Collection
    {
        return $this->borrowedBooks;
    }


    public function addBorrowedBook(BorrowedBooks $borrowedBooks): self
    {
        if (!$this->borrowedBooks->contains($borrowedBooks)) {
            $borrowedBooks->setBorrowed($this);
            $this->borrowedBooks[] = $borrowedBooks;
        }
        return $this;
    }
    public function removeBorrowedBook(BorrowedBooks $borrowedBooks): self
    {
        if ($this->borrowedBooks->contains($borrowedBooks)) {
            $this->borrowedBooks->removeElement($borrowedBooks);
            if ($borrowedBooks->getBorrowed() === $this) {
                $borrowedBooks->setBorrowed(null);
            }
        }
        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        $available = true;
        if($this->borrowedBooks->count()===0){
            $available=false;
        }

        /** @var BorrowedBooks $borrowedBook */
        foreach($this->borrowedBooks as $borrowedBook){
            if($borrowedBook->getBook()===null){
               $available=false;
            }
        }
        if(!$available){
            $context->buildViolation('No available books.')
                ->addViolation();
        }


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