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
 * Exception that is thrown if the specified group does not exist in the settings.
 *
 * @package Configuration
 * @version 1.3.5
 */
class ezcConfigurationUnknownGroupException extends ezcConfigurationException
{
    /**
     * Constructs a new ezcConfigurationUnknownGroupException.
     *
     * @param string $groupName
     * @return void
     */
    function __construct( $groupName )
    {
        parent::__construct( "The settings group '{$groupName}' does not exist." );
    }
}
?>
