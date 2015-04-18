<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity\Accessory
 *
 * 配件
 *
 * @ORM\Entity(repositoryClass="AccessoryRepository")
 * @ORM\Table(name="accessories")
 */
class Accessory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="`name`", type="string", length=45, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $create_at;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $update_at;

    /**
     * @ORM\ManyToMany(targetEntity="EavAttribute", mappedBy="accessories")
     */
    protected $eavAttributes;

    public function __construct()
    {
        $this->eavAttributes = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Entity\Accessory
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
     * Set the value of name.
     *
     * @param string $name
     * @return \Entity\Accessory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of create_at.
     *
     * @param string $create_at
     * @return \Entity\Accessory
     */
    public function setCreateAt($create_at)
    {
        $this->create_at = $create_at;

        return $this;
    }

    /**
     * Get the value of create_at.
     *
     * @return string
     */
    public function getCreateAt()
    {
        return $this->create_at;
    }

    /**
     * Set the value of update_at.
     *
     * @param string $update_at
     * @return \Entity\Accessory
     */
    public function setUpdateAt($update_at)
    {
        $this->update_at = $update_at;

        return $this;
    }

    /**
     * Get the value of update_at.
     *
     * @return string
     */
    public function getUpdateAt()
    {
        return $this->update_at;
    }

    /**
     * Add EavAttribute entity to collection.
     *
     * @param \Entity\EavAttribute $eavAttribute
     * @return \Entity\Accessory
     */
    public function addEavAttribute(EavAttribute $eavAttribute)
    {
        $this->eavAttributes[] = $eavAttribute;

        return $this;
    }

    /**
     * Remove EavAttribute entity from collection.
     *
     * @param \Entity\EavAttribute $eavAttribute
     * @return \Entity\Accessory
     */
    public function removeEavAttribute(EavAttribute $eavAttribute)
    {
        $this->eavAttributes->removeElement($eavAttribute);

        return $this;
    }

    /**
     * Get EavAttribute entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEavAttributes()
    {
        return $this->eavAttributes;
    }

    public function __sleep()
    {
        return array('id', 'name', 'create_at', 'update_at');
    }
}