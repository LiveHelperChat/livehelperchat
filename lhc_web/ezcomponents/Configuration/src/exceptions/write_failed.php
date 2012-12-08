<?php
/**
 * File containing the ezcConfigurationException class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown if the write operation for the configuration failed.
 *
 * @package Configuration
 * @version 1.3.5
 */
class ezcConfigurationWriteFailedException extends ezcConfigurationException
{
    /**
     * Constructs a new ezcConfigurationWriteFailedException.
     *
     * @param string $path
     * @return void
     */
    function __construct( $path )
    {
        parent::__construct( "The file could not be stored in '{$path}'." );
    }
}
?>
