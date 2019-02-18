<?php
/**
 * Created by PhpStorm.
 * User: josip
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
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $author;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Employee")
     */
    private $employee;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Genre")
     */
    private $genre;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Borrowed", mappedBy="book")
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
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param mixed $employee
     */
    public function setEmployee($employee): void
    {
        $this->employee = $employee;
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