<?php

namespace Jackal\ImageMerge\Command;

use Jackal\ImageMerge\Command\Options\DimensionCommandOption;
use Jackal\ImageMerge\Model\Image;
use Jackal\ImageMerge\ValueObject\Dimention;

class ResizeCommand extends AbstractCommand
{

    /**
     * ResizeCommand constructor.
     * @param Image $image
     * @param DimensionCommandOption $options
     */
    public function __construct(Image $image, DimensionCommandOption $options)
    {
        parent::__construct($image, $options);
    }

    /**
     * @return Image
     */
    public function execute()
    {
        if (!$this->options->getDimention()->getWidth()) {
            $this->options->add('dimention', new Dimention(round($this->image->getAspectRatio() * $this->options->getDimention()->getHeight()), $this->options->getDimention()->getHeight()));
        }

        if (!$this->options->getDimention()->getHeight()) {
            $this->options->add('dimention', new Dimention($this->options->getDimention()->getWidth(), round($this->options->getDimention()->getWidth() / $this->image->getAspectRatio())));
        }

        $width = $this->options->getDimention()->getWidth();
        $height = $this->options->getDimention()->getHeight();

        if ($this->image->getWidth() != $width or $this->image->getHeight() != $height) {
            $resourceResized = imagecreatetruecolor($width, $height);
            imagealphablending($resourceResized, false);
            imagesavealpha($resourceResized,true);
            $transparent = imagecolorallocatealpha($resourceResized, 255, 255, 255, 127);
            imagecolortransparent($resourceResized,$transparent);
            imagecopyresampled($resourceResized, $this->image->getResource(), 0, 0, 0, 0, $width, $height, $this->image->getWidth(), $this->image->getHeight());

            return $this->image->assignResource($resourceResized);
        }
        return $this->image;
    }
}
