<?php
/**
 * File containing the ezcDocumentPdfImageHandler class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * PDF image handler
 *
 * Abstract base class for image handlers. Should be extended by classes, which
 * can handle a set of image types and provide information about image mime
 * types and dimensions.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
abstract class ezcDocumentPdfImageHandler
{
    /**
     * Can this handler handle the passed image?
     *
     * Returns a boolean value indicatin whether the current handler can handle
     * the passed image file.
     *
     * @param string $file
     * @return bool
     */
    abstract public function canHandle( $file );

    /**
     * Get image dimensions
     *
     * Return an array with the image dimensions. The array will look like:
     * array( ezcDocumentPcssMeasure $width, ezcDocumentPcssMeasure $height ).
     *
     * @param string $file
     * @return array
     */
    abstract public function getDimensions( $file );

    /**
     * Get image mime type
     *
     * Return a string with the image mime type, identifying the type of the
     * image.
     *
     * @param string $file
     * @return string
     */
    abstract public function getMimeType( $file );
}
?>
