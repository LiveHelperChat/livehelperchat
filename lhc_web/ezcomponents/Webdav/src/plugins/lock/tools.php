<?php
/**
 * File containing the ezcWebdavLockTools class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Tool class for use in the lock plugin.
 *
 * This class contains several tool methods, which are used by the lock plugin
 * and its handlers.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockTools
{
    /**
     * Plugin options.
     * 
     * @var ezcWebdavLockPluginOptions
     */
    public $options;

    /**
     * Default headers to clone in {@link cloneRequestHeaders()}.
     * 
     * @var array(string)
     */
    protected static $defaultCloneHeaders = array(
        'Authorization',
    );

    /**
     * Creates a new tool instances.
     * 
     * @param ezcWebdavLockPluginOptions $options 
     */
    public function __construct( ezcWebdavLockPluginOptions $options )
    {
        $this->options = $options;
    }

    /**
     * Clones headers in $from to headers in $to.
     *
     * Clones all headers with names given in $heades from the request $from to
     * the request in $to. In case $defaultHeaders is set to true, the headers
     * mentioned in {@link $defaultCloneHeaders} are cloned in addition.
     *
     * Note, that this method does not call {@link
     * ezcWebdavRequest::validateHeaders()}, since headers in $to might still
     * be incomplete. You need to call this method manually, before sending $to
     * to the backend or accessing its headers for reading.
     * 
     * @param ezcWebdavRequest $from 
     * @param ezcWebdavRequest $to 
     * @param array $headers 
     * @param bool $defaultHeaders 
     */
    public static function cloneRequestHeaders(
        ezcWebdavRequest $from,
        ezcWebdavRequest $to,
        $headers = array(),
        $defaultHeaders = true
    )
    {
        if ( $defaultHeaders )
        {
            $headers = array_merge( self::$defaultCloneHeaders, $headers );
            $headers = array_unique( $headers );
        }

        foreach( $headers as $headerName )
        {
            $to->setHeader( $headerName, $from->getHeader( $headerName ) );
        }
    }

    /**
     * Checks the given $request for If header and general lock violations.
     *
     * This method performs a PROPFIND request on the backend and retrieves the
     * properties <lockdiscovery>, <getetag> and <lockinfo> for all affected
     * resources. It then checks for the following violations:
     *
     * <ul>
     *   <li>Authorization</li>
     *   <li>Restrictions to etags and lock tokens provided by the If header</li>
     *   <li>General violations of other users locks</li>
     * </ul>
     *
     * Since the utilized information from the PROPFIND request must be used in
     * other places around this class, the method may receive a $generator
     * object. This object will be notified of every processed resource and
     * receives the properties listed above. You should use this mechanism to
     * avoid duplicate requesting of these properties and store the information
     * you desire in the background. In case the checkViolations() method
     * returns null, all checks passed and you can savely execute the desired
     * requests. If $returnOnViolation is set, violations are not collected
     * until all resources are checked, but the method returns as soon as the
     * first violation occurs.
     * 
     * @param ezcWebdavLockCheckInfo $checkInfo
     * @param bool $returnOnViolation
     * @return ezcWebdavMultistatusResponse|ezcWebdavErrorResponse|null
     */
    public function checkViolations( ezcWebdavLockCheckInfo $checkInfo, $returnOnViolation = false )
    {
        $srv = ezcWebdavServer::getInstance();

        $propFind       = new ezcWebdavPropFindRequest( $checkInfo->path );
        $propFind->prop = new ezcWebdavBasicPropertyStorage();

        $propFind->prop->attach( new ezcWebdavLockDiscoveryProperty() );
        $propFind->prop->attach( new ezcWebdavGetEtagProperty() );

        $propFind->setHeader(
            'Depth',
            ( $checkInfo->depth !== null ? $checkInfo->depth : ezcWebdavRequest::DEPTH_ONE )
        );
        $propFind->setHeader( 'Authorization', $checkInfo->authHeader );

        $propFind->validateHeaders();

        $propFindMultistatusRes = $srv->backend->performRequest( $propFind );

        if ( !( $propFindMultistatusRes instanceof ezcWebdavMultistatusResponse ) )
        {
            // Bubble up error from backend
            return $propFindMultistatusRes;
        }

        foreach ( $propFindMultistatusRes->responses as $propFindRes )
        {
            if ( ( $res = $this->checkEtagsAndLocks( $propFindRes, $checkInfo ) ) !== null )
            {
                return $res;
            }

            // Notify request generator on affected ressource
            if ( $checkInfo->requestGenerator !== null )
            {
                $checkInfo->requestGenerator->notify( $propFindRes );
            }
        }

        return null;
    }

    /**
     * Returns a lock token for the resource affected by $request.
     *
     * Generates a lock token that obeys to the opaquelocktoken scheme, using a
     * UUID v3.
     * 
     * @param ezcWebdavLockRequest $request 
     * @return string
     *
     * @todo Should we use sha1 instead of md5?
     */
    public function generateLockToken( ezcWebdavLockRequest $request )
    {
        $rawToken = md5(
            $_SERVER['SERVER_PROTOCOL'] . $_SERVER['HTTP_HOST'] . $request->requestUri . microtime( true )
        );

        // @TODO: Needs version number in UUID v3/5!

        return sprintf(
            'opaquelocktoken:%s-%s-%s-%s-%s',
            substr( $rawToken,  0, 8 ),
            substr( $rawToken,  8, 4 ),
            substr( $rawToken, 12, 4 ),
            substr( $rawToken, 16, 4 ),
            substr( $rawToken, 20 )
        );
    }

    /**
     * Returns a new active lock element according to the given data.
     *
     * Creates a new instance of {@link
     * ezcWebdavLockDiscoveryPropertyActiveLock} that can be used with an
     * {@link ezcWebdavLockDiscoveryProperty}. Most information for this
     * property content is fetched from the given $request. The $lockToken for
     * the acquired lock must be provided in addition. Information used is:
     * 
     * @param ezcWebdavLockRequest $request 
     * @param string $lockToken 
     * @return ezcWebdavLockDiscoveryPropertyActiveLock
     */
    public function generateActiveLock( ezcWebdavLockRequest $request, $lockToken )
    {
        return new ezcWebdavLockDiscoveryPropertyActiveLock(
            $request->lockInfo->lockType,
            $request->lockInfo->lockScope,
            $request->getHeader( 'Depth' ),
            $request->lockInfo->owner,
            $this->getTimeoutValue(
                ( $timeouts = $request->getHeader( 'Timeout' ) ) === null ? array() : $timeouts
            ),
            // Generated lock tokens conform to the opaquelocktoken URI scheme
            new ezcWebdavPotentialUriContent( $lockToken, true ),
            null,
            new ezcWebdavDateTime()
        );
    }

    /**
     * Returns an appropriate timeout value for the given LOCK request.
     *
     * Checks each of the Timeout header values of the $request and chooses the
     * smallest timeout among these and the {@link ezcWebdavLockPluginOptions}
     * $timeout property. The timeout returned corresponds to number of seconds
     * of inactivity, before a lock is released.
     * 
     * @param array(int) $timeoutValues
     * @return int
     */
    public function getTimeoutValue( array $timeoutValues )
    {
        // Default
        $timeout = $this->options->lockTimeout;

        foreach ( $timeoutValues as $desiredTimeout )
        {
            if ( $desiredTimeout < $timeout )
            {
                $timeout = $desiredTimeout;
            }
        }

        return $timeout;
    }

    /**
     * Returns if the given $response resulted from a lock problem.
     *
     * If the given $response is null, no error happened at all (returns
     * false). Otherwise the first response in the multi status is checked for
     * lock violation errors.
     * 
     * @param ezcWebdavMultistatusResponse $response 
     * @return bool
     */
    public function isLockError( ezcWebdavMultistatusResponse $response = null )
    {
        if ( $response === null )
        {
            return false;
        }
        $status = $response->responses[0]->status;
        return (
            $status === ezcWebdavResponse::STATUS_405
            || $status === ezcWebdavResponse::STATUS_409
            || $status === ezcWebdavResponse::STATUS_423
            || $status === ezcWebdavResponse::STATUS_424
        );
    }

    /**
     * Checks if etag and locks on a resource violate the If header.
     * 
     * @param ezcWebdavPropFindResponse $propFindRes 
     * @param ezcWebdavLockCheckInfo $checkInfo 
     * @return null|ezcWebdavErrorResponse
     */
    protected function checkEtagsAndLocks( ezcWebdavPropFindResponse $propFindRes, ezcWebdavLockCheckInfo $checkInfo )
    {
        // @TODO: This only works for exclusive locks

        $path = $propFindRes->node->path;

        try
        {
            $data = $this->extractCheckProperties( $propFindRes );
        }
        catch ( ezcWebdavLockAccessDeniedException $e )
        {
            return $this->createLockViolation(
                new ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_403,
                    $path
                ),
                $propFindRes->node,
                null
            );
        }

        // No If header to check against
        if ( $checkInfo->ifHeader === null )
        {
            if ( count( $data['lockdiscovery']->activeLock ) === 0
                 || ( $checkInfo->allowSharedLocks && $this->isSharedLock( $data['lockdiscovery'] ) )
            )
            {
                // No lock, no condition, no checks. ;)
                // Shared lock in shared lock is allowed.
                return null;
            }
            return $this->createLockViolation(
                new ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_423,
                    $path
                ),
                $propFindRes->node,
                $data['lockdiscovery']
            );
        }

        $activeLockTokens = $this->extractActiveTokens(
            $data['lockdiscovery'],
            $checkInfo->authHeader
        );
        $activeEtag = ( $data['getetag'] !== null ? $data['getetag']->etag : '' );

        // Check if any of the active locks belongs to the user.
        if ( count( $data['lockdiscovery']->activeLock ) > 0 && count( $activeLockTokens ) === 0 )
        {
            return $this->createLockViolation(
                new ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_423,
                    $path
                ),
                $propFindRes->node,
                $data['lockdiscovery']
            );
        }

        // Perform If header validation, must be matched no matter if locked.
        $ifItems = $checkInfo->ifHeader[$path];
        if ( $ifItems !== array() )
        {
            $conditionVerified = false;
            // If header has conditions for the resource verify at least 1
            // condition set.
            foreach ( $ifItems as $ifItem )
            {
                if ( $this->checkLock( $ifItem, $activeLockTokens )
                     && $this->checkEtag( $ifItem, $activeEtag ) )
                {
                    $conditionVerified = true;
                    break;
                }
            }
            if ( !$conditionVerified )
            {
                return $this->createLockViolation(
                    new ezcWebdavErrorResponse(
                        ezcWebdavResponse::STATUS_412,
                        $path
                    ),
                    $propFindRes->node,
                    $data['lockdiscovery']
                );
            }
        }

        if ( count( $data['lockdiscovery']->activeLock ) === 0 )
        {
            // Not locked, no more checks
            return  null;
        }
        
        // Verify that at least 1 active lock token was submitted in the If
        // header
        $intersect = array_intersect(
            $activeLockTokens,
            $checkInfo->ifHeader->getLockTokens()
        );
        if ( count( $intersect ) !== 0 )
        {
            // Condition successfully verified
            return null;
        }

        // If header not verified
        return $this->createLockViolation(
            new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_423,
                $path
            ),
            $propFindRes->node,
            $data['lockdiscovery']
        );
    }

    /**
     * Checks if a lock is a shared lock or exclusive.
     *
     * Checks the first active lock in the given $lockDiscovery property, if it
     * is a shared lock. Returns true, for shared locks, false for exclusive
     * ones.
     * 
     * @param ezcWebdavLockDiscoveryProperty $lockDiscovery 
     * @return bool
     */
    protected function isSharedLock( ezcWebdavLockDiscoveryProperty $lockDiscovery )
    {
        return ( $lockDiscovery->activeLock[0]->lockScope ===  ezcWebdavLockRequest::SCOPE_SHARED );
    }

    /**
     * Extracts active lock tokens from a lockdiscovery property.
     *
     * Returns an array of string lock tokens, that are active on the affected
     * resource and owned by the currently active user.
     * 
     * @param ezcWebdavLockDiscoveryProperty $lockDiscovery 
     * @param ezcWebdavAuth $authHeader 
     * @return array(string)
     */
    protected function extractActiveTokens(
        ezcWebdavLockDiscoveryProperty $lockDiscovery = null,
        ezcWebdavAuth $authHeader
    )
    {
        $auth             = ezcWebdavServer::getInstance()->auth;
        $activeLockTokens = array();

        foreach ( $lockDiscovery->activeLock as $activeLock )
        {
            $token = (string) $activeLock->token;
            if ( $auth->ownsLock( $authHeader->username, $token ) )
            {
                $activeLockTokens[] = $token;
            }
        }
        return $activeLockTokens;
    }

    /**
     * Returns if the $ifItem validates agains $lockDiscovery.
     *
     * Checks if the the conditions defined in the given $ifItem comply to any
     * of the $activeLockTokens.
     * 
     * @param ezcWebdavLockIfHeaderListItem $ifItem 
     * @param array $activeLockTokens
     * @return bool
     */
    protected function checkLock( ezcWebdavLockIfHeaderListItem $ifItem, array $activeLockTokens )
    {
        foreach ( $ifItem->lockTokens as $lockToken )
        {
            if ( !( $lockToken->negated ^ in_array( (string) $lockToken, $activeLockTokens ) ) )
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns in the given $ifItem validates against the $getEtag.
     *
     * Checks if the the conditions defined in the given $ifItem comply to the
     * $activeEtag.
     *
     * @param ezcWebdavLockIfHeaderListItem $ifItem 
     * @param string $activeEtag
     * @return bool
     */
    protected function checkEtag( ezcWebdavLockIfHeaderListItem $ifItem, $activeEtag )
    {
        foreach ( $ifItem->eTags as $etag )
        {
            if ( !( $etag->negated ^ $activeEtag === (string) $etag ) )
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Extracts the properties for the If header check from the $propFindRes.
     * 
     * @param ezcWebdavPropFindResponse $propFindRes 
     * @return array(string)
     */
    protected function extractCheckProperties( ezcWebdavPropFindResponse $propFindRes )
    {
        $data = array(
            'getetag'       => null,
            'lockdiscovery' => null,
        );
        foreach ( $propFindRes->responses as $propStatRes )
        {
            switch ( $propStatRes->status )
            {
                case ezcWebdavResponse::STATUS_200:
                    $data['getetag'] = $propStatRes->storage->get(
                        'getetag'
                    );
                    // Ensure that lockdiscovery is there
                    $data['lockdiscovery'] = ( $propStatRes->storage->contains( 'lockdiscovery' )
                        ? $propStatRes->storage->get( 'lockdiscovery' )
                        : new ezcWebdavLockDiscoveryProperty()
                    );
                    break;

                case ezcWebdavResponse::STATUS_403:
                    // Access denied
                    throw new ezcWebdavLockAccessDeniedException(
                        $propFindRes->node
                    );
            }
        }
        return $data;
    }

    /**
     * Attaches the given data to the $error.
     *
     * @param ezcWebdavErrorResponse $error
     * @param ezcWebdavResource|ezcWebdavCollection $node
     * @param ezcWebdavLockDiscoveryProperty $lockDiscovery
     * @return ezcWebdavErrorResponse
     */
    protected function createLockViolation(
        ezcWebdavErrorResponse $error,
        $node,
        ezcWebdavLockDiscoveryProperty $lockDiscovery = null
    )
    {
        $error->setPluginData(
            ezcWebdavLockPlugin::PLUGIN_NAMESPACE,
            'node',
            $node
        );
        $error->setPluginData(
            ezcWebdavLockPlugin::PLUGIN_NAMESPACE,
            'lockdiscovery',
            $lockDiscovery
        );
        return $error;
    }

}

?>
