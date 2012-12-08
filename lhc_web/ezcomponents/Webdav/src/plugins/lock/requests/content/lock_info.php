<?php
/**
 * File containing the ezcWebdavRequestLockInfoContent class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class representing the <lockinfo /> XML element in the LOCK request body.
 *
 * An instance of this class represents the <lockinfo /> XML element,
 * that may optionally be contained in the body of a LOCK request.
 * 
 * An instance of this class must have the properties {@link $lockScope} and
 * {@link $lockType} set. The property {@link $owner} is optional. 
 * 
 * @package Webdav
 * @version 1.1.4
 * @access private
 *
 * @property int $lockScope Represents the <lockscope /> XML element.
 * @property int $lockType Represents the <locktype /> XML element.
 * @property string $owner Represents the <owner /> XML element.
 */
class ezcWebdavRequestLockInfoContent extends ezcWebdavInfrastructureBase
{
    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Creates a new instance.
     * 
     * @param int $lockScope 
     * @param int $lockType 
     * @param ezcWebdavPotentialUriContent $owner 
     * @return void
     */
    public function __construct( $lockScope, $lockType, ezcWebdavPotentialUriContent $owner = null )
    {
        $this->properties['lockScope'] = null;
        $this->properties['lockType']  = null;
        $this->properties['owner']     = null;

        $this->lockScope = $lockScope;
        $this->lockType  = $lockType;
        $this->owner     = ( $owner === null ? new ezcWebdavPotentialUriContent() : $owner );
    }

    /**
     * Sets a property.
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
            case 'lockScope':
                if ( $propertyValue !== ezcWebdavLockRequest::SCOPE_SHARED && $propertyValue !== ezcWebdavLockRequest::SCOPE_EXCLUSIVE )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcWebdavLockRequest::SCOPE_SHARED or ezcWebdavLockRequest::SCOPE_EXCLUSIVE'
                    );
                }
                break;
            case 'lockType':
                if ( $propertyValue !== ezcWebdavLockRequest::TYPE_WRITE && $propertyValue !== ezcWebdavLockRequest::TYPE_READ )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcWebdavLockRequest::TYPE_READ or ezcWebdavLockRequest::TYPE_WRITE'
                    );
                }
                break;
            case 'owner':
                if ( !is_object( $propertyValue ) || !( $propertyValue instanceof ezcWebdavPotentialUriContent ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcWebdavPotentialUriContent'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property get access.
     * Simply returns a given property.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property propertys is not an instance of
     * @param string $propertyName The name of the property to get.
     * @return mixed The property value.
     *
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a write-only property.
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Returns if a property exists.
     * Returns true if the property exists in the {@link $properties} array
     * (even if it is null) and false otherwise. 
     *
     * @param string $propertyName Option name to check for.
     * @return void
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }
}

?>
