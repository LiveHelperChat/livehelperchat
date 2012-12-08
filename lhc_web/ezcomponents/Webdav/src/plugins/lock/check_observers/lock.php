<?php
/**
 * File containing the ezcWebdavLockLockRequestGenerator class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Request generator used to generate PROPPATCH requests to realize the LOCK.
 *
 * This lock check observer generates PROPPATCH request to acquire a lock on
 * all checked resources. The generated requests can be obtained using the
 * {@link getRequests()} method, after the lock check successfully passed.
 *
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockLockRequestGenerator implements ezcWebdavLockCheckObserver
{
    /**
     * Generated requests. 
     * 
     * @var array(ezcWebdavPropPatchRequest)
     */
    protected $requests = array();

    /**
     * Request that issued the lock. 
     * 
     * @var ezcWebdavLockRequest
     */
    protected $issuingRequest;

    /**
     * Active lock information part of lock response.
     * 
     * @var ezcWebdavLockDiscoveryPropertyActiveLock
     */
    protected $activeLock;

    /**
     * Lockdiscovery property of the resource the lock was issued to. 
     * 
     * @var ezcWebdavLockDiscoveryProperty
     */
    protected $mainLockDiscoveryProp;

    /**
     * Creates a new request generator.
     *
     * The $request is the LOCK requst object, which was sent by the client.
     * The $activeLock part will be attached to the <lockdiscovery> property of
     * every affected resource.
     * 
     * @param ezcWebdavLockRequest $request 
     * @param ezcWebdavLockDiscoveryPropertyActiveLock $activeLock
     */
    public function __construct(
        ezcWebdavLockRequest $request,
        ezcWebdavLockDiscoveryPropertyActiveLock $activeLock
    )
    {
        $this->issuingRequest = $request;
        $this->activeLock     = $activeLock;
    }

    /**
     * Notify the generator about a response.
     *
     * Notifies the request generator that a request should be generated w.r.t.
     * the given $response.
     * 
     * @param ezcWebdavPropFindResponse $response 
     * @return void
     */
    public function notify( ezcWebdavPropFindResponse $response )
    {
        $originalRequestUri = $this->issuingRequest->requestUri;
        $path               = $response->node->path;

        $propPatch = new ezcWebdavPropPatchRequest( $response->node->path );

        $lockDiscovery = clone $this->extractLockDiscovery( $response );
        $activeLock    = clone $this->activeLock;

        if ( $originalRequestUri === $path )
        {
            // Is lock base
            $activeLock->baseUri   = null;
            $activeLock->lastAccess = new ezcWebdavDateTime();

            // Store for later use
            $this->mainLockDiscoveryProp = $lockDiscovery;
        }
        else
        {
            // Not the lock base
            $activeLock->baseUri   = $originalRequestUri;
            $activeLock->lastAccess = null;
        }

        $lockDiscovery->activeLock->append( $activeLock );
        
        $propPatch->updates->attach(
            $lockDiscovery,
            ezcWebdavPropPatchRequest::SET
        );

        $this->requests[] = $propPatch;
    }

    /**
     * Returns all collected requests generated in the processor. 
     * 
     * @return array(ezcWebdavPropPatchRequest)
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Returns the lock discovery property assigned to $path. 
     *
     * This method is used to retrieve the lock discovery property for $path,
     * to include it into the LOCK response body.
     *
     * @param string $path
     * @return ezcWebdavLockDiscoveryProperty
     */
    public function getLockDiscoveryProperty( $path )
    {
        return $this->mainLockDiscoveryProp;
    }

    /**
     * Extracts the current lock discovery property.
     * 
     * Extracts the current lock discovery property of the affected node from
     * PROPFIND $response. If no lockdiscovery property could be found, a new
     * one is returned.
     * 
     * @param ezcWebdavPropFindResponse $response 
     * @return ezcWebdavLockDiscoveryProperty
     */
    protected function extractLockDiscovery( ezcWebdavPropFindResponse $response )
    {
        foreach ( $response->responses as $propStatRes )
        {
            if ( $propStatRes->status === ezcWebdavResponse::STATUS_200
                 && $propStatRes->storage->contains( 'lockdiscovery' )
            )
            {
                return $propStatRes->storage->get( 'lockdiscovery' );
            }
        }
        // Not found
        return new ezcWebdavLockDiscoveryProperty();
    }
}

?>
