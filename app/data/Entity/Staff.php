<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity\Staff
 *
 * @ORM\Entity(repositoryClass="StaffRepository")
 * @ORM\Table(name="staff", indexes={@ORM\Index(name="idx_fk_store_id", columns={"store_id"}), @ORM\Index(name="idx_fk_address_id", columns={"address_id"})})
 */
class Staff
{
    /**
     * @ORM\Id
     * @ORM\Column(type="smallint", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $staff_id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $first_name;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $last_name;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     */
    protected $address_id;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    protected $picture;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     */
    protected $store_id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="string", length=16)
     */
    protected $username;

    /**
     * @ORM\Column(name="`password`", type="string", length=40, nullable=true)
     */
    protected $password;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $last_update;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="staff")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id")
     */
    protected $payments;

    /**
     * @ORM\OneToMany(targetEntity="Rental", mappedBy="staff")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id")
     */
    protected $rentals;

    /**
     * @ORM\OneToMany(targetEntity="Store", mappedBy="staff")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="manager_staff_id")
     */
    protected $stores;

    /**
     * @ORM\ManyToOne(targetEntity="Address", inversedBy="staff")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="address_id")
     */
    protected $address;

    /**
     * @ORM\ManyToOne(targetEntity="Store", inversedBy="staff")
     * @ORM\JoinColumn(name="store_id", referencedColumnName="store_id")
     */
    protected $store;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->rentals = new ArrayCollection();
        $this->stores = new ArrayCollection();
    }

    /**
     * Set the value of staff_id.
     *
     * @param integer $staff_id
     * @return \Entity\Staff
     */
    public function setStaffId($staff_id)
    {
        $this->staff_id = $staff_id;

        return $this;
    }

    /**
     * Get the value of staff_id.
     *
     * @return integer
     */
    public function getStaffId()
    {
        return $this->staff_id;
    }

    /**
     * Set the value of first_name.
     *
     * @param string $first_name
     * @return \Entity\Staff
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Get the value of first_name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set the value of last_name.
     *
     * @param string $last_name
     * @return \Entity\Staff
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Get the value of last_name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set the value of address_id.
     *
     * @param integer $address_id
     * @return \Entity\Staff
     */
    public function setAddressId($address_id)
    {
        $this->address_id = $address_id;

        return $this;
    }

    /**
     * Get the value of address_id.
     *
     * @return integer
     */
    public function getAddressId()
    {
        return $this->address_id;
    }

    /**
     * Set the value of picture.
     *
     * @param string $picture
     * @return \Entity\Staff
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get the value of picture.
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set the value of email.
     *
     * @param string $email
     * @return \Entity\Staff
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of store_id.
     *
     * @param integer $store_id
     * @return \Entity\Staff
     */
    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;

        return $this;
    }

    /**
     * Get the value of store_id.
     *
     * @return integer
     */
    public function getStoreId()
    {
        return $this->store_id;
    }

    /**
     * Set the value of active.
     *
     * @param boolean $active
     * @return \Entity\Staff
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of active.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set the value of username.
     *
     * @param string $username
     * @return \Entity\Staff
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of password.
     *
     * @param string $password
     * @return \Entity\Staff
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of last_update.
     *
     * @param \DateTime $last_update
     * @return \Entity\Staff
     */
    public function setLastUpdate($last_update)
    {
        $this->last_update = $last_update;

        return $this;
    }

    /**
     * Get the value of last_update.
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->last_update;
    }

    /**
     * Add Payment entity to collection (one to many).
     *
     * @param \Entity\Payment $payment
     * @return \Entity\Staff
     */
    public function addPayment(Payment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Remove Payment entity from collection (one to many).
     *
     * @param \Entity\Payment $payment
     * @return \Entity\Staff
     */
    public function removePayment(Payment $payment)
    {
        $this->payments->removeElement($payment);

        return $this;
    }

    /**
     * Get Payment entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Add Rental entity to collection (one to many).
     *
     * @param \Entity\Rental $rental
     * @return \Entity\Staff
     */
    public function addRental(Rental $rental)
    {
        $this->rentals[] = $rental;

        return $this;
    }

    /**
     * Remove Rental entity from collection (one to many).
     *
     * @param \Entity\Rental $rental
     * @return \Entity\Staff
     */
    public function removeRental(Rental $rental)
    {
        $this->rentals->removeElement($rental);

        return $this;
    }

    /**
     * Get Rental entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRentals()
    {
        return $this->rentals;
    }

    /**
     * Add Store entity to collection (one to many).
     *
     * @param \Entity\Store $store
     * @return \Entity\Staff
     */
    public function addStore(Store $store)
    {
        $this->stores[] = $store;

        return $this;
    }

    /**
     * Remove Store entity from collection (one to many).
     *
     * @param \Entity\Store $store
     * @return \Entity\Staff
     */
    public function removeStore(Store $store)
    {
        $this->stores->removeElement($store);

        return $this;
    }

    /**
     * Get Store entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * Set Address entity (many to one).
     *
     * @param \Entity\Address $address
     * @return \Entity\Staff
     */
    public function setAddress(Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get Address entity (many to one).
     *
     * @return \Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set Store entity (many to one).
     *
     * @param \Entity\Store $store
     * @return \Entity\Staff
     */
    public function setStore(Store $store = null)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get Store entity (many to one).
     *
     * @return \Entity\Store
     */
    public function getStore()
    {
        return $this->store;
    }

    public function __sleep()
    {
        return array('staff_id', 'first_name', 'last_name', 'address_id', 'picture', 'email', 'store_id', 'active', 'username', 'password', 'last_update');
    }
}