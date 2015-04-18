<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity\EavEntityType
 *
 * @ORM\Entity(repositoryClass="EavEntityTypeRepository")
 * @ORM\Table(name="eav_entity_type")
 */
class EavEntityType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $entity_type_id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $entity_type_code;

    /**
     * @ORM\OneToMany(targetEntity="EavAttribute", mappedBy="eavEntityType")
     * @ORM\JoinColumn(name="entity_type_id", referencedColumnName="entity_type_id")
     */
    protected $eavAttributes;

    public function __construct()
    {
        $this->eavAttributes = new ArrayCollection();
    }

    /**
     * Set the value of entity_type_id.
     *
     * @param integer $entity_type_id
     * @return \Entity\EavEntityType
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
     * Set the value of entity_type_code.
     *
     * @param string $entity_type_code
     * @return \Entity\EavEntityType
     */
    public function setEntityTypeCode($entity_type_code)
    {
        $this->entity_type_code = $entity_type_code;

        return $this;
    }

    /**
     * Get the value of entity_type_code.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return $this->entity_type_code;
    }

    /**
     * Add EavAttribute entity to collection (one to many).
     *
     * @param \Entity\EavAttribute $eavAttribute
     * @return \Entity\EavEntityType
     */
    public function addEavAttribute(EavAttribute $eavAttribute)
    {
        $this->eavAttributes[] = $eavAttribute;

        return $this;
    }

    /**
     * Remove EavAttribute entity from collection (one to many).
     *
     * @param \Entity\EavAttribute $eavAttribute
     * @return \Entity\EavEntityType
     */
    public function removeEavAttribute(EavAttribute $eavAttribute)
    {
        $this->eavAttributes->removeElement($eavAttribute);

        return $this;
    }

    /**
     * Get EavAttribute entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEavAttributes()
    {
        return $this->eavAttributes;
    }

    public function __sleep()
    {
        return array('entity_type_id', 'entity_type_code');
    }
}