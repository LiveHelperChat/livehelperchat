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
 * Exception that is thrown if no configuration object has been set to operate
 * on. The operation cannot continue with this object.
 *
 * @package Configuration
 * @version 1.3.5
 */
class ezcConfigurationNoConfigObjectException extends ezcConfigurationException
{
    /**
     * Constructs a new ezcConfigurationNoConfigObjectException.
     *
     * @return void
     */
    function __construct()
    {
        parent::__construct( 'There is no config object to save.' );
    }
}
?>
