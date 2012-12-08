<?php
/**
 * File containing the ezcMvcRoute class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * The interface that should be implemented by the different route types.
 * Each route is responsible for checking whether it matches data in the
 * $request. It also need to support to prefix itself with a route-type
 * dependent prefix string.
 *
 * @package MvcTools
 * @version 1.1.3
 */
interface ezcMvcRoute
{
    /**
     * Returns routing information if the route matched, or null in case the
     * route did not match.
     *
     * @param ezcMvcRequest $request
     * @return null|ezcMvcRoutingInformation
     */
    public function matches( ezcMvcRequest $request );

    /**
     * Adds a prefix to the route.
     *
     * @param mixed $prefix Prefix to add, for example: '/blog'
     */
    public function prefix( $prefix );
}
?>
