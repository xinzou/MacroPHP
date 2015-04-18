<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity\Car
 *
 * 整车
 *
 * @ORM\Entity(repositoryClass="CarRepository")
 * @ORM\Table(name="car", indexes={@ORM\Index(name="fk_car_category_entity1_idx", columns={"category_entity_id"})})
 */
class Car
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * 唯一标识
     *
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $sku;

    /**
     * 名字
     *
     * @ORM\Column(name="`name`", type="string", length=45, nullable=true)
     */
    protected $name;

    /**
     * 名字拼音
     *
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $pinyin;

    /**
     * 名字拼音简码
     *
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $pinyin_jm;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $category_entity_id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $create_at;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $update_at;

    /**
     * @ORM\OneToMany(targetEntity="CarEntityMediaGallery", mappedBy="car")
     * @ORM\JoinColumn(name="id", referencedColumnName="entity_id")
     */
    protected $carEntityMediaGalleries;

    /**
     * @ORM\ManyToOne(targetEntity="CategoryEntity", inversedBy="cars")
     * @ORM\JoinColumn(name="category_entity_id", referencedColumnName="entity_id")
     */
    protected $categoryEntity;

    /**
     * @ORM\ManyToMany(targetEntity="EavAttribute", inversedBy="cars")
     * @ORM\JoinTable(name="car_eav_attribute",
     *     joinColumns={@ORM\JoinColumn(name="car_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="eav_attribute_id", referencedColumnName="id")}
     * )
     */
    protected $eavAttributes;

    public function __construct()
    {
        $this->carEntityMediaGalleries = new ArrayCollection();
        $this->eavAttributes = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Entity\Car
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
     * Set the value of sku.
     *
     * @param string $sku
     * @return \Entity\Car
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get the value of sku.
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set the value of name.
     *
     * @param string $name
     * @return \Entity\Car
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
     * Set the value of pinyin.
     *
     * @param string $pinyin
     * @return \Entity\Car
     */
    public function setPinyin($pinyin)
    {
        $this->pinyin = $pinyin;

        return $this;
    }

    /**
     * Get the value of pinyin.
     *
     * @return string
     */
    public function getPinyin()
    {
        return $this->pinyin;
    }

    /**
     * Set the value of pinyin_jm.
     *
     * @param string $pinyin_jm
     * @return \Entity\Car
     */
    public function setPinyinJm($pinyin_jm)
    {
        $this->pinyin_jm = $pinyin_jm;

        return $this;
    }

    /**
     * Get the value of pinyin_jm.
     *
     * @return string
     */
    public function getPinyinJm()
    {
        return $this->pinyin_jm;
    }

    /**
     * Set the value of category_entity_id.
     *
     * @param integer $category_entity_id
     * @return \Entity\Car
     */
    public function setCategoryEntityId($category_entity_id)
    {
        $this->category_entity_id = $category_entity_id;

        return $this;
    }

    /**
     * Get the value of category_entity_id.
     *
     * @return integer
     */
    public function getCategoryEntityId()
    {
        return $this->category_entity_id;
    }

    /**
     * Set the value of create_at.
     *
     * @param string $create_at
     * @return \Entity\Car
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
     * @return \Entity\Car
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
     * Add CarEntityMediaGallery entity to collection (one to many).
     *
     * @param \Entity\CarEntityMediaGallery $carEntityMediaGallery
     * @return \Entity\Car
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
     * @return \Entity\Car
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
     * Set CategoryEntity entity (many to one).
     *
     * @param \Entity\CategoryEntity $categoryEntity
     * @return \Entity\Car
     */
    public function setCategoryEntity(CategoryEntity $categoryEntity = null)
    {
        $this->categoryEntity = $categoryEntity;

        return $this;
    }

    /**
     * Get CategoryEntity entity (many to one).
     *
     * @return \Entity\CategoryEntity
     */
    public function getCategoryEntity()
    {
        return $this->categoryEntity;
    }

    /**
     * Add EavAttribute entity to collection.
     *
     * @param \Entity\EavAttribute $eavAttribute
     * @return \Entity\Car
     */
    public function addEavAttribute(EavAttribute $eavAttribute)
    {
        $eavAttribute->addCar($this);
        $this->eavAttributes[] = $eavAttribute;

        return $this;
    }

    /**
     * Remove EavAttribute entity from collection.
     *
     * @param \Entity\EavAttribute $eavAttribute
     * @return \Entity\Car
     */
    public function removeEavAttribute(EavAttribute $eavAttribute)
    {
        $eavAttribute->removeCar($this);
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
        return array('id', 'sku', 'name', 'pinyin', 'pinyin_jm', 'category_entity_id', 'create_at', 'update_at');
    }
}