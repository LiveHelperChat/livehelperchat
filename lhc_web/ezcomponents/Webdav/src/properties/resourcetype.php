<?php
/**
 * File containing the ezcWebdavResourceTypeProperty class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * An object of this class represents the Webdav property <resourcetype>.
 *
 * @property string $type
 *           The resource type (free form).
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavResourceTypeProperty extends ezcWebdavLiveProperty
{
    /**
     * Indicates that a resource is a non-collection.
     *
     * @see ezcWebdavResourceTypeProperty::$type
     */
    const TYPE_RESOURCE = 1;

    /**
     * Kept for BC reasons.
     *
     * Has the same value as {@link
     * ezcWebdavResourceTypeProperty::TYPE_RESOURCE}.
     *
     * @apichange Will be removed in next major version.
     */
    const TYPE_RESSOURCE = self::TYPE_RESOURCE;

    /**
     * Indicates that a resource is a collection. 
     *
     * @see ezcWebdavResourceTypeProperty::$type
     */
    const TYPE_COLLECTION = 2;
    
    /**
     * Creates a new ezcWebdavResourceTypeProperty.
     *
     * The given $type indicates either a collection or non-collection
     * resource ({@link self::TYPE_COLLECTION} or {@link
     * self::TYPE_RESOURCE}).
     * 
     * @param int $type
     * @return void
     */
    public function __construct( $type = null )
    {
        parent::__construct( 'resourcetype' );

        $this->properties['type'] = null;
        $this->type = $type;
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
            case 'type':
                if ( $propertyValue !== self::TYPE_RESOURCE && $propertyValue !== self::TYPE_COLLECTION && $propertyValue !== null )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'ezcWebdavResourceTypeProperty::TYPE_RESOURCE, ezcWebdavResourceTypeProperty::TYPE_COLLECTION or null' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                parent::__set( $propertyName, $propertyValue );
        }
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
        return $this->properties['type'] === null;
    }
}

?>
