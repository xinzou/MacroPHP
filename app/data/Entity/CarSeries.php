<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity\CarSeries
 *
 * 车系
 *
 * @ORM\Entity(repositoryClass="CarSeriesRepository")
 * @ORM\Table(name="car_series")
 */
class CarSeries
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $series_code;

    /**
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
     * @return \Entity\CarSeries
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
     * Set the value of series_code.
     *
     * @param string $series_code
     * @return \Entity\CarSeries
     */
    public function setSeriesCode($series_code)
    {
        $this->series_code = $series_code;

        return $this;
    }

    /**
     * Get the value of series_code.
     *
     * @return string
     */
    public function getSeriesCode()
    {
        return $this->series_code;
    }

    /**
     * Set the value of label.
     *
     * @param string $label
     * @return \Entity\CarSeries
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
        return array('id', 'series_code', 'label');
    }
}