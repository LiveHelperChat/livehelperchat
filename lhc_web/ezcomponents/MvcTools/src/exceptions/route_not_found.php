<?php
/**
 * File containing the ezcMvcRouteNotFoundException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when no route matches the request.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcRouteNotFoundException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcRouteNotFoundException
     *
     * @param ezcMvcRequest $request
     */
    public function __construct( ezcMvcRequest $request )
    {
        $id = $request->requestId != '' ? $request->requestId : $request->uri;
        $message = "No route was found that matched request ID '{$id}'.";
        parent::__construct( $message );
    }
}
?>
