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
 * Class Book
 *
 * @package                                                     App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $summary;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $author;


    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(value="0")
     * @Assert\NotBlank()
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    private $borrowedQuantity = 0;


    /**
     * @ORM\Column(type="boolean")
     */
    private $available = true;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BorrowedBooks", mappedBy="book", cascade={"remove"})
     */
    private $borrowedBooks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BookGenre", mappedBy="book", cascade={"persist","remove"})
     */
    private $bookGenre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Wishlist", mappedBy="book", cascade={"persist","remove"})
     */
    private $wishlist;


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Reservation", mappedBy="book", cascade={"persist","remove"})
     */
    private $reservation;

    /**
     * @ORM\Column(type="boolean")
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


    public function addBookGenre(BookGenre $bookGenre): self
    {
        if (!$this->bookGenre->contains($bookGenre)) {
            $bookGenre->setBook($this);
            $this->bookGenre[] = $bookGenre;
        }

        return $this;
    }

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
