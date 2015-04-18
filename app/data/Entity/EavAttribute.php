<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity\EavAttribute
 *
 * 属性表
 *
 * @ORM\Entity(repositoryClass="EavAttributeRepository")
 * @ORM\Table(name="eav_attribute", indexes={@ORM\Index(name="fk_eav_attribute_eav_entity_type1_idx", columns={"entity_type_id"})})
 */
class EavAttribute
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $attribute_code;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $is_required;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $is_user_defined;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $default_value;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $is_unique;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $note;

    /**
     * @ORM\Column(type="integer")
     */
    protected $entity_type_id;

    /**
     * @ORM\OneToMany(targetEntity="CarEntityMediaGallery", mappedBy="eavAttribute")
     * @ORM\JoinColumn(name="id", referencedColumnName="attribute_id")
     */
    protected $carEntityMediaGalleries;

    /**
     * @ORM\ManyToOne(targetEntity="EavEntityType", inversedBy="eavAttributes")
     * @ORM\JoinColumn(name="entity_type_id", referencedColumnName="entity_type_id")
     */
    protected $eavEntityType;

    /**
     * @ORM\ManyToMany(targetEntity="Car", mappedBy="eavAttributes")
     */
    protected $cars;

    /**
     * @ORM\ManyToMany(targetEntity="Accessory", inversedBy="eavAttributes")
     * @ORM\JoinTable(name="eav_attribute_accessories",
     *     joinColumns={@ORM\JoinColumn(name="eav_attribute_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="accessories_id", referencedColumnName="id")}
     * )
     */
    protected $accessories;

    public function __construct()
    {
        $this->carEntityMediaGalleries = new ArrayCollection();
        $this->cars = new ArrayCollection();
        $this->accessories = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Entity\EavAttribute
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of attribute_code.
     *
     * @param string $attribute_code
     * @return \Entity\EavAttribute
     */
    public function setAttributeCode($attribute_code)
    {
        $this->attribute_code = $attribute_code;

        return $this;
    }

    /**
     * Get the value of attribute_code.
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->attribute_code;
    }

    /**
     * Set the value of is_required.
     *
     * @param string $is_required
     * @return \Entity\EavAttribute
     */
    public function setIsRequired($is_required)
    {
        $this->is_required = $is_required;

        return $this;
    }

    /**
     * Get the value of is_required.
     *
     * @return string
     */
    public function getIsRequired()
    {
        return $this->is_required;
    }

    /**
     * Set the value of is_user_defined.
     *
     * @param string $is_user_defined
     * @return \Entity\EavAttribute
     */
    public function setIsUserDefined($is_user_defined)
    {
        $this->is_user_defined = $is_user_defined;

        return $this;
    }

    /**
     * Get the value of is_user_defined.
     *
     * @return string
     */
    public function getIsUserDefined()
    {
        return $this->is_user_defined;
    }

    /**
     * Set the value of default_value.
     *
     * @param string $default_value
     * @return \Entity\EavAttribute
     */
    public function setDefaultValue($default_value)
    {
        $this->default_value = $default_value;

        return $this;
    }

    /**
     * Get the value of default_value.
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Set the value of is_unique.
     *
     * @param string $is_unique
     * @return \Entity\EavAttribute
     */
    public function setIsUnique($is_unique)
    {
        $this->is_unique = $is_unique;

        return $this;
    }

    /**
     * Get the value of is_unique.
     *
     * @return string
     */
    public function getIsUnique()
    {
        return $this->is_unique;
    }

    /**
     * Set the value of note.
     *
     * @param string $note
     * @return \Entity\EavAttribute
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get the value of note.
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set the value of entity_type_id.
     *
     * @param integer $entity_type_id
     * @return \Entity\EavAttribute
     */
    public function setEntityTypeId($entity_type_id)
    {
        $this->entity_type_id = $entity_type_id;

        return $this;
    }

    /**
     * Get the value of entity_type_id.
     *
     * @return integer
     */
    public function getEntityTypeId()
    {
        return $this->entity_type_id;
    }

    /**
     * Add CarEntityMediaGallery entity to collection (one to many).
     *
     * @param \Entity\CarEntityMediaGallery $carEntityMediaGallery
     * @return \Entity\EavAttribute
     */
    public function addCarEntityMediaGallery(CarEntityMediaGallery $carEntityMediaGallery)
    {
        $this->carEntityMediaGalleries[] = $carEntityMediaGallery;

        return $this;
    }

    /**
     * Remove CarEntityMediaGallery entity from collection (one to many).
     *
     * @param \Entity\CarEntityMediaGallery $carEntityMediaGallery
     * @return \Entity\EavAttribute
     */
    public function removeCarEntityMediaGallery(CarEntityMediaGallery $carEntityMediaGallery)
    {
        $this->carEntityMediaGalleries->removeElement($carEntityMediaGallery);

        return $this;
    }

    /**
     * Get CarEntityMediaGallery entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCarEntityMediaGalleries()
    {
        return $this->carEntityMediaGalleries;
    }

    /**
     * Set EavEntityType entity (many to one).
     *
     * @param \Entity\EavEntityType $eavEntityType
     * @return \Entity\EavAttribute
     */
    public function setEavEntityType(EavEntityType $eavEntityType = null)
    {
        $this->eavEntityType = $eavEntityType;

        return $this;
    }

    /**
     * Get EavEntityType entity (many to one).
     *
     * @return \Entity\EavEntityType
     */
    public function getEavEntityType()
    {
        return $this->eavEntityType;
    }

    /**
     * Add Car entity to collection.
     *
     * @param \Entity\Car $car
     * @return \Entity\EavAttribute
     */
    public function addCar(Car $car)
    {
        $this->cars[] = $car;

        return $this;
    }

    /**
     * Remove Car entity from collection.
     *
     * @param \Entity\Car $car
     * @return \Entity\EavAttribute
     */
    public function removeCar(Car $car)
    {
        $this->cars->removeElement($car);

        return $this;
    }

    /**
     * Get Car entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCars()
    {
        return $this->cars;
    }

    /**
     * Add Accessory entity to collection.
     *
     * @param \Entity\Accessory $accessory
     * @return \Entity\EavAttribute
     */
    public function addAccessory(Accessory $accessory)
    {
        $accessory->addEavAttribute($this);
        $this->accessories[] = $accessory;

        return $this;
    }

    /**
     * Remove Accessory entity from collection.
     *
     * @param \Entity\Accessory $accessory
     * @return \Entity\EavAttribute
     */
    public function removeAccessory(Accessory $accessory)
    {
        $accessory->removeEavAttribute($this);
        $this->accessories->removeElement($accessory);

        return $this;
    }

    /**
     * Get Accessory entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccessories()
    {
        return $this->accessories;
    }

    public function __sleep()
    {
        return array('id', 'attribute_code', 'is_required', 'is_user_defined', 'default_value', 'is_unique', 'note', 'entity_type_id');
    }
}