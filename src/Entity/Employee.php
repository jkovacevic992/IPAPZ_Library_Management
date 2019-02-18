<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 18.02.19.
 * Time: 09:25
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Employee
 * @package App\Entity
 * @ORM\Entity()
 */
class Employee
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
    private $firstName;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $lastName;
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];


}