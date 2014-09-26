<?php
/**
 * File containing the ezcConfigurationManagerNotInitializedException class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown if an invalid class is passed as INI reader to the manager.
 *
 * @package Configuration
 * @version 1.3.5
 */
class ezcConfigurationManagerNotInitializedException extends ezcConfigurationException
{
    /**
     * Constructs a new ezcConfigurationManagerNotInitializedException.
     *
     * @return void
     */
    function __construct()
    {
        parent::__construct( "The manager has not been initialized." );
    }
}
?>
