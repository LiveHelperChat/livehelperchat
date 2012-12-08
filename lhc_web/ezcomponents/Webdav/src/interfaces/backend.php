<?php
/**
 * File containing the ezcWebdavBackend class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Base class to be extended by all backend implementation.
 *
 * The backend is meant to be extended by an implementation for your data
 * storage. It enforces the base features required for each backend and should
 * be extended by further interfaces for other access methods, like:
 *
 * <ul>
 *     <li>{@link ezcWebdavBackendPut}</li>
 *     <li>{@link ezcWebdavBackendChange}</li>
 *     <li>{@link ezcWebdavBackendMakeCollection}</li>
 *     <li>{@link ezcWebdavBackendLock}</li>
 * </ul>
 *
 * @version 1.1.4
 * @package Webdav
 */
abstract class ezcWebdavBackend
{
    /**
     * Backend has native support for gzip compression.
     */
    const COMPRESSION_GZIP      = 1;

    /**
     * Backend has native support for bzip2 compression.
     */
    const COMPRESSION_BZIP2     = 2;

    /**
     * Backend performs locking itself - no handling by server is required.
     */
    const CUSTOM_LOCK           = 4;

    /**
     * Backend has native support for partial requests.
     */
    const PARTIAL               = 8;

    /**
     * Backend has native support for multipart requests.
     */
    const MULTIPART             = 16;

    /**
     * Returns additional features supported by the backend.
     *
     * Returns a bitmap of additional features supported by the backend, referenced
     * by constants from the basic {@link ezcWebdavBackend} class.
     * 
     * @return int
     */
    public function getFeatures()
    {
        return 0;
    }

    /**
     * Performs the given request.
     *
     * This method takes an instance of {@link ezcWebdavRequest} in $request
     * and dispatches it locally to the correct handling method. A
     * corresponding {@link ezcWebdavResponse} object will be returned. If the
     * given request could not be dispatched, because the backend does not
     * implement the neccessary interface or the request type is unknown, a
     * {@link ezcWebdavRequestNotSupportedException} is thrown.
     * 
     * @param ezcWebdavRequest $request 
     * @return ezcWebdavResponse
     * @throws ezcWebdavRequestNotSupportedException
     *         if the given request object could not be handled by the backend.
     */
    public function performRequest( ezcWebdavRequest $request )
    {
        switch ( true )
        {
            case ( $request instanceof ezcWebdavGetRequest ):
                return $this->get( $request );
            case ( $request instanceof ezcWebdavHeadRequest ):
                return $this->head( $request );
            case ( $request instanceof ezcWebdavPropFindRequest ):
                return $this->propFind( $request );
            case ( $request instanceof ezcWebdavPropPatchRequest ):
                return $this->propPatch( $request );
            case ( $request instanceof ezcWebdavOptionsRequest ):
                return $this->options( $request );
            case ( $request instanceof ezcWebdavDeleteRequest ):
                if ( $this instanceof ezcWebdavBackendChange )
                {
                    return $this->delete( $request );
                }
                else
                {
                    throw new ezcWebdavRequestNotSupportedException(
                        $request,
                        'Backend does not implement ezcWebdavBackendChange.'
                    );
                }
                break;
            case ( $request instanceof ezcWebdavCopyRequest ):
                if ( $this instanceof ezcWebdavBackendChange )
                {
                    return $this->copy( $request );
                }
                else
                {
                    throw new ezcWebdavRequestNotSupportedException(
                        $request,
                        'Backend does not implement ezcWebdavBackendChange.'
                    );
                }
                break;
            case ( $request instanceof ezcWebdavMoveRequest ):
                if ( $this instanceof ezcWebdavBackendChange )
                {
                    return $this->move( $request );
                }
                else
                {
                    throw new ezcWebdavRequestNotSupportedException(
                        $request,
                        'Backend does not implement ezcWebdavBackendChange.'
                    );
                }
                break;
            case ( $request instanceof ezcWebdavMakeCollectionRequest ):
                if ( $this instanceof ezcWebdavBackendMakeCollection )
                {
                    return $this->makeCollection( $request );
                }
                else
                {
                    throw new ezcWebdavRequestNotSupportedException(
                        $request,
                        'Backend does not implement ezcWebdavBackendMakeCollection.'
                    );
                }
                break;
            case ( $request instanceof ezcWebdavPutRequest ):
                if ( $this instanceof ezcWebdavBackendPut )
                {
                    return $this->put( $request );
                }
                else
                {
                    throw new ezcWebdavRequestNotSupportedException(
                        $request,
                        'Backend does not implement ezcWebdavBackendPut.'
                    );
                }
                break;
            default:
                throw new ezcWebdavRequestNotSupportedException(
                    $request,
                    'Backend could not dispatch request object.'
                );
        }
    }

