<?php
/**
 * Created by PhpStorm.
 * Customer: josip
 * Date: 18.02.19.
 * Time: 09:26
 */

namespace App\Entity;

/**
 * Class Genre
 *
 * @package                                                      App\Entity
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\GenreRepository")
 * @Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity("name")
 */
class Genre
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;
    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    private $name;


    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\BookGenre", mappedBy="genre", cascade={"remove"})
     */
    private $bookGenre;

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
    public function getBookGenre()
    {
        return $this->bookGenre;
    }
}
