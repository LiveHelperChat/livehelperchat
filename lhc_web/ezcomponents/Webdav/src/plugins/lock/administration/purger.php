<?php
/**
 * File containing the ezcWebdavLockPurger class.
 * 
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Instances of this class are used to purge outdated locks.
 *
 * An instance of this class is used in {@link ezcWebdavLockAdministrator} to
 * purge locks.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockPurger
{
    /**
     * The backend to operate on.
     * 
     * @var ezcWebdavBackend
     */
    protected $backend;

    /**
     * All collected lock properties under $path.
     * 
     * @var array(ezcWebdavLockDiscoveryProperty)
     */
    protected $lockProperties = array();

    /**
     * Lock tokens that need to be purged.
     * 
     * @var array(string=>true)
     */
    protected $locksToPurge = array();

    /**
     * Creates a new lock purger.
     *
     * After creation, the instance can be used to purge locks under a given
     * path with {@link purgeLocks()}.
     * 
     * @param ezcWebdavBackend $backend 
     */
    public function __construct( ezcWebdavBackend $backend )
    {
        $this->backend = $backend;
    }

    /**
     * Purges locks under $path.
     *
     * Checks all active locks on any resource under $path. Purges every lock
     * that has not been accessed for its $timeout value.
     * 
     * @param string $path 
     *
     * @throws ezcWebdavLockAdministrationException
     *         if searching for locks or purging an outdated lock failed.
     */
    public function purgeLocks( $path )
    {
        $this->locksToPurge   = array();
        $this->lockProperties = array();

        $propFindReq = new ezcWebdavPropFindRequest( $path );
        $propFindReq->prop = new ezcWebdavBasicPropertyStorage();
        $propFindReq->prop->attach( new ezcWebdavLockDiscoveryProperty() );
        $propFindReq->setHeader( 'Depth', ezcWebdavRequest::DEPTH_INFINITY );
        $propFindReq->validateHeaders();

        $propFindMultistatusRes = $this->backend->propFind( $propFindReq );

        if ( !( $propFindMultistatusRes instanceof ezcWebdavMultistatusResponse ) )
        {
            throw new ezcWebdavLockAdministrationException(
                'Finding locks failed.',
                $propFindMultistatusRes
            );
        }

        $this->collectPurgeProperties( $propFindMultistatusRes );
        if ( $this->locksToPurge !== array() )
        {
            $this->performPurge();
        }
    }

    /**
     * Collects lock properties and locks to purge.
     *
     * Iterates of the request response $res and stores all found {@link
     * ezcWebdavLockDiscoveryProperty} elements in {@link $lockProperties}. In
     * addition, records all lock tokens that are outdated for later purge in
     * {@link $locksToPurge}.
     * 
     * @param ezcWebdavMultistatusResponse $res 
     */
    protected function collectPurgeProperties( ezcWebdavMultistatusResponse $res )
    {
        $now = time();
        foreach ( $res->responses as $propFindRes )
        {
            if ( !( $propFindRes instanceof ezcWebdavPropFindResponse ) )
            {
                continue;
            }

            foreach ( $propFindRes->responses as $propStatRes )
            {
                if ( $propStatRes->status === ezcWebdavResponse::STATUS_200
                     && $propStatRes->storage->contains( 'lockdiscovery' )
                )
                {
                    $lockDiscoveryProp = clone $propStatRes->storage->get( 'lockdiscovery' );
                    $this->lockProperties[$propFindRes->node->path] = $lockDiscoveryProp;
                    foreach ( $lockDiscoveryProp->activeLock as $activeLock )
                    {
                        if ( $activeLock->lastAccess !== null )
                        {
                            $timeDiff = $now - $activeLock->lastAccess->format( 'U' );
                            if ( $timeDiff > $activeLock->timeout )
                            {
                                // Lock is outdated, record for purging
                                $this->locksToPurge[(string) $activeLock->token] = true;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Performs actual purging of locks.
     *
     * Iterates over {@link $lockProperties} and purges all locks of which the
     * tokens have been collected in {$locksToPurge}.
     * 
     * @return void
     *
     * @throws ezcWebdavLockAdministrationException
     *         in case purging of a lock failed.
     */
    protected function performPurge()
    {
        foreach ( $this->lockProperties as $path => $lockDiscoveryProp )
        {
            $removeIds = array();
            foreach ( $lockDiscoveryProp->activeLock as $id => $activeLock )
            {
                if ( isset( $this->locksToPurge[(string) $activeLock->token] ) )
                {
                    $removeIds[] = $id;
                }
            }
            if ( $removeIds !== array() )
            {
                foreach ( $removeIds as $id )
                {
                    $lockDiscoveryProp->activeLock->offsetUnset( $id );
                }

                $propPatchReq = new ezcWebdavPropPatchRequest( $path );
                $propPatchReq->updates = new ezcWebdavFlaggedPropertyStorage();
                $propPatchReq->updates->attach(
                    $lockDiscoveryProp,
                    ezcWebdavPropPatchRequest::SET
                );
                $propPatchReq->validateHeaders();
                
                $propPatchRes = $this->backend->propPatch( $propPatchReq );
                if ( !( $propPatchRes instanceof ezcWebdavPropPatchResponse ) )
                {
                    throw new ezcWebdavLockAdministrationException(
                        "PROPPATCH to remove timedout lock failed for '$path'.",
                        $propPatchRes
                    );
                }
            }
        }
    }
}

?>
