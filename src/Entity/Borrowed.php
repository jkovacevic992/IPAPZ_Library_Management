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
use App\Entity\BorrowedBooks;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class Borrowed
 *
 * @package      App\Entity
 * @Doctrine\ORM\Mapping\Entity()
 */
class Borrowed
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;
    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    private $borrowDate;
    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    private $returnDate;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\BorrowedBooks",
     *     mappedBy="borrowed", cascade={"persist", "remove"})
     * @var                                                    Collection
     */
    private $borrowedBooks;
    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="borrowed")
     */
    private $user;
    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $active = true;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    private $paymentMethod = 'notPaid';

    public function __construct()
    {
        $this->borrowedBooks = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param mixed $paymentMethod
     */
    public function setPaymentMethod($paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

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
     * @Symfony\Component\Validator\Constraints\Callback
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        $available = true;
        if ($this->borrowedBooks->count() === 0) {
            $available = false;
        }

        /**
         * @var BorrowedBooks $borrowedBook
         */
        foreach ($this->borrowedBooks as $borrowedBook) {
            if ($borrowedBook->getBook() === null) {
                $available = false;
            }
        }

        if (!$available) {
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}
