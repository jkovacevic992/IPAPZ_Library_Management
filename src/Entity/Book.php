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
use App\Entity\BookGenre;

/**
 * Class Book
 *
 * @package                                                     App\Entity
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\Column(type="text")
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    private $summary;
    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    private $name;
    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    private $author;


    /**
     * @Doctrine\ORM\Mapping\Column(type="integer")
     * @Symfony\Component\Validator\Constraints\GreaterThan(value="0")
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    private $quantity;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $borrowedQuantity = 0;


    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $available = true;

    /**
     * @Doctrine\ORM\Mapping\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\BorrowedBooks", mappedBy="book", cascade={"remove"})
     */
    private $borrowedBooks;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\BookGenre",
     *     mappedBy="book", cascade={"persist","remove"})
     */
    private $bookGenre;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Wishlist",
     *     mappedBy="book", cascade={"persist","remove"})
     */
    private $wishlist;


    /**
     * @Doctrine\ORM\Mapping\OneToOne(targetEntity="App\Entity\Reservation",
     *     mappedBy="book", cascade={"persist","remove"})
     */
    private $reservation;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $notification = false;

    /**
     * @return mixed
     */
    public function getBorrowedBooks()
    {
        return $this->borrowedBooks;
    }


    /**
     * @return mixed
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param mixed $notification
     */
    public function setNotification($notification): void
    {
        $this->notification = $notification;
    }

    /**
     * @return mixed
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * @param mixed $reservation
     */
    public function setReservation($reservation): void
    {
        $this->reservation = $reservation;
    }


    public function __construct()
    {
        $this->bookGenre = new ArrayCollection();
    }

    /**
     * @param mixed $borrowedQuantity
     */
    public function setBorrowedQuantity($borrowedQuantity): void
    {
        $this->borrowedQuantity = $borrowedQuantity;
    }

    /**
     * @return mixed
     */
    public function getBorrowedQuantity()
    {
        return $this->borrowedQuantity;
    }

    /**
     * @return Collection
     */
    public function getBookGenre(): Collection
    {
        return $this->bookGenre;
    }

    /**
     * @param BookGenre $bookGenre
     * @return Book
     */
    public function addBookGenre(BookGenre $bookGenre): self
    {
        if (!$this->bookGenre->contains($bookGenre)) {
            $bookGenre->setBook($this);
            $this->bookGenre[] = $bookGenre;
        }

        return $this;
    }

    /**
     * @param BookGenre $bookGenre
     * @return Book
     */
    public function removeBookGenre(BookGenre $bookGenre): self
    {
        if ($this->bookGenre->contains($bookGenre)) {
            $this->bookGenre->removeElement($bookGenre);
            if ($bookGenre->getBook() === $this) {
                $bookGenre->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     */
    public function setSummary($summary): void
    {
        $this->summary = $summary;
    }

    public function addImages($image): self
    {
        $this->images[] = $image;


        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @param mixed $available
     */
    public function setAvailable($available): void
    {
        $this->available = $available;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }
}
