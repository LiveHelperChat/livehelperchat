<?php
/**
 * File containing the ezcMvcConfigurableDispatcher class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * This class implements an example dispatcher that can be configured through
 * ezcMvcDispatcherConfiguration.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
class ezcMvcConfigurableDispatcher implements ezcMvcDispatcher
{
    /**
     * Contains the configuration that determines which request parser, router,
     * view handler and response writer are used.
     *
     * @var ezcMvcDispatcherConfiguration
     */
    protected $configuration;

    /**
     * Creates a new ezcMvcConfigurableDispatcher
     *
     * @param ezcMvcDispatcherConfiguration $configuration
     */
    public function __construct( ezcMvcDispatcherConfiguration $configuration )
    {
        $this->configuration = $configuration;
    }

    /**
     * Creates the controller by using the routing information and request data.
     *
     * @param ezcMvcRoutingInformation $routingInformation
     * @param ezcMvcRequest            $request
     * @return ezcMvcController
     */
    protected function createController( ezcMvcRoutingInformation $routingInformation, ezcMvcRequest $request )
    {
        $controllerClass = $routingInformation->controllerClass;
        $controller = new $controllerClass( $routingInformation->action, $request );
        return $controller;
    }

    /**
     * Checks whether the number of redirects does not exceed the limit, and
     * increases the $redirects count.
     *
     * @throws ezcMvcInfiniteLoopException when the number of redirects exceeds
     *         the limit (25 by default).
     * @param int $redirects
     */
    protected function checkRedirectLimit( &$redirects )
    {
        $redirects++;
        if ( $redirects >= 25 )
        {
            throw new ezcMvcInfiniteLoopException( $redirects );
        }
    }

    /**
     * Uses the configuration to fetch the request parser
     *
     * @throws ezcMvcInvalidConfiguration when the returned object is of the wrong class
     *
     * @return ezcMvcRequestParser
     */
    protected function getRequestParser()
    {
        // create the request parser
        $requestParser = $this->configuration->createRequestParser();
        if ( ezcBase::inDevMode() && !$requestParser instanceof ezcMvcRequestParser )
        {
            throw new ezcMvcInvalidConfiguration( 'requestParser', $requestParser, 'instance of ezcMvcRequestParser' );
        }
        return $requestParser;
    }

    /**
     * Uses the configuration to fetch the router
     *
     * @throws ezcMvcInvalidConfiguration when the returned object is of the wrong class
     *
     * @param ezcMvcRequest $request
     * @return ezcMvcRouter
     */
    protected function getRouter( ezcMvcRequest $request )
    {
        $router = $this->configuration->createRouter( $request );
        if ( ezcBase::inDevMode() && !$router instanceof ezcMvcRouter )
        {
            throw new ezcMvcInvalidConfiguration( 'router', $router, 'instance of ezcMvcRouter' );
        }
        return $router;
    }

    /**
     * Uses the router (through createController()) to fetch the controller
     *
     * @throws ezcMvcInvalidConfiguration when the returned object is of the wrong class
     *
     * @param ezcMvcRoutingInformation $routingInformation
     * @param ezcMvcRequest            $request
     * @return ezcMvcController
     */
    protected function getController( ezcMvcRoutingInformation $routingInformation, ezcMvcRequest $request )
    {
        $controller = $this->createController( $routingInformation, $request );
        if ( ezcBase::inDevMode() && !$controller instanceof ezcMvcController )
        {
            throw new ezcMvcInvalidConfiguration( 'controller', $controller, 'instance of ezcMvcController' );
        }
        $controller->setRouter( $routingInformation->router );
        return $controller;
    }

    /**
     * Uses the configuration to fetch the view handler
     *
     * @throws ezcMvcInvalidConfiguration when the returned object is of the wrong class
     *
     * @param ezcMvcRoutingInformation $routingInformation
     * @param ezcMvcRequest            $request
     * @param ezcMvcResult             $result
     * @return ezcMvcView
     */
    protected function getView( ezcMvcRoutingInformation $routingInformation, ezcMvcRequest $request, ezcMvcResult $result )
    {
        $view = $this->configuration->createView( $routingInformation, $request, $result );
        if ( ezcBase::inDevMode() && !$view instanceof ezcMvcView )
        {
            throw new ezcMvcInvalidConfiguration( 'view', $view, 'instance of ezcMvcView' );
        }
        return $view;
    }

    /**
     * Uses the configuration to fetch a fatal redirect request object
     *
     * @throws ezcMvcInvalidConfiguration when the returned object is of the wrong class
     *
     * @param ezcMvcRequest $request
     * @param ezcMvcResult  $result
     * @param Exception     $e
     * @return ezcMvcRequest
     */
    protected function getFatalRedirectRequest( ezcMvcRequest $request, ezcMvcResult $result, Exception $e )
    {
        if ( $request->isFatal )
        {
            throw new ezcMvcFatalErrorLoopException( $request );
        }
        $request = $this->configuration->createFatalRedirectRequest( $request, new ezcMvcResult, $e );
        $request->isFatal = true;
        if ( ezcBase::inDevMode() && !$request instanceof ezcMvcRequest )
        {
            throw new ezcMvcInvalidConfiguration( 'request', $request, 'instance of ezcMvcRequest' );
        }
        return $request;
    }

    /**
     * Uses the configuration to fetch the response writer
     *
     * @throws ezcMvcInvalidConfiguration when the returned object is of the wrong class
     *
     * @param ezcMvcRoutingInformation $routingInformation
     * @param ezcMvcRequest            $request
     * @param ezcMvcResult             $result
     * @param ezcMvcResponse           $response
     * @return ezcMvcResponseWriter
     */
    protected function getResponseWriter( ezcMvcRoutingInformation $routingInformation, ezcMvcRequest $request, ezcMvcResult $result, ezcMvcResponse $response )
    {
        $responseWriter = $this->configuration->createResponseWriter( $routingInformation, $request, $result, $response );
        if ( ezcBase::inDevMode() && !$responseWriter instanceof ezcMvcResponseWriter )
        {
            throw new ezcMvcInvalidConfiguration( 'responseWriter', $responseWriter, 'instance of ezcMvcResponseWriter' );
        }
        return $responseWriter;
    }

    /**
     * Runs through the request, by using the configuration to obtain correct handlers.
     */
    public function run()
    {
        // initialize infinite loop counter
        $redirects = 0;

        // create the request
        $requestParser = $this->getRequestParser();
        $request = $requestParser->createRequest();

        // start of the request loop
        do
        {
            // do the infinite loop check
            $this->checkRedirectLimit( $redirects );
            $continue = false;

            // run pre-routing filters
            $this->configuration->runPreRoutingFilters( $request );

            // create the router from the configuration
            $router = $this->getRouter( $request );

            // router creates routing information
            try
            {
                $routingInformation = $router->getRoutingInformation();
            }
            catch ( ezcMvcRouteNotFoundException $e )
            {
                $request = $this->getFatalRedirectRequest( $request, new ezcMvcResult, $e );
                $continue = true;
                continue;
            }

            // run request filters
            $filterResult = $this->configuration->runRequestFilters( $routingInformation, $request );

            if ( $filterResult instanceof ezcMvcInternalRedirect )
            {
                $request = $filterResult->request;
                $continue = true;
                continue;
            }

            // create the controller
            $controller = $this->getController( $routingInformation, $request );

            // run the controller
            try
            {
                $result = $controller->createResult();
            }
            catch ( Exception $e )
            {
                $request = $this->getFatalRedirectRequest( $request, new ezcMvcResult, $e );
                $continue = true;
                continue;
            }

            if ( $result instanceof ezcMvcInternalRedirect )
            {
                $request = $result->request;
                $continue = true;
                continue;
            }
            if ( !$result instanceof ezcMvcResult )
            {
                throw new ezcMvcControllerException( "The action '{$routingInformation->action}' of controller '{$routingInformation->controllerClass}' did not return an ezcMvcResult object." );
            }

            $this->configuration->runResultFilters( $routingInformation, $request, $result );

            if ( $result->status !== 0 )
            {
                $response = new ezcMvcResponse;
                $response->status = $result->status;
            }
            else
            {
                // want the view manager to use my filters
                $view = $this->getView( $routingInformation, $request, $result );

                // create the response
                try
                {
                    $response = $view->createResponse();
                }
                catch ( Exception $e )
                {
                    $request = $this->getFatalRedirectRequest( $request, $result, $e );
                    $continue = true;
                    continue;
                }
            }
            $this->configuration->runResponseFilters( $routingInformation, $request, $result, $response );

            // create the response writer
            $responseWriter = $this->getResponseWriter( $routingInformation, $request, $result, $response );

            // handle the response
            $responseWriter->handleResponse();
        }
        while ( $continue );
    }
}
?>
