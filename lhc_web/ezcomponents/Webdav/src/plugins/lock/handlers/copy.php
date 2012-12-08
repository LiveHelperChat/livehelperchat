<?php
/**
 * File containing the ezcWebdavLockCopyRequestResponseHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Handler class for the COPY request.
 *
 * This class provides plugin callbacks for the COPY request for {@link
 * ezcWebdavLockPlugin}.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockCopyRequestResponseHandler extends ezcWebdavLockRequestResponseHandler
{
    /**
     * Properties of the destination parent.
     *
     * These properties need to be set on the successfully moved the source to
     * the destination. The properties still need to be manipulated in {@link
     * generatedResponse()}
     * 
     * @var ezcWebdavBasicPropertyStorage
     */
    protected $lockProperties;

    /**
     * The original request.
     * 
     * @var ezcWebdavCopyRequest
     */
    protected $request;

    /**
     * Pathes moved to the destination.
     *
     * Used to determine all paths that need lock updates.
     * 
     * @var array(string)
     */
    protected $sourcePaths;

    /**
     * Handles COPY requests.
     *
     * Performs all lock related checks necessary for the COPY request. In case
     * a violation with locks is detected or any other pre-condition check
     * fails, this method returns an instance of {@link ezcWebdavResponse}. If
     * everything is correct, null is returned, so that the $request is handled
     * by the backend.
     *
     * @param ezcWebdavRequest $request ezcWebdavCopyRequest
     * @return ezcWebdavResponse|null
     */
    public function receivedRequest( ezcWebdavRequest $request )
    {
        $backend = ezcWebdavServer::getInstance()->backend;

        $this->request = $request;

        $destination = $request->getHeader( 'Destination' );
        $destParent  = dirname( $destination );
        $ifHeader    = $request->getHeader( 'If' );
        $authHeader  = $request->getHeader( 'Authorization' );

        // Check destination parent and collect the lock properties to
        // set after successfully moving

        $destinationLockRefresher = new ezcWebdavLockRefreshRequestGenerator(
            $request
        );

        $violation = $this->tools->checkViolations(
            // Destination parent dir
            // We also get the lock property from here and refresh the
            // locks on it
            new ezcWebdavLockCheckInfo(
                $destParent,
                ezcWebdavRequest::DEPTH_ZERO,
                $ifHeader,
                $authHeader,
                ezcWebdavAuthorizer::ACCESS_WRITE,
                $destinationLockRefresher
            ),
            // Return on first violation
            true
        );


        if ( $violation !== null )
        {
            if ( $violation->status === ezcWebdavResponse::STATUS_404 )
            {
                // Destination parent not found
                return new ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_409,
                    $violation->requestUri
                );
            }
            return $violation;
        }

        // Check destination itself, if it exsists

        $violation = $this->tools->checkViolations(
            // Destination (maybe overwritten, maybe not, but we must not
            // care)
            new ezcWebdavLockCheckInfo(
                $destination,
                ezcWebdavRequest::DEPTH_INFINITY,
                $ifHeader,
                $authHeader,
                ezcWebdavAuthorizer::ACCESS_WRITE,
                $destinationLockRefresher
            ),
            // Return on first violation
            true
        );

        // Destination might be there but not violated, or might not be there
        if ( $violation !== null && $violation->status !== ezcWebdavResponse::STATUS_404 )
        {
            // ezcWebdavErrorResponse
            return $violation;
        }

        // Perform lock refresh (must occur no matter if request succeeds)
        $destinationLockRefresher->sendRequests();

        // Store infos for use on correct moving
        
        // @TODO: Do we always get the correct property here?
        $this->lockDiscoveryProp = $destinationLockRefresher->getLockDiscoveryProperty(
            $destParent
        );

        $sourcePaths = $this->getSourcePaths();
        if ( is_object( $sourcePaths ) )
        {
            // ezcWebdavErrorResponse
            return $sourcePaths;
        }
        $this->sourcePaths = $sourcePaths;

        // Backend now handles the request
        return null;
    }

    /**
     * Returns all pathes in the copy source.
     *
     * This method performs the necessary checks on the source to copy. It
     * returns all paths that are to be moved. In case of any violation of the
     * checks, the method must hold and return an instance of
     * ezcWebdavErrorResponse instead of the desired paths.
     * 
     * @return array(string)|ezcWebdavErrorResponse
     */
    protected function getSourcePaths()
    {
        $propFindReq = new ezcWebdavPropFindRequest( $this->request->requestUri );
        $propFindReq->prop = new ezcWebdavBasicPropertyStorage();
        $propFindReq->prop->attach( new ezcWebdavLockDiscoveryProperty() );
        ezcWebdavLockTools::cloneRequestHeaders(
            $this->request,
            $propFindReq,
            array( 'If', 'Depth' )
        );
        $propFindReq->validateHeaders();

        $propFindMultiStatusRes = ezcWebdavServer::getInstance()->backend->propFind(
            $propFindReq
        );

        if ( !( $propFindMultiStatusRes instanceof ezcWebdavMultiStatusResponse ) )
        {
            return $propFindMultiStatusRes;
        }

        $paths = array();
        foreach ( $propFindMultiStatusRes->responses as $propFindRes )
        {
            $paths[] = $propFindRes->node->path;
        }
        return $paths;
    }

    /**
     * Handles responses to the COPY request.
     * 
     * This method reacts on the response generated by the backend for a COPY
     * request. It takes care to release all locks that existed on the source
     * URI from the newly created destination and to add all necessary locks to
     * it, indicated by its parent.
     *
     * Returns null, if no errors occured, an {@link ezcWebdavErrorResponse}
     * otherwise.
     *
     * @param ezcWebdavResponse $response ezcWebdavCopyResponse
     * @return ezcWebdavResponse|null
     */
    public function generatedResponse( ezcWebdavResponse $response )
    {
        if ( !( $response instanceof ezcWebdavCopyResponse ) )
        {
            return null;
        }

        $backend = ezcWebdavServer::getInstance()->backend;

        // Backend successfully performed request, update with LOCK from parent

        $request    = $this->request;
        $source     = $request->requestUri;
        $dest       = $request->getHeader( 'Destination' );
        $destParent = dirname( $dest );
        $paths      = $this->sourcePaths;

        $lockDiscovery = ( isset( $this->lockDiscoveryProp )
            ? clone $this->lockDiscoveryProp
            : new ezcWebdavLockDiscoveryProperty()
        );

        // Update active locks to reflect new resources
        foreach ( $lockDiscovery->activeLock as $id => $activeLock )
        {
            if ( $activeLock->depth !== ezcWebdavRequest::DEPTH_INFINITY )
            {
                unset( $lockDiscovery->activeLock[$id] );
                continue;
            }
            if ( $activeLock->baseUri === null )
            {
                $activeLock->baseUri   = $destParent;
                $activeLock->lastAccess = null;
            }
        }

        // Perform lock updates
        foreach ( $paths as $path )
        {
            $newPath      = str_replace( $source, $dest, $path );

            $propPatchReq = new ezcWebdavPropPatchRequest( $newPath );
            $propPatchReq->updates->attach( $lockDiscovery, ezcWebdavPropPatchRequest::SET );
            ezcWebdavLockTools::cloneRequestHeaders(
                $request,
                $propPatchReq
            );
            $propPatchReq->validateHeaders();

            $propPatchRes = $backend->propPatch( $propPatchReq );

            if ( !( $propPatchRes instanceof ezcWebdavPropPatchResponse ) )
            {
                throw new ezcWebdavInconsistencyException(
                    "Could not set lock on resource {$newPath}."
                );
            }
        }
       
        return null;
    }
}

?>
