<?php
/**
 * File containing the ezcTemplateFileNotWriteableException class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception for problems when writing to template files.
 *
 * @package Template
 * @version 1.4.2
 */
class ezcTemplateFileNotWriteableException extends ezcTemplateException
{
    /**
     * Constructor
     *
     * @param string $stream    The stream path to the template file which could not be written.
     * @param string $type      The type of the file that could not be read.
     */
    public function __construct( $stream, $type = "requested template file" )
    {
        parent::__construct( "The {$type} '{$stream}' is not writeable." );
    }
}
?>
