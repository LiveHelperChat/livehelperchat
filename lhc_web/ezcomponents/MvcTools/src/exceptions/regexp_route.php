<?php
/**
 * File containing the ezcMvcRegexpRouteException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when the prefix() method can't prefix the route's
 * pattern.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcRegexpRouteException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcRegexpRouteException
     *
     * @param string $message
     */
    public function __construct( $message )
    {
        parent::__construct( $message );
    }
}
?>
