<?php
/**
 * This file contains the ezcMvcReversibleRoute interface.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * The interface that should be implemented by the different route types
 * that allow Url generation from the route definition.
 *
 * @package MvcTools
 * @version 1.1.3
 */
interface ezcMvcReversibleRoute
{
    /**
     * Generates an URL back out of a route, including possible arguments
     *
     * @param array $arguments
     */
    public function generateUrl( array $arguments = null );
}
?>