    /**
     * Serves GET requests.
     *
     * The method receives a {@link ezcWebdavGetRequest} object containing all
     * relevant information obout the clients request and will return an {@link
     * ezcWebdavErrorResponse} instance on error or an instance of {@link
     * ezcWebdavGetResourceResponse} or {@link ezcWebdavGetCollectionResponse}
     * on success, depending on the type of resource that is referenced by the
     * request.
     *
     * @param ezcWebdavGetRequest $request
     * @return ezcWebdavResponse
     */
    abstract public function get( ezcWebdavGetRequest $request );

    /**
     * Serves HEAD requests.
     *
     * The method receives a {@link ezcWebdavHeadRequest} object containing all
     * relevant information obout the clients request and will return an {@link
     * ezcWebdavErrorResponse} instance on error or an instance of {@link
     * ezcWebdavHeadResponse} on success.
     * 
     * @param ezcWebdavHeadRequest $request
     * @return ezcWebdavResponse
     */
    abstract public function head( ezcWebdavHeadRequest $request );

    /**
     * Serves PROPFIND requests.
     * 
     * The method receives a {@link ezcWebdavPropFindRequest} object containing
     * all relevant information obout the clients request and will either
     * return an instance of {@link ezcWebdavErrorResponse} to indicate an error
     * or a {@link ezcWebdavPropFindResponse} on success. If the referenced
     * resource is a collection or if some properties produced errors, an
     * instance of {@link ezcWebdavMultistatusResponse} may be returned.
     *
     * The {@link ezcWebdavPropFindRequest} object contains a definition to
     * find one or more properties of a given collection or non-collection
     * resource.
     *
     * @param ezcWebdavPropFindRequest $request
     * @return ezcWebdavResponse
     */
    abstract public function propFind( ezcWebdavPropFindRequest $request );

    /**
     * Serves PROPPATCH requests.
     * 
     * The method receives a {@link ezcWebdavPropPatchRequest} object
     * containing all relevant information obout the clients request and will
     * return an instance of {@link ezcWebdavErrorResponse} on error or a
     * {@link ezcWebdavPropPatchResponse} response on success. If the
     * referenced resource is a collection or if only some properties produced
     * errors, an instance of {@link ezcWebdavMultistatusResponse} may be
     * returned.
     *
     * @param ezcWebdavPropPatchRequest $request
     * @return ezcWebdavResponse
     */
    abstract public function propPatch( ezcWebdavPropPatchRequest $request );

    /**
     * Required method to serve OPTIONS requests.
     * 
     * The method receives a {@link ezcWebdavOptionsRequest} object containing all
     * relevant information obout the clients request and should either return
     * an error by returning an {@link ezcWebdavErrorResponse} object, or any
     * other {@link ezcWebdavResponse} objects.
     *
     * @param ezcWebdavOptionsRequest $request
     * @return ezcWebdavResponse
     */
    public function options( ezcWebdavOptionsRequest $request )
    {
        $response = new ezcWebdavOptionsResponse( '1' );

        // Always allowed
        $allowed = 'GET, HEAD, PROPFIND, PROPPATCH, OPTIONS, ';

        // Check if modifications are allowed
        if ( $this instanceof ezcWebdavBackendChange )
        {
            $allowed .= 'DELETE, COPY, MOVE, ';
        }

        // Check if MKCOL is allowed
        if ( $this instanceof ezcWebdavBackendMakeCollection )
        {
            $allowed .= 'MKCOL, ';
        }

        // Check if PUT is allowed
        if ( $this instanceof ezcWebdavBackendPut )
        {
            $allowed .= 'PUT, ';
        }

        $response->setHeader( 'Allow', substr( $allowed, 0, -2 ) );

        return $response;
    }
}

?>
