<?php
/**
 * File containing the ezcMvcDispatcherConfiguration class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Configure a dispatcher with an instance of an implementation of this
 * interface.
 *
 * You can use any dispatcher with the same configuration class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
interface ezcMvcDispatcherConfiguration
{
    /**
     * Creates the request parser able to produce a relevant request object
     * for this session.
     *
     * @return ezcMvcRequestParser
     */
    public function createRequestParser();

    /**
     * Create the router able to instantiate a relevant controller for this
     * request.
     *
     * @param ezcMvcRequest $request
     *
     * @return ezcMvcRouter
     */
    public function createRouter( ezcMvcRequest $request );

    /**
     * Creates the view handler that is able to process the result.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @param ezcMvcResult $result
     *
     * @return ezcMvcView
     */
    public function createView( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result );

    /**
     * Creates a response writer that uses the response and sends its
     * output.
     *
     * This method should be able to pick different response writers, but the
     * response writer itself will only know about the $response.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @param ezcMvcResult $result
     * @param ezcMvcResponse $response
     *
     * @return ezcMvcResponseWriter
     */
    public function createResponseWriter( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result, ezcMvcResponse $response );

    /**
     * Create the default internal redirect object in case something goes
     * wrong in the views.
     *
     * @param ezcMvcRequest $request
     * @param ezcMvcResult  $result
     * @param Exception     $e
     *
     * @return ezcMvcRedirect
     */
    public function createFatalRedirectRequest( ezcMvcRequest $request, ezcMvcResult $result, Exception $e );

    /**
     * Runs all the pre-routing filters that are deemed necessary depending on
     * information in $request.
     *
     * The pre-routing filters could modify the request data so that a
     * different router can be chosen.
     *
     * @param ezcMvcRequest $request
     */
    public function runPreRoutingFilters( ezcMvcRequest $request );

    /**
     * Runs all the request filters that are deemed necessary depending on
     * information in $routeInfo and $request.
     *
     * This method can return an object of class ezcMvcInternalRedirect in case
     * the filters require this. A reason for this could be in case an
     * authentication filter requires authentication credentials to be passed
     * in through a login form. The method can also not return anything in case
     * no redirect is necessary.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     *
     * @return ezcMvcInternalRedirect|null
     */
    public function runRequestFilters( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request );

    /**
     * Runs all the request filters that are deemed necessary depending on
     * information in $routeInfo, $request and $result.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @param ezcMvcResult $result
     */
    public function runResultFilters( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result );

    /**
     * Runs all the request filters that are deemed necessary depending on
     * information in $routeInfo, $request, $result and $response.
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @param ezcMvcResult  $result
     * @param ezcMvcResponse $response
     */
    public function runResponseFilters( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result, ezcMvcResponse $response );
}
?>
