<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity\Brand
 *
 * 品牌
 *
 * @ORM\Entity(repositoryClass="BrandRepository")
 * @ORM\Table(name="brand")
 */
class Brand
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
    protected $brand_code;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $brand_type;

    /**
     * 品牌的类型(车,配件)
     *
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $label;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Entity\Brand
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
     * Set the value of brand_code.
     *
     * @param string $brand_code
     * @return \Entity\Brand
     */
    public function setBrandCode($brand_code)
    {
        $this->brand_code = $brand_code;

        return $this;
    }

    /**
     * Get the value of brand_code.
     *
     * @return string
     */
    public function getBrandCode()
    {
        return $this->brand_code;
    }

    /**
     * Set the value of brand_type.
     *
     * @param string $brand_type
     * @return \Entity\Brand
     */
    public function setBrandType($brand_type)
    {
        $this->brand_type = $brand_type;

        return $this;
    }

    /**
     * Get the value of brand_type.
     *
     * @return string
     */
    public function getBrandType()
    {
        return $this->brand_type;
    }

    /**
     * Set the value of label.
     *
     * @param string $label
     * @return \Entity\Brand
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function __sleep()
    {
        return array('id', 'brand_code', 'brand_type', 'label');
    }
}