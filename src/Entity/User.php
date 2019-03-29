<?php
/**
 * Created by PhpStorm.
 * Customer: josip
 * Date: 18.02.19.
 * Time: 09:25
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Wishlist;
use App\Entity\Reservation;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\UserRepository")
 * @Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity(fields={"email"},
 *     message="There is already an account with this email")
 * @Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity(fields={"username"},
 *     message="A user with that username already exists")
 */
class User implements UserInterface
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
    private $username;
    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */

    private $firstName;
    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    private $lastName;
    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=180, unique=true)
     * @Symfony\Component\Validator\Constraints\NotBlank()
     * @Symfony\Component\Validator\Constraints\Email(message      = "The email '{{ value }}' is not a valid email.")
     */
    private $email;
    /**
     * @var                       string The hashed password
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    private $password;
    /**
     * @Doctrine\ORM\Mapping\Column(type="json")
     */
    private $roles = [];

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $admin = 0;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $hasBooks = false;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Borrowed", mappedBy="user", cascade={"remove"})
     */
    private $borrowed;
    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $employee = 0;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Wishlist",
     *     mappedBy="user", cascade={"persist","remove"})
     */
    private $wishlist;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Reservation",
     *     mappedBy="user", cascade={"persist","remove"})
     */
    private $reservation;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\PaypalTransaction",
     *     mappedBy="user", cascade={"persist","remove"})
     */
    private $paypalTransaction;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\OnDeliveryTransaction",
     *     mappedBy="user", cascade={"persist","remove"})
     */
    private $onDeliveryTransaction;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Subscription",
     *     mappedBy="user", cascade={"persist","remove"})
     */
    private $subscription;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $membership = false;

    /**
     * @return mixed
     */
    public function getMembership()
    {
        return $this->membership;
    }

    /**
     * @param mixed $membership
     */
    public function setMembership($membership): void
    {
        $this->membership = $membership;
    }



    public function __construct()
    {
        $this->wishlist = new ArrayCollection();
        $this->reservation = new ArrayCollection();
        $this->subscription = new ArrayCollection();
        $this->reservation = new ArrayCollection();
    }




    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }


    /**
     * @return Collection
     */
    public function getWishlist(): Collection
    {
        return $this->wishlist;
    }

    /**
     * @return mixed
     */
    public function getSubscription()
    {
        return $this->subscription;
    }


    /**
     * @param Wishlist $wishlist
     * @return User
     */
    public function addWishlist(Wishlist $wishlist): self
    {
        if (!$this->wishlist->contains($wishlist)) {
            $wishlist->setUser($this);
            $this->wishlist[] = $wishlist;
        }

        return $this;
    }

    /**
     * @param Wishlist $wishlist
     * @return User
     */
    public function removeWishlist(Wishlist $wishlist): self
    {
        if ($this->wishlist->contains($wishlist)) {
            $this->wishlist->removeElement($wishlist);
            if ($wishlist->getUser() === $this) {
                $wishlist->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservation->contains($reservation)) {
            $reservation->setUser($this);
            $this->reservation[] = $reservation;
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservation->contains($reservation)) {
            $this->reservation->removeElement($reservation);
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
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
     * @param mixed $hasBooks
     */
    public function setHasBooks($hasBooks): void
    {
        $this->hasBooks = $hasBooks;
    }

    /**
     * @return mixed
     */
    public function getHasBooks()
    {
        return $this->hasBooks;
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

    /**
     * @return int
     */
    public function getAdmin(): int
    {
        return $this->admin;
    }

    /**
     * @param int $admin
     */
    public function setAdmin(int $admin): void
    {
        $this->admin = $admin;
    }


    public function getFullName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getId(): ?int
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isGranted($role)
    {
        return in_array($role, $this->getRoles());
    }
}
