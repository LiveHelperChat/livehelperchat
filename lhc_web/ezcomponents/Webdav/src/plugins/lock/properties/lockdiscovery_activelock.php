<?php
/**
 * File containing the ezcWebdavLockDiscoveryPropertyActiveLock class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Objects of this class are used in the ezcWebdavLockDiscoveryProperty class.
 *
 * @property int $depth
 *           Constant indicating 0, 1 or infinity.
 * @property string $owner
 *           Owner of this lock (free form string). Null if not provided.
 * @property ezcWebdavDateTime|null $timeout
 *           Timeout date or null for inifinite. Null if not provided.
 * @property array(string) $token
 *           Tokens submitted in <locktocken> (URIs). Null if not provided.
 *           These are originally covered in additional <href> elements, which
 *           is left out here.
 * @property ezcWebdavDateTime $lastAccess
 *           The last time this lock was accessed by a client. Only set, if
 *           this is the property of a lock base.
 * @property string $baseUri
 *           The base path of the lock. Only set, if this is a property on a
 *           path that is no lock base.
 *
 * @version 1.1.4
 * @package Webdav
 *
 * @access private
 */
class ezcWebdavLockDiscoveryPropertyActiveLock extends ezcWebdavSupportedLockPropertyLockentry
{

    /**
     * Creates a new ezcWebdavSupportedLockPropertyLockentry.
     *
     * The $lockType indicates the type of lock in the given $lockScope. The
     * $depth value indicates the depth of collection locks and the free-form
     * $owner string can be used to specify an identifier for the user owning
     * the lock. The $timeout indicates after which time when the lock will be
     * removed, if it is inactive. The $token is the lock token representing
     * this lock.
     *
     * The $lastAccess and $baseUri properties are custom to the lock plugin
     * and are not mentioned in the WebDAV RFC. They are represented in XML in
     * a custom namespace. The $baseUri is the base of the lock (where it was
     * issued). The $lastAccess time object stores when a lock was last
     * accessed. It is only set on the lock base (where $baseUri is null).
     *
     * @param int $lockType Constant ezcWebdavLockRequest::TYPE_*
     * @param int $lockScope Constant ezcWebdavLockRequest::SCOPE_*
     * @param int $depth Constant ezcWebdavRequest::DEPTH_*
     * @param ezcWebdavPotentialUriContent $owner
     * @param int $timeout
     * @param ezcWebdavPotentialUriContent $token
     * @param string $baseUri
     * @param ezcWebdavDateTime $lastAccess
     */
    public function __construct(
        $lockType                           = ezcWebdavLockRequest::TYPE_READ,
        $lockScope                          = ezcWebdavLockRequest::SCOPE_SHARED,
        $depth                              = ezcWebdavRequest::DEPTH_INFINITY,
        ezcWebdavPotentialUriContent $owner = null,
        $timeout                            = null,
        ezcWebdavPotentialUriContent $token = null,
        $baseUri                            = null,
        ezcWebdavDateTime $lastAccess       = null
    )
    {
        parent::__construct( $lockType, $lockScope );
        $this->depth      = $depth;
        $this->owner      = ( $owner === null ? new ezcWebdavPotentialUriContent() : $owner );
        $this->timeout    = $timeout;
        $this->token      = ( $token === null ? new ezcWebdavPotentialUriContent() : $token );
        $this->baseUri    = $baseUri;
        $this->lastAccess = $lastAccess;

        $this->name    = 'activelock';
    }

    /**
     * Sets a property.
     *
     * This method is called when an property is to be set.
     * 
     * @param string $propertyName The name of the property to set.
     * @param mixed $propertyValue The property value.
     * @return void
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'depth':
                if ( $propertyValue !== ezcWebdavRequest::DEPTH_INFINITY && $propertyValue !== ezcWebdavRequest::DEPTH_ONE && $propertyValue !== ezcWebdavRequest::DEPTH_ZERO )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'ezcWebdavLockDiscoveryPropertyActiveLock::DEPTH_*' );
                }
                break;
            case 'owner':
                if ( !is_object( $propertyValue ) || !( $propertyValue instanceof ezcWebdavPotentialUriContent ) )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'ezcWebdavPotentialUriContent' );
                }
                break;
            case 'timeout':
                if ( ( !is_int( $propertyValue ) || $propertyValue < 1 ) && $propertyValue !== null )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'int > 0' );
                }
                break;
            case 'token':
                if ( !is_object( $propertyValue ) || !( $propertyValue instanceof ezcWebdavPotentialUriContent ) )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'ezcWebdavPotentialUriContent' );
                }
                break;
            case 'lastAccess':
                if ( ( !is_object( $propertyValue ) || !( $propertyValue instanceof ezcWebdavDateTime ) ) && $propertyValue !== null )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'ezcWebdavDateTime|null' );
                }
                break;
            case 'baseUri':
                if ( !is_string( $propertyValue ) && $propertyValue !== null )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'string|null' );
                }
                break;
            default:
                return parent::__set( $propertyName, $propertyValue );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Returns if property has no content.
     *
     * Returns true, if the property has no content stored.
     * 
     * @return bool
     */
    public function hasNoContent()
    {
        return false;
    }

    /**
     * Removes all contents from a property.
     *
     * Clears the property, so that it will be recognized as empty later.
     * 
     * @return void
     */
    public function clear()
    {
        parent::clear();

        $this->properties['lockType']  = ezcWebdavLockRequest::TYPE_READ;
        $this->properties['lockScope'] = ezcWebdavLockRequest::SCOPE_SHARED;
        $this->properties['depth']     = ezcWebdavRequest::DEPTH_INFINITY;
        $this->properties['owner']     = new ezcWebdavPotentialUriContent();
        $this->properties['token']     = new ezcWebdavPotentialUriContent();
    }

    /**
     * Clones deep.
     * 
     * @return void
     */
    public function __clone()
    {
        $this->properties['owner'] = clone $this->properties['owner'];
        $this->properties['token'] = clone $this->properties['token'];
    }
}


?>
