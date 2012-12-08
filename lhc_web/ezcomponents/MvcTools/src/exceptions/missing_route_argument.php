<?php
/**
 * File containing the ezcMvcMissingRouteArgumentException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when a reverse route is requested with a missing argument
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcMissingRouteArgumentException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcMissingRouteArgumentException
     *
     * @param string $pattern
     * @param string $argument
     */
    public function __construct( $pattern, $argument )
    {
        $message = "The argument '{$argument}' was not specified while generating a URL out of the route with pattern '{$pattern}'.";
        parent::__construct( $message );
    }
}
?>
