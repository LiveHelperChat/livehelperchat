<?php
/**
 * File containing the ezcCacheStackStorageConfiguration class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * ezcCacheStackStorageConfiguration 
 *
 * Configuration for a {@link ezcCacheStackableStorage} inside an {@link
 * ezcCacheStack}. The configuration consists of a string $id that identifies
 * the $storage uniquely inside the stack. The $itemLimit is the maximum number
 * of items that might be stored in the $storage at once. This number is
 * determined by the used {@link ezcCacheStackMetaData} used in the stack. The
 * $freeRate is the fraction of $itemLimit that will be freed by the used
 * {@link ezcCacheStackReplacementStrategy} when the storage runs full.
 *
 * @property-read string $id
 *                The unique ID of the configured storage in the stack.
 * @property-read ezcCacheStackableStorage $storage
 *                The storage that is configured with this configuration. A
 *                storage might only be part of 1 stack and this only once.
 * @property-read int $itemLimit
 *                Maximum number of items to be stored in $storage.
 * @property-read float $freeRate
 *                Fraction of $itemLimit that is freed if $storage runs full.
 * @package Cache
 * @version 1.5
 */
class ezcCacheStackStorageConfiguration
{
    /**
     * Properties
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'id'        => null,
        'storage'   => null,
        'itemLimit' => null,
        'freeRate'  => null,
    );

    /**
     * Creates a new storage configuration.
     *
     * This method creates an new configuration with the given values. Note
     * that, once set, these values cannot be changed anymore to avoid
     * inconsistencies in the stack. For details about the parameters, please
     * refer to the properties documentation of this class.
     *
     * Note that the properties can only be set once, using this constructore,
     * and are not changeable via property access.
     * 
     * @param string $id 
     * @param ezcCacheStackableStorage $storage 
     * @param int $itemLimit 
     * @param float $freeRate 
     *
     * @throws ezcBaseValueException
     *         if a given value does not conform to the indicated format.
     */
    public function __construct( $id, ezcCacheStackableStorage $storage, $itemLimit, $freeRate )
    {
        if ( !is_string( $id ) || strlen( $id ) < 1 )
        {
            throw new ezcBaseValueException(
                'id', $id, 'string, length > 0'
            );
        }
        if ( !is_int( $itemLimit ) || $itemLimit < 1 )
        {
            throw new ezcBaseValueException(
                'itemLimit', $itemLimit, 'int > 1'
            );
        }
        if ( !is_numeric( $freeRate ) ||  $freeRate <= 0 || $freeRate > 1 )
        {
            throw new ezcBaseValueException(
                'freeRate', $freeRate, 'float > 0 and <= 1'
            );
        }
        $this->properties['id']        = $id;
        $this->properties['storage']   = $storage;
        $this->properties['itemLimit'] = $itemLimit;
        $this->properties['freeRate']  = $freeRate;
    }

    /**
     * Returns the value of a property.
     * 
     * @param string $propertyName 
     * @return mixed
     * @ignore
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
     * 
     * @param string $propertyName 
     * @return bool
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }

    /**
     * Sets a property.
     * 
     * @param string $propertyName 
     * @param mixed $propertyValue 
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        if ( $this->__isset( $propertyName ) )
        {
            throw new ezcBasePropertyPermissionException(
                $propertyName,
                ezcBasePropertyPermissionException::READ
            );
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }
}

?>
