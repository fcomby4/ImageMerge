<?php

namespace Jackal\ImageMerge\ValueObject;

/**
 * Class Coordinate
 * @package Jackal\ImageMerge\Model
 */
class Coordinate
{
    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    /**
     * Coordinate constructor.
     * @param $x
     * @param $y
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return integer
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return integer
     */
    public function getY()
    {
        return $this->y;
    }
}