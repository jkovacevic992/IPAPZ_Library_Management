<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/18/19
 * Time: 11:57 AM
 */

namespace App\Entity;

/**
 * Class PaypalTransaction
 *
 * @package      App\Entity
 * @Doctrine\ORM\Mapping\Entity()
 */
class PaypalTransaction
{

    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;
    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="paypalTransaction")
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    private $payment;


    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $complete;

    /**
     * @Doctrine\ORM\Mapping\Column(type="float")
     */
    private $amount;

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

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
    public function getUser()
    {
        return $this->user;
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
    public function getComplete()
    {
        return $this->complete;
    }


    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $complete
     */
    public function setComplete($complete): void
    {
        $this->complete = $complete;
    }


    /**
     * @param mixed $payment
     */
    public function setPayment($payment): void
    {
        $this->payment = $payment;
    }
}
