<?php
/**
 * File containing the ezcMvcActionNotFoundException class
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when no action method exists for a route.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcActionNotFoundException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcActionNotFoundException
     *
     * @param string $action
     */
    public function __construct( $action )
    {
        $message = "The action '{$action}' does not exist.";
        parent::__construct( $message );
    }
}
?>
