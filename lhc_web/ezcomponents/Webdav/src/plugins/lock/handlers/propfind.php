<?php
/**
 * File containing the ezcWebdavLockPropFindRequestResponseHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Handler class for the PROPFIND request.
 *
 * This class provides plugin callbacks for the PROPFIND request for {@link
 * ezcWebdavLockPlugin}.
 * 
 * @package Webdav
 * @version 1.1.4
 * @todo Refactor code.
 *
 * @access private
 */
class ezcWebdavLockPropFindRequestResponseHandler extends ezcWebdavLockRequestResponseHandler
{
    /**
     * If this handler requires the backend to get locked. 
     *
     * Even if the backend changes while the response is processed, this does
     * not really matter.
     * 
     * @var bool
     */
    public $needsBackendLock = false;

    /**
     * The received request. 
     * 
     * @var ezcWebdavPropFindRequest
     */
    protected $request;

    /**
     * Handles PROPFIND requests.
     *
     * Stores the received $request for later use in {@link
     * generatedResponse()}.
     * 
     * @param ezcWebdavRequest $request ezcWebdavPropFindRequest
     */
    public function receivedRequest( ezcWebdavRequest $request )
    {
        $this->request = $request;
        return null;
    }

    /**
     * Handles responses to the PROPFIND request.
     *
     * Checks if lock related properties were requested in the $request. If
     * this is the case, $responses will be manipulated accordingly: Requested
     * properties which are not handled by the backend are added to the 200
     * status storage and removed from the 404 status storage.
     * 
     * @param ezcWebdavResponse $response ezcWebdavMultistatusResponse
     * @return null
     */
    public function generatedResponse( ezcWebdavResponse $response )
    {
        if ( !( $response instanceof ezcWebdavMultistatusResponse ) )
        {
            return;
        }

        // Cleanup and enhance all PROPSTAT responses

        foreach ( $response->responses as $propFindRes )
        {
            $status200Storage = null;
            $status404Storage = null;

            // Collect property storages
            foreach ( $propFindRes->responses as $propStatResponse )
            {
                if ( $propStatResponse->status === ezcWebdavResponse::STATUS_200 )
                {
                    $status200Storage = $propStatResponse->storage;
                }
                if ( $propStatResponse->status === ezcWebdavResponse::STATUS_404 )
                {
                    $status404Storage = $propStatResponse->storage;
                }
            }

            if ( $status404Storage !== null || $this->request->allProp || $this->request->propName )
            {

                if ( $this->request->allProp || $this->request->propName || $status404Storage->contains( 'lockdiscovery' )  )
                {
                    if ( $status200Storage === null )
                    {
                        $status200Storage = new ezcWebdavBasicPropertyStorage();
                        $responses        = $propFindRes->responses;
                        $responses[]      = new ezcWebdavPropStatResponse(
                            $status200Storage
                        );
                        $propFindRes->responses = $responses;
                    }
                    $status200Storage->attach(
                        new ezcWebdavLockDiscoveryProperty()
                    );
                    if ( $status404Storage !== null )
                    {
                        $status404Storage->detach( 'lockdiscovery' );
                    }
                }

                if ( $this->request->allProp || $this->request->propName || $status404Storage->contains( 'supportedlock' ) )
                {
                    if ( $status200Storage === null )
                    {
                        $status200Storage = new ezcWebdavBasicPropertyStorage();
                        $responses        = $propFindRes->responses;
                        $responses[]      = new ezcWebdavPropStatResponse(
                            $status200Storage
                        );
                        $propFindRes->responses = $responses;
                    }
                    $supportedLock = new ezcWebdavSupportedLockProperty(
                        new ArrayObject(
                            ( $this->request->propName ? array() :
                                array(
                                    new ezcWebdavSupportedLockPropertyLockentry(
                                        ezcWebdavLockRequest::TYPE_WRITE,
                                        ezcWebdavLockRequest::SCOPE_EXCLUSIVE
                                    ),
                                    new ezcWebdavSupportedLockPropertyLockentry(
                                        ezcWebdavLockRequest::TYPE_WRITE,
                                        ezcWebdavLockRequest::SCOPE_SHARED
                                    ),
                                )
                            )
                        )
                    );
                    $status200Storage->attach(
                        $supportedLock
                    );
                    if ( $status404Storage !== null )
                    {
                        $status404Storage->detach( 'supportedlock' );
                    }
                }
            }

            if ( count( $status404Storage ) === 0 )
            {
                $responses = $propFindRes->responses;
                foreach ( $responses as $id => $propStatRes )
                {
                    if ( $propStatRes->status === ezcWebdavResponse::STATUS_404 )
                    {
                        unset( $responses[$id] );
                    }
                }
                $propFindRes->responses = $responses;
            }
        }
    }
}

?>
