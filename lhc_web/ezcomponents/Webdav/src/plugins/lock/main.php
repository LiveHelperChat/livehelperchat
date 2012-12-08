<?php
/**
 * File containing the ezcWebdavLockPlugin class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Main class of the lock plugin.
 *
 * This class is responsible to dispatch all actions of the lock plugin and to
 * instantiate all necessary objects.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @property ezcWebdavLockTransport $transport
 *           The transport class to parse the LOCK and UNLOCK requests and to
 *           process the corresponding responses.
 * @property ezcWebdavLockPropertyHandler $propertyHandler
 *           Property handler to handle parsing and serializing of lock related
 *           properties.
 * @property ezcWebdavLockHeaderHandler $headerHandler
 *           Header handler to parse lock related headers.
 *
 * @access private
 */
class ezcWebdavLockPlugin
{
    /**
     * Namespace of the LOCK plugin. 
     */
    const PLUGIN_NAMESPACE = 'ezcWebdavLockPlugin';

    /**
     * XML namespace for properties.
     */
    const XML_NAMESPACE = 'http://ezcomponents.org/s/Webdav#lock';

    /**
     * Properties.
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'transport'       => null,
        'propertyHandler' => null,
        'headerHandler'   => null,
    );

    /**
     * Maps request classes to handling methods.
     *
     * @var array(string=>string)
     */
    protected static $requestHandlingMap = array(
        'ezcWebdavLockRequest'           => 'ezcWebdavLockLockRequestResponseHandler',
        'ezcWebdavUnlockRequest'         => 'ezcWebdavLockUnlockRequestResponseHandler',
        'ezcWebdavCopyRequest'           => 'ezcWebdavLockCopyRequestResponseHandler',
        'ezcWebdavDeleteRequest'         => 'ezcWebdavLockDeleteRequestResponseHandler',
        'ezcWebdavMoveRequest'           => 'ezcWebdavLockMoveRequestResponseHandler',
        'ezcWebdavMakeCollectionRequest' => 'ezcWebdavLockMakeCollectionRequestResponseHandler',
        'ezcWebdavOptionsRequest'        => 'ezcWebdavLockOptionsRequestResponseHandler',
        'ezcWebdavPropFindRequest'       => 'ezcWebdavLockPropFindRequestResponseHandler',
        'ezcWebdavPropPatchRequest'      => 'ezcWebdavLockPropPatchRequestResponseHandler',
        'ezcWebdavPutRequest'            => 'ezcWebdavLockPutRequestResponseHandler',
    );

    /**
     * Lock plugin options. 
     * 
     * @var ezcWebdavLockPluginOptions
     */
    protected $options;

    /**
     * Lock transport. 
     * 
     * @var ezcWebdavLockTransport
     */
    protected $transport;

    /**
     * Lock property handler. 
     * 
     * @var ezcWebdavLockPropertyHandler
     */
    protected $propertyHandler;

    /**
     * Lock header handler. 
     * 
     * @var ezcWebdavLockHeaderHandler
     */
    protected $headerHandler;

    /**
     * Request / response handler.
     *
     * @var ezcWebdavLockRequestResponseHandler
     */
    protected $handler;

    /**
     * Creates the objects needed for dispatching the hooks.
     *
     * Can optionally receive $options to influence the behavior of the lock
     * plugin.
     * 
     * @param ezcWebdavLockPluginOptions $options
     */
    public function __construct( ezcWebdavLockPluginOptions $options )
    {
        $this->options         = $options;
        $this->headerHandler   = new ezcWebdavLockHeaderHandler();
        $this->propertyHandler = new ezcWebdavLockPropertyHandler();
        $this->transport       = new ezcWebdavLockTransport(
            $this->headerHandler,
            $this->propertyHandler
        );
    }

    /**
     * Callback for the hook ezcWebdavTransport::parseUnknownRequest().
     *
     * This method is attached to the specified hook through {@link
     * ezcWebdavLockPluginConfiguration}.
     *
     * Parameters are:
     * - string path
     * - string body
     * - string requestUri
     *
     * Reacts on the LOCK and UNLOCK request methods.
     * 
     * @param ezcWebdavPluginParameters $params 
     * @return ezcWebdavRequest|null
     */
    public function parseUnknownRequest( ezcWebdavPluginParameters $params )
    {
        return $this->transport->parseRequest(
            $params['requestMethod'],
            $params['path'],
            $params['body']
        );
    }

    /**
     * Callback for the hook ezcWebdavTransport::handleUnknownResponse().
     *
     * Parameters are:
     * - ezcWebdavResponse response
     * 
     * @param ezcWebdavPluginParameters $params 
     * @return ezcWebdavDisplayInformation
     */
    public function processUnknownResponse( ezcWebdavPluginParameters $params )
    {
        return $this->transport->processResponse( $params['response'] );
    }

    /**
     * Callback for the hook ezcWebdavPropertyHandler::extractUnknownLiveProperty().
     *
     * Parameters are:
     * - DOMElement domElement
     * - ezcWebdavXmlTool xmlTool
     * 
     * @param ezcWebdavPluginParameters $params 
     * @return void
     */
    public function extractUnknownLiveProperty( ezcWebdavPluginParameters $params )
    {
        return $this->propertyHandler->extractLiveProperty(
            $params['domElement'],
            $params['xmlTool']
        );
    }

