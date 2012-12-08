<?php
/**
 * File containing the ezcMvcNamedRouteNotFoundException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when a reverse route is requested with an unknown name.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcNamedRouteNotFoundException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcNamedRouteNotFoundException
     *
     * @param string $routeName
     */
    public function __construct( $routeName )
    {
        $message = "No route was found with the name '{$routeName}'.";
        parent::__construct( $message );
    }
}
?>
