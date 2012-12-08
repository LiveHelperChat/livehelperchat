<?php
/**
 * File containing the ezcDocumentPdfBoundingBox class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Bounding box
 *
 * Simple struct, representing a bounding box, used to specify rectangular 
 * covered or availalable areas.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfBoundingBox extends ezcBaseStruct
{
    /**
     * Vertical box position
     *
     * @var float
     */
    public $x;

    /**
     * Horizontal box position
     *
     * @var float
     */
    public $y;

    /**
     * Box width
     *
     * @var float
     */
    public $width;

    /**
     * Box height
     *
     * @var float
     */
    public $height;

    /**
     * Construct a bounding box from its dimensions
     *
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @return void
     */
    public function __construct( $x, $y, $width, $height )
    {
        $this->x      = (float) $x;
        $this->y      = (float) $y;
        $this->width  = (float) $width;
        $this->height = (float) $height;
    }
}
?>
