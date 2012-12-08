<?php
/**
 * File containing the ezcWebdavLockTransport class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Transport layer extension class of the lock plugin.
 *
 * This class contains methods that extend the transport layer of the Webdav
 * component, by providing methods that parse requests and process responses.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockTransport
{
    /**
     * Map for request parsers.
     *
     * Maps request method names as provided by $_SERVER to methods of this
     * class.
     *
     * @var array(string=>string)
     */
    protected static $parsingMap = array(
        'LOCK'   => 'parseLockRequest',
        'UNLOCK' => 'parseUnlockRequest',
    );

    /**
     * Map for response handling.
     *
     * Maps response classes to a method that handles objects of this class.
     *
     * @var array(string=>string)
     */
    protected static $processingMap = array(
        'ezcWebdavLockResponse'   => 'processLockResponse',
        'ezcWebdavUnlockResponse' => 'processUnlockResponse',
    );


    /**
     * Property handler. 
     * 
     * @var ezcWebdavLockPropertyHandler
     */
    protected $propertyHandler;

    /**
     * Header handler. 
     * 
     * @var ezcWebdavLockHeaderHandler
     */
    protected $headerHandler;

    /**
     * Creates a new lock transport.
     *
     * @param ezcWebdavLockHeaderHandler $headerHandler
     * @param ezcWebdavLockPropertyHandler $propertyHandler
     */
    public function __construct( $headerHandler, $propertyHandler )
    {
        $this->propertyHandler = $propertyHandler;
        $this->headerHandler   = $headerHandler;
    }

    /**
     * Callback for the hook ezcWebdavTransport::parseUnknownRequest().
     *
     * Reacts on the LOCK and UNLOCK request methods.
     * 
     * @param string $method
     * @param string $path
     * @param string $body
     * @return ezcWebdavRequest
     */
    public function parseRequest( $method, $path, $body )
    {
        if ( isset( self::$parsingMap[$method] ) )
        {
            $req = call_user_func(
                array( $this, self::$parsingMap[$method] ),
                $path,
                $body
            );
            $req->validateHeaders();
            return $req;
        }
        return null;
    }

    /**
     * Handles responses of the LOCK plugin.
     * 
     * @param ezcWebdavResponse $response 
     * @return ezcWebdavDisplayInformation
     */
    public function processResponse( ezcWebdavResponse $response )
    {
        if( isset( self::$processingMap[( $responseClass = get_class( $response ) )] ) )
        {
            $method = self::$processingMap[$responseClass];
            return $this->$method( $response );
        }
        return null;
    }

    /**
     * Parses the LOCK request and returns a request object.
     *
     * This method is responsible for parsing the LOCK request. It retrieves
     * the current request URI in $path and the request body as $body.  The
     * return value, if no exception is thrown, is a valid {@link
     * ezcWebdavLockRequest} object.
     *
     * This method may be overwritten to adjust it to special client behaviour.
     * 
     * @param string $path 
     * @param string $body 
     * @return ezcWebdavLockRequest
     */
    protected function parseLockRequest( $path, $body )
    {
        $request = new ezcWebdavLockRequest( $path );

        $request->setHeaders(
            ezcWebdavServer::getInstance()->headerHandler->parseHeaders(
                array( 'Depth' )
            )
        );

        if ( trim( $body ) === '' )
        {
            return $request;
        }

        try
        {
            $dom = ezcWebdavServer::getInstance()->xmlTool->createDom( $body );
        }
        catch ( ezcWebdavInvalidXmlException $e )
        {
            throw new ezcWebdavInvalidRequestBodyException(
                'LOCK',
                $e->getMessage()
            );
        }
        
        if ( $dom->documentElement->localName !== 'lockinfo' )
        {
            throw new ezcWebdavInvalidRequestBodyException(
                'LOCK',
                "Expected XML element <lockinfo />, received <{$dom->documentElement->localName} />."
            );
        }

        $lockTypeElements  = $dom->documentElement->getElementsByTagnameNS(
            ezcWebdavXmlTool::XML_DEFAULT_NAMESPACE,
            'locktype'
        );
        $lockScopeElements = $dom->documentElement->getElementsByTagnameNS(
            ezcWebdavXmlTool::XML_DEFAULT_NAMESPACE,
            'lockscope'
        );
        $ownerElements = $dom->documentElement->getElementsByTagnameNS(
            ezcWebdavXmlTool::XML_DEFAULT_NAMESPACE,
            'owner'
        );

        if ( $lockTypeElements->length === 0 )
        {
            throw new ezcWebdavInvalidRequestBodyException(
                'LOCK',
                "Expected XML element <locktype /> as child of <lockinfo /> in namespace DAV: which was not found."
            );
        }
        if ( $lockScopeElements->length === 0 )
        {
            throw new ezcWebdavInvalidRequestBodyException(
                'LOCK',
                "Expected XML element <lockscope /> as child of <lockinfo /> in namespace DAV: which was not found."
            );
        }

        // @todo is the following not restrictive enough?
        $request->lockInfo = new ezcWebdavRequestLockInfoContent(
            ( $lockScopeElements->item( 0 )->firstChild->localName === 'exclusive'
                ? ezcWebdavLockRequest::SCOPE_EXCLUSIVE
                : ezcWebdavLockRequest::SCOPE_SHARED ),
            ( $lockTypeElements->item( 0 )->firstChild->localName === 'read'
                ? ezcWebdavLockRequest::TYPE_READ
                : ezcWebdavLockRequest::TYPE_WRITE ),
            ( $ownerElements->length > 0 
                ? new ezcWebdavPotentialUriContent(
                    $ownerElements->item( 0 )->textContent,
                    ( $ownerElements->item( 0 )->hasChildNodes() && $ownerElements->item( 0 )->firstChild->localName === 'href' )
                  )
                : new ezcWebdavPotentialUriContent() )
        );

        $request->setHeader( 'Timeout', $this->headerHandler->parseTimeoutHeader() );

        return $request;
    }
    
    /**
     * Parses the UNLOCK request and returns a request object.
     *
     * This method is responsible for parsing the UNLOCK request. It retrieves
     * the current request URI in $path and the request body as $body.  The
     * return value, if no exception is thrown, is a valid {@link
     * ezcWebdavUnlockRequest} object.
     *
     * This method may be overwritten to adjust it to special client behaviour.
     * 
     * @param string $path 
     * @param string $body 
     * @return ezcWebdavUnlockRequest
     */
    protected function parseUnlockRequest( $path, $body )
    {
        $request = new ezcWebdavUnlockRequest( $path );
        
        $request->setHeaders(
            ezcWebdavServer::getInstance()->headerHandler->parseHeaders()
        );
        $request->setHeader(
            'Lock-Token',
            $this->headerHandler->parseLockTokenHeader()
        );

        return $request;
    }

    /**
     * Processes a lock response into a korresponding display information struct.
     *
     * The struct ist the processed by {@link
     * ezcWebdavTransport::flattenResponse()} and send by {@link
     * ezcWebdavTransport::sendResponse()}.
     * 
     * @param ezcWebdavLockResponse $response 
     * @return ezcWebdavXmlDisplayInformation
     */
    protected function processLockResponse( ezcWebdavLockResponse $response )
    {
        $xmlTool = ezcWebdavServer::getInstance()->xmlTool;
        $dom     = $xmlTool->createDom();

        $propElement = $dom->appendChild(
            $xmlTool->createDomElement( $dom, 'prop' )
        );

        $this->propertyHandler->serializeLiveProperty(
            $response->lockDiscovery,
            $propElement,
            $xmlTool
        );

        return new ezcWebdavXmlDisplayInformation(
            $response,
            $dom
        );
    }

    /**
     * Processes a unlock response into a korresponding display information struct.
     *
     * The struct ist the processed by {@link
     * ezcWebdavTransport::flattenResponse()} and send by {@link
     * ezcWebdavTransport::sendResponse()}.
     * 
     * @param ezcWebdavUnlockResponse $response 
     * @return ezcWebdavEmptyDisplayInformation
     */
    protected function processUnlockResponse( ezcWebdavUnlockResponse $response )
    {
        return new ezcWebdavEmptyDisplayInformation( $response );
    }
}

?>
