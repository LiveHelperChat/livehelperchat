<?php
/**
 * File containing the ezcWebdavLockRefreshRequestGenerator class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Check observer that generates PROPPATCH requests to refresh locks.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockRefreshRequestGenerator implements ezcWebdavLockCheckObserver
{
    /**
     * Lock bases that have not been found, yet.
     *
     * Structure:
     * <code>
     * <?php
     *  array(
     *      '/some/lock/base' => true,
     *      '/anotther/lock/base' => true,
     *  );
     * ?>
     * </code>
     * 
     * @var array(string=>bool)
     */
    protected $notFoundLockBases = array();

    /**
     * Contains <lockdiscovery> properties that need to ba updates.
     * 
     * @var array(string=>ezcWebdavLockDiscoveryProperty)
     */
    protected $lockDiscoveryProperties = array();

    /**
     * All paths that require a property update. 
     * 
     * @var array(string=>bool)
     */
    protected $pathsToUpdate = array();

    /**
     * The If header containing the tokens to refresh. 
     * 
     * @var ezcWebdavLockIfHeaderList
     */
    protected $ifHeader;

    /**
     * The request that issued the lock refresh.
     * 
     * @var ezcWebdavLockIfHeaderList
     */
    protected $request;

    /**
     * New timeout to set for the lock. 
     * 
     * @var int|null
     */
    protected $timeout;

    /**
     * Creates a new observer for lock refreshs.
     *
     * This observer collects the base for all affected locks of a request and
     * creates PROPPATCH requests to update the affected locks.
     *
     * The PROPPATCH requests can be obtained after collecting, using the
     * {@link getRequests()} or can be send using the {@link sendRequests()}
     * method.
     * 
     * @param ezcWebdavRequest $request 
     * @param int $timeout 
     * @return void
     */
    public function __construct( ezcWebdavRequest $request, $timeout = null )
    {
        $this->issuingRequest = $request;
        $this->ifHeader       = $request->getHeader( 'If' );
        $this->affectedTokens = ( $this->ifHeader === null ? array() : $this->ifHeader->getLockTokens() );
        $this->timeout        = $timeout;
    }

    /**
     * Notify the request generator about a checked resource. 
     * 
     * @param ezcWebdavPropFindResponse $response 
     * @return void
     */
    public function notify( ezcWebdavPropFindResponse $response )
    {
        $path              = $response->node->path;
        $origLockDiscovery = $this->extractLockDiscovery( $response );
        $lockDiscovery     = clone $origLockDiscovery;

        if ( $this->affectedTokens === array() || count( $lockDiscovery->activeLock ) === 0 )
        {
            // Nothing to do
            return null;
        }
        
        $needsUpdate = false;
        foreach ( $lockDiscovery->activeLock as $activeLock )
        {
            if ( !in_array( (string) $activeLock->token, $this->affectedTokens ) )
            {
                // Lock must not be updated
                continue;
            }

            // Check for lock base
            if ( $activeLock->baseUri === null )
            {
                $activeLock->lastAccess = new ezcWebdavDateTime();
                unset( $this->notFoundLockBases[$path] );
                $needsUpdate = true;
            }
            else
            {
                // Check if base for lock is already recorded
                if ( !isset( $this->lockDiscoveryProperties[$activeLock->baseUri] ) )
                {
                    // No, it's not notify it for later fetching
                    $this->notFoundLockBases[$activeLock->baseUri] = true;
                }
            }
            
            // Check for timeout update
            if ( $this->timeout !== null && $this->timeout !== $activeLock->timeout )
            {
                $activeLock->timeout = $this->timeout;
                $needsUpdate = true;
            }
        }

        if ( $needsUpdate )
        {
            $this->lockDiscoveryProperties[$path] = $lockDiscovery;
            $this->pathsToUpdate[$path]           = true;
        }
        else
        {
            $this->lockDiscoveryProperties[$path] = $origLockDiscovery;
        }
    }

    /**
     * Returns the requests necessary to refresh the locks.
     * 
     * @return array(ezcWebdavRequest)
     */
    public function getRequests()
    {
        foreach ( $this->notFoundLockBases as $lockBase => $dummy )
        {
            $this->fetchLockBase( $lockBase );
        }

        if ( count( $this->notFoundLockBases ) )
        {
            throw new ezcWebdavInconsistencyException(
                'Some lock bases could not be determined.'
            );
        }

        return $this->generateRequests();
    }

    /**
     * Receives the <lockdiscovery> property for $path.
     *
     * Returs the desired <lockdiscovery> property, if it was found and
     * recorded, otherwise null.
     * 
     * @param string $path
     * @return ezcWebdavLockDiscoveryProperty|null
     */
    public function getLockDiscoveryProperty( $path )
    {
        if ( isset( $this->lockDiscoveryProperties[$path] ) )
        {
            return $this->lockDiscoveryProperties[$path];
        }
        return null;
    }

    /**
     * Sends the generated requests and performs the lock refresh.
     *
     * Returns an error response, if an error occurs.
     * 
     * @return ezcWebdavErrorResponse|null
     */
    public function sendRequests()
    {
        $backend = ezcWebdavServer::getInstance()->backend;
        
        $reqs = $this->getRequests();

        foreach ( $reqs as $propPatch )
        {
            $propPatch->validateHeaders();
            $res = $backend->propPatch( $propPatch );
            if ( !( $res instanceof ezcWebdavPropPatchResponse ) )
            {
                return $res;
            }
        }
        return null;
    }

    /**
     * Fetches a lock base at a given $path.
     *
     * This method fetches a lock base, in case we need to refresh a lock, of
     * which the base was not below the request uri. The method issues the
     * necessary PROPFOND request and hands the result over to {@link notify()}
     * again.
     * 
     * @param string $path 
     * @return void
     *
     * @throws ezcWebdavInconsistencyException
     *         in case no lock base is found in the given $path.
     */
    protected function fetchLockBase( $path )
    {
        $propFind       = new ezcWebdavPropFindRequest( $path );
        $propFind->prop = new ezcWebdavBasicPropertyStorage();

        $propFind->prop->attach( new ezcWebdavLockDiscoveryProperty() );

        ezcWebdavLockTools::cloneRequestHeaders( $this->issuingRequest, $propFind );
        $propFind->validateHeaders();

        $response = ezcWebdavServer::getInstance()->backend->propFind(
            $propFind
        );

        if ( !( $response instanceof ezcWebdavMultistatusResponse ) )
        {
            throw new ezcWebdavInconsistencyException(
                "Could not find expected lock base at path '$path'."
            );
        }

        $this->notify( $response->responses[0] );
    }

    /**
     * Generates the requests to update the locks.
     *
     * This method generates a PROPPATCH request for each <lockinfo> property
     * in {@link $lockBaseProperties} and returns all of them in an array.
     * 
     * @return array(ezcWebdavPropPatchRequest)
     */
    protected function generateRequests()
    {
        $requests = array();
        foreach ( $this->pathsToUpdate as $path => $dummy )
        {
            $propPatch = new ezcWebdavPropPatchRequest( $path );
            $propPatch->updates->attach(
                $this->lockDiscoveryProperties[$path],
                ezcWebdavPropPatchRequest::SET
            );
            ezcWebdavLockTools::cloneRequestHeaders( $this->issuingRequest, $propPatch );

            $requests[] = $propPatch;
        }
        return $requests;
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