    /**
     * Callback for the hook ezcWebdavPropertyHandler::serializeUnknownLiveProperty().;
     *
     * Parameters are:
     * - ezcWebdavLiveProperty property
     * - ezcWebdavTransport xmlTool
     * - DOMElement parentElement
     * 
     * @param ezcWebdavPluginParameters $params 
     * @return void
     */
    public function serializeUnknownLiveProperty( ezcWebdavPluginParameters $params )
    {
        return $this->propertyHandler->serializeLiveProperty(
            $params['property'],
            $params['parentElement'],
            $params['xmlTool']
        );
    }

    /**
     * Callback for the hook ezcWebdavPropertyHandler::extractDeadProperty().
     *
     * Parameters are:
     * - DOMElement domElement
     * - ezcWebdavXmlTool xmlTool
     * 
     * @param ezcWebdavPluginParameters $params 
     * @return ezcWebdavDeadProperty|null
     */
    public function extractDeadProperty( ezcWebdavPluginParameters $params )
    {
        // Check namespace before bothering property handler
        if ( $params['domElement']->namespaceURI !== ezcWebdavLockPlugin::XML_NAMESPACE )
        {
            return;
        }

        return $this->propertyHandler->extractDeadProperty(
            $params['domElement'],
            $params['xmlTool']
        );
    }

    /**
     * Callback for the hook ezcWebdavPropertyHandler::serializeDeadProperty().
     *
     * Parameters are:
     * - ezcWebdavDeadProperty property
     * - ezcWebdavXmlTool xmlTool
     * 
     * @param ezcWebdavPluginParameters $params 
     * @return DOMElement|null
     */
    public function serializeDeadProperty( ezcWebdavPluginParameters $params )
    {
        return $this->propertyHandler->serializeDeadProperty(
            $params['property'],
            $params['xmlTool']
        );
    }

    /**
     * Callback for the hook ezcWebdavServer::receivedRequest().
     *
     * Parameters are:
     * - ezcWebdavRequest request
     *
     * Needs to react directly on:
     * - ezcWebdavLockRequest
     * - ezcWebdavUnlockRequest
     *
     * Needs to check if lock violations occur on:
     * - ezcWebdavCopyRequest
     * - ezcWebdavMoveRequest
     * - ezcWebdavMakeCollectionRequest
     * - ezcWebdavPropPatchRequest
     * - ezcWebdavPutRequest
     * 
     * @param ezcWebdavPluginParameters $params 
     * @return ezcWebdavResponse|null
     */
    public function receivedRequest( ezcWebdavPluginParameters $params )
    {
        $request  = $params['request'];

        $requestClass = get_class( $request );
        if ( isset( ezcWebdavLockPlugin::$requestHandlingMap[$requestClass] ) )
        {
            // Set headers parsed by the lock plugin only.
            $request->setHeader(
                'If',
                $this->headerHandler->parseIfHeader( $request )
            );
            $request->setHeader(
                'Timeout',
                $this->headerHandler->parseTimeoutHeader( $request )
            );
            $request->setHeader(
                'Lock-Token',
                $this->headerHandler->parseLockTokenHeader( $request )
            );
            $request->validateHeaders();

            $handlerClass = ezcWebdavLockPlugin::$requestHandlingMap[$requestClass];
            $this->handler = new $handlerClass(
                new ezcWebdavLockTools( $this->options )
            );

            if ( $this->handler->needsBackendLock )
            {
                ezcWebdavServer::getInstance()->backend->lock(
                    $this->options->backendLockWaitTime,
                    $this->options->backendLockTimeout
                );
            }

            $res = null;
            try
            {
                $res = $this->handler->receivedRequest( $request );
            }
            catch ( Exception $e )
            {
                if ( $this->handler->needsBackendLock )
                {
                    ezcWebdavServer::getInstance()->backend->unlock();
                }
                throw $e;
            }

            return $res;
        }
        // return null
    }

    /**
     * Handles responses generated by the backend.
     * 
     * @param ezcWebdavPluginParameters $params 
     * @return ezcWebdavResponse|null
     */
    public function generatedResponse( ezcWebdavPluginParameters $params )
    {
        if ( isset( $this->handler ) )
        {
            $res = null;

            try
            {
                $res = $this->handler->generatedResponse( $params['response'] );
            }
            catch ( Exception $e )
            {
                if ( $this->handler->needsBackendLock )
                {
                    ezcWebdavServer::getInstance()->backend->unlock();
                }
                throw $e;
            }

            if ( $this->handler->needsBackendLock )
            {
                ezcWebdavServer::getInstance()->backend->unlock();
            }
            return $res;
        }
    }

    //
    //
    // Property access
    //
    //

    /**
     * Sets a property.
     *
     * This method is called when an property is to be set.
     * 
     * @param string $propertyName The name of the property to set.
     * @param mixed $propertyValue The property value.
     * @return void
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'transport':
                if ( !( $propertyValue instanceof ezcWebdavLockTransport ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavLockTransport' );
                }
                break;
            case 'propertyHandler':
                if ( !( $propertyValue instanceof ezcWebdavLockPropertyHandler ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavLockPropertyHandler' );
                }
                break;
            case 'headerHandler':
                if ( !( $propertyValue instanceof ezcWebdavLockHeaderHandler ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavLockPropertyHandler' );
                }
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property get access.
     *
     * Simply returns a given property.
     *
     * @param string $propertyName The name of the property to get.
     * @return mixed The property value.
     *
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a write-only property.
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }
    
    /**
     * Returns if a property exists.
     *
     * Returns true if the property exists in the {@link $properties} array
     * (even if it is null) and false otherwise. 
     *
     * @param string $propertyName Option name to check for.
     * @return void
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }
}

?>
