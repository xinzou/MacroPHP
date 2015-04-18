<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity\CategoryEntity
 *
 * 分类
 *
 * @ORM\Entity(repositoryClass="CategoryEntityRepository")
 * @ORM\Table(name="category_entity")
 */
class CategoryEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $entity_id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $entity_type_id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $is_visible;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $parent_id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $create_at;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $update_at;

    /**
     * @ORM\Column(name="`path`", type="string", length=45, nullable=true)
     */
    protected $path;

    /**
     * @ORM\Column(name="`position`", type="string", length=45, nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(name="`level`", type="string", length=45, nullable=true)
     */
    protected $level;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $children_count;

    /**
     * @ORM\OneToMany(targetEntity="Car", mappedBy="categoryEntity")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="category_entity_id")
     */
    protected $cars;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
    }

    /**
     * Set the value of entity_id.
     *
     * @param integer $entity_id
     * @return \Entity\CategoryEntity
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
     * Set the value of entity_type_id.
     *
     * @param string $entity_type_id
     * @return \Entity\CategoryEntity
     */
    public function setEntityTypeId($entity_type_id)
    {
        $this->entity_type_id = $entity_type_id;

        return $this;
    }

    /**
     * Get the value of entity_type_id.
     *
     * @return string
     */
    public function getEntityTypeId()
    {
        return $this->entity_type_id;
    }

    /**
     * Set the value of is_visible.
     *
     * @param string $is_visible
     * @return \Entity\CategoryEntity
     */
    public function setIsVisible($is_visible)
    {
        $this->is_visible = $is_visible;

        return $this;
    }

    /**
     * Get the value of is_visible.
     *
     * @return string
     */
    public function getIsVisible()
    {
        return $this->is_visible;
    }

    /**
     * Set the value of parent_id.
     *
     * @param string $parent_id
     * @return \Entity\CategoryEntity
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;

        return $this;
    }

    /**
     * Get the value of parent_id.
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Set the value of create_at.
     *
     * @param string $create_at
     * @return \Entity\CategoryEntity
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
     * @return \Entity\CategoryEntity
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
     * Set the value of path.
     *
     * @param string $path
     * @return \Entity\CategoryEntity
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of position.
     *
     * @param string $position
     * @return \Entity\CategoryEntity
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the value of position.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of level.
     *
     * @param string $level
     * @return \Entity\CategoryEntity
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get the value of level.
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set the value of children_count.
     *
     * @param string $children_count
     * @return \Entity\CategoryEntity
     */
    public function setChildrenCount($children_count)
    {
        $this->children_count = $children_count;

        return $this;
    }

    /**
     * Get the value of children_count.
     *
     * @return string
     */
    public function getChildrenCount()
    {
        return $this->children_count;
    }

    /**
     * Add Car entity to collection (one to many).
     *
     * @param \Entity\Car $car
     * @return \Entity\CategoryEntity
     */
    public function addCar(Car $car)
    {
        $this->cars[] = $car;

        return $this;
    }

    /**
     * Remove Car entity from collection (one to many).
     *
     * @param \Entity\Car $car
     * @return \Entity\CategoryEntity
     */
    public function removeCar(Car $car)
    {
        $this->cars->removeElement($car);

        return $this;
    }

    /**
     * Get Car entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCars()
    {
        return $this->cars;
    }

    public function __sleep()
    {
        return array('entity_id', 'entity_type_id', 'is_visible', 'parent_id', 'create_at', 'update_at', 'path', 'position', 'level', 'children_count');
    }
}