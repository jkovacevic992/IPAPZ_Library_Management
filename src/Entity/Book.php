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
}