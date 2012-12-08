<?php
/**
 * File containing the ezcDocumentParserException class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, when the RST parser could not parse asome token sequence.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentParserException extends ezcDocumentConversionException
{
    /**
     * Error string
     *
     * String describing the general type of this error
     *
     * @var string
     */
    protected $errorString = 'Parse error';
}

?>
