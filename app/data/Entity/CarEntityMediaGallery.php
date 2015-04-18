<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity\CarEntityMediaGallery
 *
 * @ORM\Entity(repositoryClass="CarEntityMediaGalleryRepository")
 * @ORM\Table(name="car_entity_media_gallery", indexes={@ORM\Index(name="fk_car_entity_media_gallery_eav_attribute1_idx", columns={"attribute_id"}), @ORM\Index(name="fk_car_entity_media_gallery_car1_idx", columns={"entity_id"})})
 */
class CarEntityMediaGallery
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $value_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $attribute_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $entity_id;

    /**
     * @ORM\Column(name="`value`", type="string", length=45, nullable=true)
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="EavAttribute", inversedBy="carEntityMediaGalleries")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     */
    protected $eavAttribute;

    /**
     * @ORM\ManyToOne(targetEntity="Car", inversedBy="carEntityMediaGalleries")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    protected $car;

    public function __construct()
    {
    }

    /**
     * Set the value of value_id.
     *
     * @param integer $value_id
     * @return \Entity\CarEntityMediaGallery
     */
    public function setValueId($value_id)
    {
        $this->value_id = $value_id;

        return $this;
    }

    /**
     * Get the value of value_id.
     *
     * @return integer
     */
    public function getValueId()
    {
        return $this->value_id;
    }

    /**
     * Set the value of attribute_id.
     *
     * @param integer $attribute_id
     * @return \Entity\CarEntityMediaGallery
     */
    public function setAttributeId($attribute_id)
    {
        $this->attribute_id = $attribute_id;

        return $this;
    }

    /**
     * Get the value of attribute_id.
     *
     * @return integer
     */
    public function getAttributeId()
    {
        return $this->attribute_id;
    }

    /**
     * Set the value of entity_id.
     *
     * @param integer $entity_id
     * @return \Entity\CarEntityMediaGallery
     */
    public function setEntityId($entity_id)
    {
        $this->entity_id = $entity_id;

        return $this;
    }

    /**
     * Get the value of entity_id.
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entity_id;
    }

    /**
     * Set the value of value.
     *
     * @param string $value
     * @return \Entity\CarEntityMediaGallery
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set EavAttribute entity (many to one).
     *
     * @param \Entity\EavAttribute $eavAttribute
     * @return \Entity\CarEntityMediaGallery
     */
    public function setEavAttribute(EavAttribute $eavAttribute = null)
    {
        $this->eavAttribute = $eavAttribute;

        return $this;
    }

    /**
     * Get EavAttribute entity (many to one).
     *
     * @return \Entity\EavAttribute
     */
    public function getEavAttribute()
    {
        return $this->eavAttribute;
    }

    /**
     * Set Car entity (many to one).
     *
     * @param \Entity\Car $car
     * @return \Entity\CarEntityMediaGallery
     */
    public function setCar(Car $car = null)
    {
        $this->car = $car;

        return $this;
    }

    /**
     * Get Car entity (many to one).
     *
     * @return \Entity\Car
     */
    public function getCar()
    {
        return $this->car;
    }

    public function __sleep()
    {
        return array('value_id', 'attribute_id', 'entity_id', 'value');
    }
}