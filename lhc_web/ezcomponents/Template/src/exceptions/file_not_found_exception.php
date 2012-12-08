<?php
/**
 * File containing the ezcTemplateFileNotFoundException class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception for problems when accessing template files which does not exists.
 *
 * @package Template
 * @version 1.4.2
 */
class ezcTemplateFileNotFoundException extends ezcTemplateException
{
    /**
     * Initialises the exception with the template file path.
     *
     * @param string $stream The stream path to the template file which could not be
     * found.
     */
    public function __construct( $stream )
    {
        parent::__construct( "The requested template file '{$stream}' does not exist." );
    }
}
?>
