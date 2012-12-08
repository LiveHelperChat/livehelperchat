<?php
/**
 * File containing the ezcMvcNoRoutesException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when the createRoutes() method does not return any routes.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcNoRoutesException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcNoRoutesException
     */
    public function __construct()
    {
        parent::__construct( "No routes are defined in the router." );
    }
}
?>
