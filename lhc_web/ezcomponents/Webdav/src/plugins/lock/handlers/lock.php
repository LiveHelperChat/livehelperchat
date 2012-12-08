<?php
/**
 * File containing the ezcWebdavLockLockRequestResponseHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Handler class for the LOCK request.
 *
 * This class provides plugin callbacks for the LOCK request for {@link
 * ezcWebdavLockPlugin}.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockLockRequestResponseHandler extends ezcWebdavLockRequestResponseHandler
{
    /**
     * Handled request object.
     * 
     * @var ezcWebdavLockRequest
     */
    protected $request;

    /**
     * Handles responses to the LOCK request.
     *
     * Dummy method to satisfy interface. Does not perform any action, since
     * the complete request is handled in {@link receivedRequest()}.
     *
     * @param ezcWebdavResponse $response ezcWebdavLockResponse
     * @return null
     */
    public function generatedResponse( ezcWebdavResponse $response )
    {
        return null;
    }

    /**
     * Handles LOCK requests (completely).
     *
     * Performs the LOCK request. First checks if any pre-conditions for the
     * lock to acquire failed. In case a violation occurs, returns the
     * corresponding {@link ezcWebdavErrorResponse}. If now errors occur, the
     * lock is acquired using PROPPATCH requests to the backend and an {@link
     * ezcWebdavLockResponse} is returned.
     *
     * @param ezcWebdavRequest $request ezcWebdavLockRequest
     * @return ezcWebdavResponse
     */
    public function receivedRequest( ezcWebdavRequest $request )
    {
        // Authentication has already taken place here.

        $this->request = $request;
        
        // New lock
        if ( $request->lockInfo !== null )
        {
            return $this->acquireLock( $request );
        }
        // Lock refresh
        else
        {
            return $this->refreshLock( $request );
        }
    }

    /**
     * Aquires a new lock.
     *
     * Performs all necessary checks for the lock to be acquired by $request.
     * If any failures occur, either an instance of {@link
     * ezcWebdavErrorResponse} or {@link ezcWebdavMultistatusResponse} is
     * returned. If the lock was acquired successfully, an instance of {@link
     * ezcWebdavLockResponse} is returned.
     * 
     * @param ezcWebdavLockRequest $request 
     * @return ezcWebdavResponse
     */
    protected function acquireLock( ezcWebdavLockRequest $request )
    {
        $auth = ezcWebdavServer::getInstance()->auth;

        $authHeader = $request->getHeader( 'Authorization' );

        // Active lock part to be used in PROPPATCH requests and LOCK response
        $lockToken  = $this->tools->generateLockToken( $request );
        $activeLock = $this->tools->generateActiveLock(
            $request,
            $lockToken
        );

        // Generates PROPPATCH requests while checking violations
        $requestGenerator = new ezcWebdavLockLockRequestGenerator(
            $request,
            $activeLock
        );

        // Check violations and collect PROPPATCH requests
        $res = $this->tools->checkViolations(
            new ezcWebdavLockCheckInfo(
                $request->requestUri,
                $request->getHeader( 'Depth' ),
                $request->getHeader( 'If' ),
                $authHeader,
                ezcWebdavAuthorizer::ACCESS_WRITE,
                $requestGenerator,
                ( $request->lockInfo->lockScope === ezcWebdavLockRequest::SCOPE_SHARED )
            )
        );

        if ( $res !== null )
        {
            if ( $res->status === ezcWebdavResponse::STATUS_404 )
            {
                return $this->createLockNullResource( $request );
            }

            // Other violations -> return multistatus
            return $this->createLockError( $res );
        }

        // Assign lock to user
        $auth->assignLock( $authHeader->username, $lockToken );
        
        $affectedLockDiscovery = null;

        // Send all generated PROPPATCH requests to the backend to update lock information
        foreach ( $requestGenerator->getRequests() as $propPatch )
        {
            // Authorization for lock assignement
            ezcWebdavLockTools::cloneRequestHeaders( $request, $propPatch );
            $propPatch->validateHeaders();

            $res = ezcWebdavServer::getInstance()->backend->performRequest(
                $propPatch
            );

            if ( !( $res instanceof ezcWebdavPropPatchResponse  ) )
            {
                // An error occured while performing PROPPATCH, very bad thing!
                // @TODO: Should usually cleanup successful patches again!
                return $res;
            }
        }

        return new ezcWebdavLockResponse(
            $requestGenerator->getLockDiscoveryProperty( $request->requestUri ),
            ezcWebdavResponse::STATUS_200,
            $lockToken
        );
    }

    /**
     * Performs a manual request for a lock.
     *
     * Clients may send a lock request without a body and with an If header, to
     * indicate they want to reset the timeout for a lock. This method handles
     * such requests.
     * 
     * @param ezcWebdavLockRequest $request 
     * @return ezcWebdavResponse
     */
    protected function refreshLock( ezcWebdavLockRequest $request )
    {
        if ( ( $ifHeader = $request->getHeader( 'If' ) ) === null )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_412,
                'If header needs to be provided to refresh a lock.'
            );
        }
        
        $reqGen = new ezcWebdavLockRefreshRequestGenerator(
            $request
        );

        $violation = $this->tools->checkViolations(
            new ezcWebdavLockCheckInfo(
                $request->requestUri,
                $request->getHeader( 'Depth' ),
                $request->getHeader( 'If' ),
                $request->getHeader( 'Authorization' ),
                ezcWebdavAuthorizer::ACCESS_WRITE,
                $reqGen
            )
        );
        
        if ( $violation !== null )
        {
            return $this->createLockError( $violation );
        }

        $reqGen->sendRequests();

        return new ezcWebdavLockResponse(
            $reqGen->getLockDiscoveryProperty( $request->requestUri )
        );
    }

    /**
     * Creates a lock-null resource.
     *
     * In case a LOCK request is issued on a resource, that does not exists, a
     * so-called lock-null resource is created. This resource must support some
     * of the WebDAV requests, but not all. In case an MKCOL or PUT request is
     * issued to such a resource, it is switched to be a real resource. In case
     * the lock is released, all null-lock resources in it are removed.
     * 
     * @param ezcWebdavLockRequest $request 
     * @return ezcWebdavResponse
     */
    protected function createLockNullResource( ezcWebdavLockRequest $request )
    {
        $backend = ezcWebdavServer::getInstance()->backend;

        // Check parent directory for locks and other violations

        $violation = $this->tools->checkViolations(
            new ezcWebdavLockCheckInfo(
                dirname( $request->requestUri ),
                ezcWebdavRequest::DEPTH_ZERO,
                $request->getHeader( 'If' ),
                $request->getHeader( 'Authorization' ),
                ezcWebdavAuthorizer::ACCESS_WRITE
            )
        );

        if ( $violation !== null )
        {
            return $this->createLockError( $violation );
        }

        // Create lock null resource

        $putReq = new ezcWebdavPutRequest(
            $request->requestUri,
            ''
        );
        ezcWebdavLockTools::cloneRequestHeaders( $request, $putReq, array( 'If' ) );
        $putReq->setHeader( 'Content-Length', '0' );
        $putReq->validateHeaders();

        $putRes = $backend->put( $putReq );

        if ( !( $putRes instanceof ezcWebdavPutResponse ) )
        {
            return $this->createLockError( $putRes );
        }

        // Attention, recursion!
        $res = $this->acquireLock( $request );

        if ( $res->status !== ezcWebdavResponse::STATUS_200 )
        {
            return $res;
        }

        $res->status = ezcWebdavResponse::STATUS_201;
        return $res;
    }

    /**
     * Creates an error response for the LOCK method.
     * 
     * @param ezcWebdavErrorResponse $response 
     * @return ezcWebdavMultistatusResponse
     */
    protected function createLockError( ezcWebdavErrorResponse $response )
    {
        // RFC 4918 does no more require 207 here
        if ( $response->status === ezcWebdavResponse::STATUS_423 )
        {
            return $response;
        }

        return new ezcWebdavMultistatusResponse(
            $response,
            new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_424,
                $this->request->requestUri
            )
        );
    }
}

?>
