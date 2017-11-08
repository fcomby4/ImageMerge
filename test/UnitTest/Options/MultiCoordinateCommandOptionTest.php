<?php
/**
 * Created by PhpStorm.
 * User: luca
 * Date: 07/11/17
 * Time: 17.40
 */

namespace Jackal\ImageMerge\Test\Options;


use Jackal\ImageMerge\Command\Options\MultiCoordinateCommandOption;
use Jackal\ImageMerge\Command\Options\SingleCoordinateCommandOption;
use Jackal\ImageMerge\Model\Coordinate;
use PHPUnit\Framework\TestCase;

class MultiCoordinateCommandOptionTest extends TestCase
{
    public function testMultiCoordinateCommandOptionObject(){

        $object = new MultiCoordinateCommandOption([
            new SingleCoordinateCommandOption(new Coordinate(10,20)),
            new SingleCoordinateCommandOption(new Coordinate(100,20)),
            new SingleCoordinateCommandOption(new Coordinate(10,100)),
            new SingleCoordinateCommandOption(new Coordinate(100,100))
        ]);

        $this->assertEquals(10,$object->getCoordinates()[0]);
        $this->assertEquals(20,$object->getCoordinates()[1]);

        $this->assertEquals(100,$object->getCoordinates()[2]);
        $this->assertEquals(20,$object->getCoordinates()[3]);

        $this->assertEquals(10,$object->getCoordinates()[4]);
        $this->assertEquals(100,$object->getCoordinates()[5]);

        $this->assertEquals(100,$object->getCoordinates()[6]);
        $this->assertEquals(100,$object->getCoordinates()[7]);

        $this->assertEquals(10,$object->getMinX());
        $this->assertEquals(100,$object->getMaxX());

        $this->assertEquals(20,$object->getMinY());
        $this->assertEquals(100,$object->getMaxY());

        $this->assertEquals(90,$object->getCropWidth());
        $this->assertEquals(80,$object->getCropHeight());
    }
}