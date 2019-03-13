<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/13/19
 * Time: 8:04 AM
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
/**
 * Class BookGenre
 * @package App\Entity
 * @ORM\Entity()
 */
class BookGenre
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book", inversedBy="bookGenre")
     */
    private $book;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Genre", inversedBy="bookGenre")
     */
    private $genre;

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
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $book
     */
    public function setBook($book): void
    {
        $this->book = $book;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre): void
    {
        $this->genre = $genre;
    }
}