<?php
/**
 * Created by PhpStorm.
 * User: luca
 * Date: 11/09/17
 * Time: 11.33
 */

namespace Jackal\ImageMerge\Command\Options;

use Jackal\ImageMerge\Model\Color;
use Jackal\ImageMerge\Utils\ColorUtils;

class SingleCoordinateColorCommandOption extends SingleCoordinateCommandOption
{
    public function __construct($x1, $y1, $colorHex)
    {
        parent::__construct($x1, $y1);
        $this->add('color', new Color($colorHex));
    }

    public function getColor(){
        return $this->get('color');
    }
}
