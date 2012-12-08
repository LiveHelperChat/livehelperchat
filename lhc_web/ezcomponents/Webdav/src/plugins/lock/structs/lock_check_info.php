<?php
/**
 * File containing the ezcWebdavLockCheckInfo struct class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Struct containing information on lock checking for a request.
 *
 * An array of such structs is given to {@link
 * ezcWebdavLockPlugin::checkViolations()}. It contains all information
 * necessary to check violations on locks.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockCheckInfo extends ezcBaseStruct
{
    /**
     * Path to check
     * 
     * @var string
     */
    public $path;

    /**
     * Depth to check in the $path. 
     * 
     * @var ezcWebdavRequest::DEPTH_*
     */
    public $depth;

    /**
     * If header item fitting to that path 
     * 
     * @var ezcWebdavLockIfHeaderTaggedList|ezcWebdavLockIfHeaderNoTagList
     */
    public $ifHeader;

    /**
     * Authorization header content. 
     * 
     * @var ezcWebdavAuthBasic|ezcWebdavAuthDigest|null
     */
    public $authHeader;

    /**
     * Access type for auth checks. 
     * 
     * @var ezcWebdavAuthorizer::ACCESS
     */
    public $access;

    /**
     * Request generator to notify for this $path. 
     * 
     * @var ezcWebdavLockCheckObserver
     */
    public $requestGenerator;

    /**
     * If a lock-null resource may occur while checking. 
     * 
     * @var bool
     */
    public $allowSharedLocks;

    /**
     * Creates a new lock info struct.
     *
     * Creates a new struct that indicates how lock conditions should be checked.
     *
     * @param string $path
     * @param int $depth
     * @param ezcWebdavIfHeaderList $ifHeader
     * @param ezcWebdavAuth $authHeader
     * @param int $access
     * @param ezcWebdavLockCheckObserver $requestGenerator
     * @param bool $allowSharedLocks
     */
    public function __construct(
        $path                                        = '',
        $depth                                       = ezcWebdavRequest::DEPTH_INFINITY,
        $ifHeader                                    = null,
        $authHeader                                  = null,
        $access                                      = ezcWebdavAuthorizer::ACCESS_WRITE,
        ezcWebdavLockCheckObserver $requestGenerator = null,
        $allowSharedLocks                            = false
    )
    {
        $this->path             = $path;
        $this->depth            = $depth;
        $this->ifHeader         = $ifHeader;
        $this->authHeader       = $authHeader;
        $this->access           = $access;
        $this->requestGenerator = $requestGenerator;
        $this->allowSharedLocks = $allowSharedLocks;
    }
}

?>
