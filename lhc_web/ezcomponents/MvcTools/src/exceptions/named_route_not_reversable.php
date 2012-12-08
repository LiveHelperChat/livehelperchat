<?php
/**
 * File containing the ezcMvcNamedRouteNotReversableException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when a reverse route is requested on a route class
 * that does not support it.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcNamedRouteNotReversableException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcNamedRouteNotReversableException
     *
     * @param string $routeName
     * @param string $routerClass
     */
    public function __construct( $routeName, $routerClass )
    {
        $message = "The route with name '{$routeName}' is of the '{$routerClass}' class, which does not support reversed route generation.";
        parent::__construct( $message );
    }
}
?>
