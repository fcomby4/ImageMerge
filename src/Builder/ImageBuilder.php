<?php

namespace Jackal\ImageMerge\Builder;

use Jackal\ImageMerge\Command\Asset\ImageAssetCommand;
use Jackal\ImageMerge\Command\Asset\SquareAssetCommand;
use Jackal\ImageMerge\Command\Asset\TextAssetCommand;
use Jackal\ImageMerge\Command\BlurCommand;
use Jackal\ImageMerge\Command\BorderCommand;
use Jackal\ImageMerge\Command\BrightnessCommand;
use Jackal\ImageMerge\Command\ContrastCommand;
use Jackal\ImageMerge\Command\CropCommand;
use Jackal\ImageMerge\Command\CropPolygonCommand;
use Jackal\ImageMerge\Command\FlipHorizontalCommand;
use Jackal\ImageMerge\Command\FlipVerticalCommand;
use Jackal\ImageMerge\Command\GrayScaleCommand;
use Jackal\ImageMerge\Command\Options\BorderCommandOption;
use Jackal\ImageMerge\Command\Options\CommandOptionInterface;
use Jackal\ImageMerge\Command\Options\CropCommandOption;
use Jackal\ImageMerge\Command\Options\DimensionCommandOption;
use Jackal\ImageMerge\Command\Options\DoubleCoordinateColorCommandOption;
use Jackal\ImageMerge\Command\Options\LevelCommandOption;
use Jackal\ImageMerge\Command\Options\MultiCoordinateCommandOption;
use Jackal\ImageMerge\Command\Options\SingleCoordinateCommandOption;
use Jackal\ImageMerge\Command\Options\SingleCoordinateFileObjectCommandOption;
use Jackal\ImageMerge\Command\Options\TextCommandOption;
use Jackal\ImageMerge\Command\PixelCommand;
use Jackal\ImageMerge\Command\ResizeCommand;
use Jackal\ImageMerge\Command\RotateCommand;
use Jackal\ImageMerge\Factory\CommandFactory;
use Jackal\ImageMerge\Metadata\Metadata;
use Jackal\ImageMerge\Model\Color;
use Jackal\ImageMerge\Model\Coordinate;
use Jackal\ImageMerge\Model\File\FileObjectInterface;
use Jackal\ImageMerge\Model\File\FileTempObject;
use Jackal\ImageMerge\Model\Image;
use Jackal\ImageMerge\Model\Text\Text;

class ImageBuilder
{
    /**
     * @var Image
     */
    protected $image;

    private function __construct()
    {
    }


    /**
     * @param Image $image
     * @return ImageBuilder
     */
    public static function fromImage(Image $image){
        $b = new self();
        $b->image = $image;

        return $b;
    }

    /**
     * @param FileObjectInterface $file
     * @return ImageBuilder
     */
    public static function fromFile(FileObjectInterface $file){

        $b = new self();
        $b->image = Image::fromFile($file);
        $b->image->addMetadata(new Metadata($file));

        return $b;
    }

    /**
     * @param $contentString
     * @return ImageBuilder
     */
    public static function fromString($contentString){

        $b = new self();
        $b->image = Image::fromString($contentString);
        $b->image->addMetadata(new Metadata(FileTempObject::fromString($contentString)));

        return $b;
    }

    /**
     * @param $className
     * @param CommandOptionInterface|null $options
     * @return $this
     * @throws \Exception
     */
    public function addCommand($className, CommandOptionInterface $options = null)
    {
        $command = CommandFactory::getInstance($className, $this->image, $options);
        $this->image = $command->execute();
        return $this;
    }

    /**
     * @param $level
     * @return ImageBuilder
     * @throws \Exception
     */
    public function blur($level)
    {
        return $this->addCommand(BlurCommand::CLASSNAME, new LevelCommandOption($level));
    }

    /**
     * @param $width
     * @param $height
     * @return ImageBuilder
     * @throws \Exception
     */
    public function resize($width, $height)
    {
        return $this->addCommand(ResizeCommand::CLASSNAME, new DimensionCommandOption($width, $height));
    }

    /**
     * @param $degree
     * @return ImageBuilder
     * @throws \Exception
     */
    public function rotate($degree)
    {
        return $this->addCommand(RotateCommand::CLASSNAME,
            new LevelCommandOption($degree)
        );
    }

    /**
     * @return ImageBuilder
     * @throws \Exception
     */
    public function flipVertical(){
        return $this->addCommand(FlipVerticalCommand::CLASSNAME);
    }

    /**
     * @return ImageBuilder
     * @throws \Exception
     */
    public function flipHorizontal(){
        return $this->addCommand(FlipHorizontalCommand::CLASSNAME);
    }

    /**
     * @param Text $text
     * @param $x1
     * @param $y1
     * @return ImageBuilder
     * @throws \Exception
     */
    public function addText(Text $text, $x1,$y1)
    {
        return $this->addCommand(TextAssetCommand::CLASSNAME,
            new TextCommandOption($text, new Coordinate($x1,$y1))
        );
    }

