<?php
/**
 * File containing the ezcTemplateInvalidCompiledFileException class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception for missing invalid compiled files.
 *
 * @package Template
 * @version 1.4.2
 */
class ezcTemplateInvalidCompiledFileException extends ezcTemplateException
{
    /**
     * Initialises the exception with the location object $location which
     * contains the locator which is missing.
     *
     * @param string $identifier The unique identifier for the compiled file.
     * @param string $path The path to the compiled file.
     */
    public function __construct( $identifier, $path )
    {
        if ( !file_exists( $path ) )
        {
            parent::__construct( "The compiled template file '{$path}' does not exist." );
        }
        elseif ( !is_readable( $path ) )
        {
            parent::__construct( "The compiled template file '{$path}' cannot be read." );
        }
    }
}
?>
