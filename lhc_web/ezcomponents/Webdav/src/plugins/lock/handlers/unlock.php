<?php
/**
 * File containing the ezcWebdavLockUnlockRequestResponseHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Handler class for the UNLOCK request.
 *
 * This class provides plugin callbacks for the UNLOCK request for {@link
 * ezcWebdavLockPlugin}.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockUnlockRequestResponseHandler extends ezcWebdavLockRequestResponseHandler
{
    /**
     * Handles responses to the UNLOCk request.
     *
     * Dummy method to satisfy interface. Does not perform any action, since
     * the complete request is handled in {@link receivedRequest()}.
     *
     * @param ezcWebdavResponse $response 
     * @return ezcWebdavResponse|null
     */
    public function generatedResponse( ezcWebdavResponse $response )
    {
        return null;
    }

    /**
     * Handles UNLOCK requests.
     *
     * This method determines the base of the lock determined by the Lock-Token
     * header of $request and releases the lock from all locked resources. In
     * case a lock null resource is beyond these, it will be deleted.
     * 
     * @param ezcWebdavRequest $request ezcWebdavUnlockRequest
     * @return ezcWebdavResponse
     */
    public function receivedRequest( ezcWebdavRequest $request )
    {
        $srv = ezcWebdavServer::getInstance();

        $token = $request->getHeader( 'Lock-Token' );
        $authHeader = $request->getHeader( 'Authorization' );

        if ( $token === null )
        {
            // UNLOCK must have a lock token
            return new ezcWebdavErrorResponse( ezcWebdavResponse::STATUS_412 );
        }

        // Check permission

        if ( !$srv->isAuthorized(
                $request->requestUri,
                $authHeader,
                ezcWebdavAuthorizer::ACCESS_WRITE
             )
             || !$srv->auth->ownsLock( $authHeader->username, $token )
           )
        {
            return $srv->createUnauthorizedResponse(
                $request->requestUri,
                'Authorization failed.'
            );
        }

        // Find properties to determine lock base

        $propFindReq = new ezcWebdavPropFindRequest(
            $request->requestUri
        );
        $propFindReq->prop = new ezcWebdavBasicPropertyStorage();
        $propFindReq->prop->attach(
            new ezcWebdavLockDiscoveryProperty()
        );

        ezcWebdavLockTools::cloneRequestHeaders( $request, $propFindReq );
        $propFindReq->setHeader( 'Depth', ezcWebdavRequest::DEPTH_ZERO );
        $propFindReq->validateHeaders();

        $propFindMultistatusRes = $srv->backend->propFind( $propFindReq );

        if ( !( $propFindMultistatusRes instanceof ezcWebdavMultistatusResponse ) )
        {
            return $propFindMultistatusRes;
        }

        $lockDiscoveryProp = null;

        foreach ( $propFindMultistatusRes->responses as $propFindRes )
        {
            foreach( $propFindRes->responses as $propStatRes )
            {
                if ( $propStatRes->storage->contains( 'lockdiscovery' ) )
                {
                    $lockDiscoveryProp = clone $propStatRes->storage->get( 'lockdiscovery' );
                }
            }
        }

        if ( $lockDiscoveryProp === null )
        {
            // Lock was not found (purged?)! Finish successfully.
            return new ezcWebdavResponse( ezcWebdavResponse::STATUS_204 );
        }

        $affectedActiveLock = null;
        foreach ( $lockDiscoveryProp->activeLock as $id => $activeLock )
        {
            // Note the ==, sinde $activeLock->token is an instance of
            // ezcWebdavPotentialUriContent
            if ( $activeLock->token == $token )
            {
                $affectedActiveLock = $activeLock;
                break;
            }
        }

        if (  $affectedActiveLock === null )
        {
            // Lock not present (purged)! Finish successfully.
            return new ezcWebdavUnlockResponse( ezcWebdavResponse::STATUS_204 );
        }

        if ( $affectedActiveLock->baseUri !== null )
        {
            // Requested resource is not the lock base, recurse
            $newRequest = new ezcWebdavUnlockRequest( $affectedActiveLock->baseUri );
            ezcWebdavLockTools::cloneRequestHeaders( $request, $newRequest, array( 'If', 'Lock-Token' ) );
            $newRequest->validateHeaders();

            // @TODO Should be protected against infinite recursion
            return $this->receivedRequest(
                $newRequest
            );
        }

        // If lock depth is 0, we issue 1 propfind too much here
        // @TODO: Analyse if clients usually lock 0 or infinity
        $res = $this->performUnlock( $request, $token, $affectedActiveLock->depth );

        if ( $res instanceof ezcWebdavUnlockResponse )
        {
            $srv->auth->releaseLock( $authHeader->username, $token );
        }
        return $res;
    }

    /**
     * Performs unlocking.
     *
     * Performs a PROPFIND request with the $depth of the lock with $token on
     * the given $path (which must be the lock base). All affected resources
     * get the neccessary properties updated to reflect the change. Lock null
     * resources in the lock are removed.
     * 
     * @param ezcWebdavUnlockRequest $request
     * @param string $token 
     * @param int $depth 
     * @return ezcWebdavResponse
     */
    protected function performUnlock( ezcWebdavUnlockRequest $request, $token, $depth )
    {
        $path    = $request->requestUri;
        $backend = ezcWebdavServer::getInstance()->backend;

        // Find alle resources affected by the unlock, including affected properties

        $propFindReq = new ezcWebdavPropFindRequest( $path );
        $propFindReq->prop = new ezcWebdavBasicPropertyStorage();
        $propFindReq->prop->attach( new ezcWebdavLockDiscoveryProperty() );

        ezcWebdavLockTools::cloneRequestHeaders( $request, $propFindReq );
        $propFindReq->setHeader( 'Depth', $depth );
        $propFindReq->validateHeaders();

        $propFindMultistatusRes = $backend->propFind( $propFindReq );

        // Remove lock information for the lock identified by $token from each affected resource

        foreach ( $propFindMultistatusRes->responses as $propFindRes )
        {
            // Takes properties to be updated
            $changeProps = new ezcWebdavFlaggedPropertyStorage();

            foreach ( $propFindRes->responses as $propStatRes )
            {
                if ( $propStatRes->status === ezcWebdavResponse::STATUS_200 )
                {
                    // Remove affected active lock part from lockdiscovery property

                    if ( $propStatRes->storage->contains( 'lockdiscovery' ) )
                    {
                        $lockDiscoveryProp = clone $propStatRes->storage->get( 'lockdiscovery' );
                        foreach ( $lockDiscoveryProp->activeLock as $id => $activeLock )
                        {
                            if ( $activeLock->token == $token )
                            {
                                $lockDiscoveryProp->activeLock->offsetUnset( $id );

                                $changeProps->attach(
                                    $lockDiscoveryProp,
                                    ezcWebdavPropPatchRequest::SET
                                );
                                break;
                            }
                        }
                    }
                }
            }

            // Perform the PROPPATCH

            if ( count( $changeProps ) > 0 )
            {
                $propPatchReq = new ezcWebdavPropPatchRequest(
                    $propFindRes->node->path
                );
                $propPatchReq->updates = $changeProps;

                ezcWebdavLockTools::cloneRequestHeaders(
                    $request,
                    $propPatchReq
                );
                $propPatchReq->validateHeaders();

                $propPatchRes = $backend->propPatch( $propPatchReq );

                if ( !( $propPatchRes instanceof ezcWebdavPropPatchResponse ) )
                {
                    throw new ezcWebdavInconsistencyException(
                        "Lock token $token could not be unlocked on resource {$propFindRes->node->path}."
                    );
                }
            }
        }

        return new ezcWebdavUnlockResponse( ezcWebdavResponse::STATUS_204 );
    }
}

?>
