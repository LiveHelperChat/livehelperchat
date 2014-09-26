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
 * Exception that is thrown if the specified configuration does not exist in the system.
 *
 * @package Configuration
 * @version 1.3.5
 */
class ezcConfigurationUnknownConfigException extends ezcConfigurationException
{
    /**
     * Constructs a new ezcConfigurationUnknownConfigException.
     *
     * @param string $configurationName
     * @return void
     */
    function __construct( $configurationName )
    {
        parent::__construct( "The configuration '{$configurationName}' does not exist." );
    }
}
?>
