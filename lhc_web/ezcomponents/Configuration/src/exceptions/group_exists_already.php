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
 * Exception that is thrown if a group is tried to be added, while it already
 * exists.
 *
 * @package Configuration
 * @version 1.3.5
 */
class ezcConfigurationGroupExistsAlreadyException extends ezcConfigurationException
{
    /**
     * Constructs a new ezcConfigurationGroupExistsAlreadyException for the group $groupName.
     *
     * @param string $groupName
     * @return void
     */
    function __construct( $groupName )
    {
        parent::__construct( "The settings group '{$groupName}' exists already." );
    }
}
?>
