<?php
/**
 * Created by PhpStDoctrine\ORM\Mapping.
 * User: inchoo
 * Date: 3/13/19
 * Time: 8:04 AM
 */

namespace App\Entity;

/**
 * Class BookGenre
 *
 * @package      App\Entity
 * @Doctrine\ORM\Mapping\Entity()
 */
class BookGenre
{

    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Book", inversedBy="bookGenre")
     */
    private $book;


    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Genre", inversedBy="bookGenre")
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
