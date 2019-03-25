<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/19/19
 * Time: 11:42 AM
 */

namespace App\Entity;

/**
 * Class OnDeliveryTransaction
 *
 * @package      App\Entity
 * @Doctrine\ORM\Mapping\Entity()
 */
class OnDeliveryTransaction
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;
    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="onDeliveryTransaction")
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $complete;

    /**
     * @Doctrine\ORM\Mapping\Column(type="float")
     */
    private $amount;

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getComplete()
    {
        return $this->complete;
    }

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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param mixed $complete
     */
    public function setComplete($complete): void
    {
        $this->complete = $complete;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}
