<?php
/**
 * File containing the ezcMvcView class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * The abstract view that you need to inherit from to supply your view zones.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
abstract class ezcMvcView
{
    /**
     * Holds the request object
     *
     * @var ezcMvcRequest
     */
    protected $request;

    /**
     * Holds the result object, that's the result of all the views.
     *
     * @var ezcMvcResult
     */
    protected $result;

    /**
     * Creates the view object
     *
     * @param ezcMvcRequest $request
     * @param ezcMvcResult  $result
     */
    public function __construct( ezcMvcRequest $request, ezcMvcResult $result )
    {
        $this->request = $request;
        $this->result  = $result;
    }

    /**
     * The user-implemented that returns the zones.
     *
     * This method creates all the zones that are needed to render a view. A
     * zone is an array of elements that implement a view handler. The view
     * handlers do not have to be of the same type, as long as they implement
     * the ezcMvcViewHandler interface.
     *
     * The $layout parameter can be used to determine whether a "page layout" should
     * be added to the list of zones. This can be useful in case you're incorporating
     * many different applications. The $layout parameter will be set to true automatically
     * for the top level createZones() method, which can then chose to add zones from
     * other views as well. The createZones() methods from those other views should
     * have the $layout parameter set to false.
     *
     * @param bool $layout
     *
     * @return array(ezcMvcViewHandler)
     */
    abstract public function createZones( $layout );

    /**
     * Creates a controller from the set of routes.
     *
     * This method is run by the createResponse() method to obtain a rendered
     * result from data from the controller. It uses the user implemented
     * createZones() method from the inherited class to fetch the different
     * zones of the view, and then loops over these zones in order. Each zone's
     * results are made available to subsequent zones. Each zone will be
     * processed by a view handler of the ezcMvcViewHandler class.
     *
     * @throws ezcMvcNoZonesException when there are no zones defined.
     * @throws ezcBaseValueException when one of the returned zones was not
     *         actually an object implementing the ezcMvcViewHandler interface.
     * @return mixed
     */
    protected function createResponseBody()
    {
        $processed = array();
        $zones = $this->createZones( true );
        if ( ezcBase::inDevMode() && ( !is_array( $zones ) || !count( $zones ) ) )
        {
            throw new ezcMvcNoZonesException();
        }

        // get the last zone
        $lastZone = array_pop( $zones );
        array_push( $zones, $lastZone );

        foreach ( $zones as $zone )
        {
            if ( ezcBase::inDevMode() && !$zone instanceof ezcMvcViewHandler )
            {
                throw new ezcBaseValueException( 'zone', $zone, 'instance of ezcMvcViewHandler' );
            }

            // Get the variables returned by the controller for the view
            foreach ( $this->result->variables as $propertyName => $propertyValue )
            {
                // Send it verbatim to the template processor
                $zone->send( $propertyName, $propertyValue );
            }

            // Zones are additional templates that the final view should be built
            // with. The main page layout is the last zone returned from
            // createZones() method.
            foreach ( $processed as $processedZone )
            {
                $zone->send( $processedZone->getName(), $processedZone->getResult() );
            }

            $zone->process( $zone === $lastZone );

            $processed[] = $zone;
        }
        return $zone->getResult();
    }

    /**
     * This method is called from the dispatched to create the rendered response from
     * the controller's result.
     *
     * It calls the createResponseBody() to do the actual rendering. If any exception
     * is thrown in any of the view handlers, this method catches it and returns that
     * exception object to the dispatcher. The dispatcher must therefore check whether
     * an exception object was returned. In case there was an exception, the dispatcher
     * can take appropriate actions such as rendering a fatal error template.
     *
     * If everything goes well, this method returns an ezcMvcResponse object with the
     * headers from the $result, and the response body from the processed views.
     *
     * @return ezcMvcResponse
     */
    public function createResponse()
    {
        $resultBody = $this->createResponseBody();
        $result = $this->result;
        return new ezcMvcResponse( $result->status, $result->date,
            $result->generator, $result->cache, $result->cookies,
            $result->content, $resultBody );
    }
}
?>
