<?php
/**
 * Created by PhpStorm.
 * User: luca
 * Date: 03/03/18
 * Time: 15.36
 */

namespace Jackal\ImageMerge\Test\FunctionalTest;


use Jackal\ImageMerge\Builder\ImageBuilder;
use Jackal\ImageMerge\Command\Effect\Distortion;
use Jackal\ImageMerge\Command\Options\MultiCoordinateCommandOption;
use Jackal\ImageMerge\Model\Coordinate;
use Jackal\ImageMerge\Model\File\FileObject;

class DistortionTest extends FunctionalTest
{
    public function testDistortion(){
        $builder = ImageBuilder::fromFile(new FileObject(__DIR__.'/Resources/monkey.jpg'));


        $builder->addCommand(Distortion::CLASSNAME,new MultiCoordinateCommandOption([
            new Coordinate(26,0),
            new Coordinate(114,23),
            new Coordinate(128,100),
            new Coordinate(0,123),
        ]));

        $this->assertJPGSameImage($builder->getImage(),__DIR__.'/Resources/monkey_distorted.jpg');
    }
}