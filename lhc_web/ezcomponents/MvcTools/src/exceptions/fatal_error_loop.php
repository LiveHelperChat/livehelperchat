<?php
/**
 * File containing the ezcMvcFatalErrorLoopException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when a fatal error request generates another fatal
 * error request.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcFatalErrorLoopException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcFatalErrorLoopException
     *
     * @param ezcMvcRequest $request
     */
    public function __construct( ezcMvcRequest $request )
    {
        $id = "\"{$request->host}\", \"{$request->uri}\" ({$request->requestId})";
        parent::__construct( "The request {$id} results in an infinite fatal error loop." );
    }
}
?>
