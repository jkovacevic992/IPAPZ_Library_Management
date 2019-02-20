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
 * @package App\Entity
 * @ORM\Entity()
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Genre", inversedBy="book")
     *
     */
    private $genre;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Borrowed", mappedBy="books",cascade={"persist"})
     * @var Collection
     */
    private $borrowed;



    public function __construct()
    {
        $this->borrowed = new ArrayCollection();
    }

    public function addBorrowed(Borrowed $borrowed): self
    {
        if (!$this->borrowed->contains($borrowed)) {
            $this->borrowed[] = $borrowed;
            $borrowed->addBook($this);
        }
        return $this;
    }
    public function removeBorrowed(Borrowed $borrowed): self
    {
        if ($this->borrowed->contains($borrowed)) {
            $this->borrowed->removeElement($borrowed);
            $borrowed->removeBorrowed($this);
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

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $employee
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre): void
    {
        $this->genre = $genre;
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