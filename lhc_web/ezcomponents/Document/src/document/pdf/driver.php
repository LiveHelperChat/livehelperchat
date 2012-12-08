<?php
/**
 * File containing the ezcDocumentPdfDriver class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base class for driver implementations.
 *
 * Driver implement the actual creation of PDF documents. They offer a simple
 * unified API for PDF creation and proxy them to the actual PDF
 * implementation, which will create the binary data.
 *
 * The unit used for all values passed to methods of this class is milimeters.
 * The extending drivers need to transform these values in a representation
 * they can use.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentPdfDriver
{
    /**
     * Normal text
     */
    const FONT_PLAIN     = 0;

    /**
     * Bold text
     */
    const FONT_BOLD      = 1;

    /**
     * Italic text
     */
    const FONT_OBLIQUE   = 2;

    /**
     * PDF options
     * 
     * @var ezcDocumentPdfOptions
     */
    protected $options;

    /**
     * Construct driver
     *
     * Creates a new PDF driver.
     *
     * @return void
     */
    public function __construct()
    {
        $this->options = new ezcDocumentPdfOptions();
    }

    /**
     * Set compression
     *
     * Set whether the generated PDF should be compressed or not.
     * 
     * @param bool $compress 
     * @return void
     */
    public function setOptions( ezcDocumentPdfOptions $options )
    {
        $this->options = $options;
    }

    /**
     * Create a new page
     *
     * Create a new page in the PDF document with the given width and height.
     *
     * @param float $width
     * @param float $height
     * @return void
     */
    abstract public function createPage( $width, $height );

    /**
     * Set text formatting option
     *
     * Set a text formatting option. The names of the options are the same used
     * in the PCSS files and need to be translated by the driver to the proper
     * backend calls.
     *
     *
     * @param string $type
     * @param mixed $value
     * @return void
     */
    abstract public function setTextFormatting( $type, $value );

    /**
     * Calculate the rendered width of the current word
     *
     * Calculate the width of the passed word, using the currently set text
     * formatting options.
     *
     * @param string $word
     * @return float
     */
    abstract public function calculateWordWidth( $word );

    /**
     * Get current line height
     *
     * Return the current line height in millimeter based on the current font
     * and text rendering settings.
     *
     * @return float
     */
    abstract public function getCurrentLineHeight();

    /**
     * Draw word at given position
     *
     * Draw the given word at the given position using the currently set text
     * formatting options.
     *
     * The coordinate specifies the left bottom edge of the words bounding box.
     *
     * @param float $x
     * @param float $y
     * @param string $word
     * @return void
     */
    abstract public function drawWord( $x, $y, $word );

    /**
     * Draw image
     *
     * Draw image at the defined position. The first parameter is the
     * (absolute) path to the image file, and the second defines the type of
     * the image. If the driver cannot handle this aprticular image type, it
     * should throw an exception.
     *
     * The further parameters define the location where the image should be
     * rendered and the dimensions of the image in the rendered output. The
     * dimensions do not neccesarily match the real image dimensions, and might
     * require some kind of scaling inside the driver depending on the used
     * backend.
     *
     * @param string $file
     * @param string $type
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @return void
     */
    abstract public function drawImage( $file, $type, $x, $y, $width, $height );

    /**
     * Draw a fileld polygon
     *
     * Draw any filled polygon, filled using the defined color. The color
     * should be passed as an array with the keys "red", "green", "blue" and
     * optionally "alpha". Each key should have a value between 0 and 1
     * associated.
     *
     * The polygon itself is specified as an array of two-tuples, specifying
     * the x and y coordinate of the point.
     * 
     * @param array $points 
     * @param array $color 
     * @return void
     */
    public function drawPolygon( array $points, array $color )
    {
        // @apichange This method should be declared abstract, but can't be
        // because this would change the internal API.
        return;
    }

    /**
     * Draw a polyline
     *
     * Draw any non-filled polygon, filled using the defined color. The color
     * should be passed as an array with the keys "red", "green", "blue" and
     * optionally "alpha". Each key should have a value between 0 and 1
     * associated.
     *
     * The polyline itself is specified as an array of two-tuples, specifying
     * the x and y coordinate of the point.
     *
     * The thrid parameter defines the width of the border and the last
     * parameter may optionally be set to false to not close the polygon (draw
     * another line from the last point to the first one).
     * 
     * @param array $points 
     * @param array $color 
     * @param float $width 
     * @param bool $close 
     * @return void
     */
    public function drawPolyline( array $points, array $color, $width, $close = true )
    {
        // @apichange This method should be declared abstract, but can't be
        // because this would change the internal API.
        return;
    }

    /**
     * Add an external link
     *
     * Add an external link to the rectangle specified by its top-left
     * position, width and height. The last parameter is the actual URL to link
     * to.
     * 
     * @param float $x 
     * @param float $y 
     * @param float $width 
     * @param float $height 
     * @param string $url 
     * @return void
     */
    public function addExternalLink( $x, $y, $width, $height, $url )
    {
        // @apichange This method should be declared abstract, but can't be
        // because this would change the internal API.
        return;
    }

    /**
     * Add an internal link
     *
     * Add an internal link to the rectangle specified by its top-left
     * position, width and height. The last parameter is the target identifier
     * to link to.
     * 
     * @param float $x 
     * @param float $y 
     * @param float $width 
     * @param float $height 
     * @param string $target 
     * @return void
     */
    public function addInternalLink( $x, $y, $width, $height, $target )
    {
        // @apichange This method should be declared abstract, but can't be
        // because this would change the internal API.
        return;
    }

    /**
     * Add an internal link target
     *
     * Add an internal link to the current page. The last parameter
     * is the target identifier.
     * 
     * @param string $id 
     * @return void
     */
    public function addInternalLinkTarget( $id )
    {
        // @apichange This method should be declared abstract, but can't be
        // because this would change the internal API.
        return;
    }

    /**
     * Register a font
     *
     * Registers a font, which can be used by its name later in the driver. The 
     * given type is either self::FONT_PLAIN or a bitwise combination of self::FONT_BOLD 
     * and self::FONT_OBLIQUE.
     *
     * The third paramater specifies an array of pathes with references to font 
     * definition files. Multiple pathes may be specified to provide the same 
     * font using different types, because not all drivers may process all font 
     * types.
     * 
     * @param string $name 
     * @param int $type 
     * @param array $pathes 
     * @return void
     */
    public function registerFont( $name, $type, array $pathes )
    {
        // @apichange This method should be declared abstract, but can't be
        // because this would change the internal API.
        return;
    }

    /**
     * Set metadata
     *
     * Set document meta data. The meta data types are identified by a list of 
     * keys, common to PDF, like: title, author, subject, created, modified.
     *
     * The values are passed like embedded in the docbook document and might 
     * need to be reformatted.
     * 
     * @param string $key 
     * @param string $value 
     * @return void
     */
    public function setMetaData( $key, $value )
    {
        // @apichange This method should be declared abstract, but can't be
        // because this would change the internal API.
        return;
    }

    /**
     * Generate and return PDF
     *
     * Return the generated binary PDF content as a string.
     *
     * @return string
     */
    abstract public function save();
}

?>
