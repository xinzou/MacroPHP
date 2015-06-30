<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity\Rental
 *
 * @ORM\Entity(repositoryClass="RentalRepository")
 * @ORM\Table(name="rental", indexes={@ORM\Index(name="idx_fk_inventory_id", columns={"inventory_id"}), @ORM\Index(name="idx_fk_customer_id", columns={"customer_id"}), @ORM\Index(name="idx_fk_staff_id", columns={"staff_id"})}, uniqueConstraints={@ORM\UniqueConstraint(name="", columns={"rental_date", "inventory_id", "customer_id"})})
 */
class Rental
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $rental_id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $rental_date;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $inventory_id;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     */
    protected $customer_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $return_date;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     */
    protected $staff_id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $last_update;

    /**
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="rental")
     * @ORM\JoinColumn(name="rental_id", referencedColumnName="rental_id", onDelete="SET NULL")
     */
    protected $payments;

    /**
     * @ORM\ManyToOne(targetEntity="Inventory", inversedBy="rentals")
     * @ORM\JoinColumn(name="inventory_id", referencedColumnName="inventory_id")
     */
    protected $inventory;

    /**
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="rentals")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="customer_id")
     */
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Staff", inversedBy="rentals")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="staff_id")
     */
    protected $staff;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    /**
     * Set the value of rental_id.
     *
     * @param integer $rental_id
     * @return \Entity\Rental
     */
    public function setRentalId($rental_id)
    {
        $this->rental_id = $rental_id;

        return $this;
    }

    /**
     * Get the value of rental_id.
     *
     * @return integer
     */
    public function getRentalId()
    {
        return $this->rental_id;
    }

    /**
     * Set the value of rental_date.
     *
     * @param \DateTime $rental_date
     * @return \Entity\Rental
     */
    public function setRentalDate($rental_date)
    {
        $this->rental_date = $rental_date;

        return $this;
    }

    /**
     * Get the value of rental_date.
     *
     * @return \DateTime
     */
    public function getRentalDate()
    {
        return $this->rental_date;
    }

    /**
     * Set the value of inventory_id.
     *
     * @param integer $inventory_id
     * @return \Entity\Rental
     */
    public function setInventoryId($inventory_id)
    {
        $this->inventory_id = $inventory_id;

        return $this;
    }

    /**
     * Get the value of inventory_id.
     *
     * @return integer
     */
    public function getInventoryId()
    {
        return $this->inventory_id;
    }

    /**
     * Set the value of customer_id.
     *
     * @param integer $customer_id
     * @return \Entity\Rental
     */
    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    /**
     * Get the value of customer_id.
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Set the value of return_date.
     *
     * @param \DateTime $return_date
     * @return \Entity\Rental
     */
    public function setReturnDate($return_date)
    {
        $this->return_date = $return_date;

        return $this;
    }

    /**
     * Get the value of return_date.
     *
     * @return \DateTime
     */
    public function getReturnDate()
    {
        return $this->return_date;
    }

    /**
     * Set the value of staff_id.
     *
     * @param integer $staff_id
     * @return \Entity\Rental
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
     * Set the value of last_update.
     *
     * @param \DateTime $last_update
     * @return \Entity\Rental
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
     * @return \Entity\Rental
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
     * @return \Entity\Rental
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
     * Set Inventory entity (many to one).
     *
     * @param \Entity\Inventory $inventory
     * @return \Entity\Rental
     */
    public function setInventory(Inventory $inventory = null)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get Inventory entity (many to one).
     *
     * @return \Entity\Inventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * Set Customer entity (many to one).
     *
     * @param \Entity\Customer $customer
     * @return \Entity\Rental
     */
    public function setCustomer(Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get Customer entity (many to one).
     *
     * @return \Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set Staff entity (many to one).
     *
     * @param \Entity\Staff $staff
     * @return \Entity\Rental
     */
    public function setStaff(Staff $staff = null)
    {
        $this->staff = $staff;

        return $this;
    }

    /**
     * Get Staff entity (many to one).
     *
     * @return \Entity\Staff
     */
    public function getStaff()
    {
        return $this->staff;
    }

    public function __sleep()
    {
        return array('rental_id', 'rental_date', 'inventory_id', 'customer_id', 'return_date', 'staff_id', 'last_update');
    }
}