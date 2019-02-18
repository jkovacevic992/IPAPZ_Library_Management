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
 * Class Genre
 * @package App\Entity
 * @ORM\Entity()
 */
class Genre
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
     * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="genre")
     */
    private $book;
}