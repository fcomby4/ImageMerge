<?php

namespace Jackal\ImageMerge\Command\Asset;

use Jackal\ImageMerge\Command\AbstractCommand;
use Jackal\ImageMerge\Command\Options\DoubleCoordinateColorCommandOption;
use Jackal\ImageMerge\Model\Image;
use Jackal\ImageMerge\Utils\ColorUtils;

/**
 * Class SquareAssetCommand
 * @package Jackal\ImageMerge\Command\Asset
 */
class SquareAssetCommand extends AbstractCommand
{
    /**
     * @param Image $image
     * @return Image
     */
    public function execute(Image $image)
    {
        /** @var DoubleCoordinateColorCommandOption $options */
        $options = $this->options;

        $color = ColorUtils::colorIdentifier($image->getResource(), $options->getColor());
        imagefilledrectangle($image->getResource(),
            $options->getCoordinate1()->getX(),
            $options->getCoordinate1()->getY(),
            $options->getCoordinate2()->getX(),
            $options->getCoordinate2()->getY(),
            $color
        );

        return $image;
    }
}