    /**
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @param $color
     * @return ImageBuilder
     * @throws \Exception
     * @throws \Jackal\ImageMerge\Exception\InvalidColorException
     */
    public function addSquare($x1,$y1,$x2,$y2,$color){
        return $this->addCommand(SquareAssetCommand::CLASSNAME,
            new DoubleCoordinateColorCommandOption(
                new Coordinate($x1,$y1),
                new Coordinate($x2,$y2),
                $color)
        );

    }

    /**
     * @param Image $image
     * @param int $x
     * @param int $y
     * @return ImageBuilder
     * @throws \Exception
     */
    public function merge(Image $image, $x = 0, $y = 0)
    {
        $fileObject = FileTempObject::fromString($image->toPNG()->getBody());

        return $this->addCommand(ImageAssetCommand::CLASSNAME,
            new SingleCoordinateFileObjectCommandOption($fileObject, new Coordinate($x, $y))
        );
    }

    /**
     * @param $level
     * @return ImageBuilder
     * @throws \Exception
     */
    public function pixelate($level)
    {
        return $this->addCommand(PixelCommand::CLASSNAME,
            new LevelCommandOption($level)
        );
    }

    /**
     * @param $stroke
     * @param string $colorHex
     * @return ImageBuilder
     * @throws \Exception
     */
    public function border($stroke, $colorHex = Color::WHITE)
    {
        return $this->addCommand(BorderCommand::CLASSNAME,
            new BorderCommandOption($stroke, $colorHex)
        );
    }

    /**
     * @param $newWidth
     * @param $newHeight
     * @return ImageBuilder
     * @throws \Exception
     */
    public function cropCenter($newWidth, $newHeight)
    {
        $width = $this->image->getWidth();
        $height = $this->image->getHeight();

        if ($newWidth > $width || $newHeight > $height) {
            throw new \Exception(sprintf('Crop area exceed, max dimensions are: %s X %s', $width, $height));
        }

        $x = ($width- $newWidth) / 2;
        $y = ($height - $newHeight) / 2;

        return $this->addCommand(CropCommand::CLASSNAME, new CropCommandOption(new Coordinate($x, $y), $newWidth, $newHeight));
    }

    /**
     * @param $level
     * @return ImageBuilder
     * @throws \Exception
     */
    public function brightness($level)
    {
        return $this->addCommand(BrightnessCommand::CLASSNAME,
            new LevelCommandOption($level)
        );
    }

    /**
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     * @return ImageBuilder
     * @throws \Exception
     */
    public function crop($x, $y, $width, $height)
    {
        return $this->addCommand(CropCommand::CLASSNAME,
            new CropCommandOption(new Coordinate($x, $y), $width, $height)
        );
    }

    /**
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @param $x3
     * @param $y3
     * @return ImageBuilder
     * @throws \Exception
     */
    public function cropPolygon($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $points = func_get_args();
        $coords = [];

        foreach ($points as $k => $point) {
            if ($k == 0 or ($k %2) == 0) {
                if (isset($points[$k + 1])) {
                    $x = $point;
                    $y = $points[$k + 1];
                    $coords[] = new SingleCoordinateCommandOption(new Coordinate($x, $y));
                }
            }
        }

        return $this->addCommand(CropPolygonCommand::CLASSNAME,
            new MultiCoordinateCommandOption($coords)
        );
    }

    /**
     * @param null $width
     * @param null $height
     * @return $this
     * @throws \Exception
     */
    public function thumbnail($width = null, $height = null)
    {
        /** @var DimensionCommandOption $options */
        $options = new DimensionCommandOption($width,$height);

        if (!$options->getWidth() and !$options->getHeight()) {
            throw new \Exception('Both width and height are empy value');
        }

        if (!$options->getWidth()) {
            $options->add('width', $options->getWidth() ? $options->getWidth() : round($this->image->getAspectRatio() * $options->getHeight()));
        }

        if (!$options->getHeight()) {
            $options->add('height', $options->getHeight() ? $options->getHeight() : round($options->getWidth() / $this->image->getAspectRatio()));
        }

        $thumbAspect = $options->getWidth() / $options->getHeight();

        if ($this->image->getAspectRatio() >= $thumbAspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $newHeight = $options->getHeight();
            $newWidth = $this->image->getWidth() / ($this->image->getHeight() / $options->getHeight());
        } else {
            // If the thumbnail is wider than the image
            $newHeight = $this->image->getHeight() / ($this->image->getWidth() / $options->getWidth());
            $newWidth = $options->getWidth();
        }

        $this->resize($newWidth, $newHeight);
        $this->cropCenter($options->getWidth(), $options->getHeight());

        return $this;
    }

    /**
     * @return ImageBuilder
     * @throws \Exception
     */
    public function grayScale()
    {
        return $this->addCommand(GrayScaleCommand::CLASSNAME, null);
    }

    /**
     * @param $level
     * @return ImageBuilder
     * @throws \Exception
     */
    public function contrast($level){

        return $this->addCommand(ContrastCommand::CLASSNAME,new LevelCommandOption($level));
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
