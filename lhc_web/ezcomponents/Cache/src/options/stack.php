<?php
/**
 * File containing the ezcCacheStackOptions class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Options class for ezcCacheStack instances.
 * 
 * This options class is used with {@link ezcCacheStack} instances.
 *
 * The $configurator property is special, since it only takes effect during
 * construction of the stack. The idea is, to use this in combination with
 * {@link ezcCacheManager}. The method {@link
 * ezcCacheStackConfigurator::configure()} will called by the constructor of
 * {@link ezcCacheStack}. Inside this method, the configuration of the stack
 * can happen as needed. Therefore, the {@link ezcCacheStackableStorage}
 * instances for the stack do not need to exist when the stack is configured in
 * the {@link ezcCacheManager}.
 *
 * The rest of the options is used as usual to affect the behavior of the
 * {@link ezcCacheStack}. However, it is highly recommended to not change
 * $metaStorage and $replacementStrategy once they have been set for a stack.
 * If these options are changed, the whole stack needs to be resetted using
 * {@link ezcCacheStack::reset()}. Aside of that, the previous $metaStorage
 * needs to be resetted.
 *
 * @property string $configurator
 *           Name of a class implementing ezcCacheStackConfigurator. This class
 *           will be used right after construction of the stack, to perform
 *           initial configuration. After the construction process, this option
 *           does not have any effect. Null (default) means no configuration.
 * @property ezcCacheStackMetaDataStorage $metaStorage
 *           This storage will be used to store the meta data of the
 *           replacement strategy used by the stack. If null (default) is
 *           given, the top most storage will be used.
 * @property string $replacementStrategy
 *           The  name of the class given in this property must extend {@link
 *           ezcCacheReplacementStrategy}. The class will be used as the
 *           replacement strategy in the stack. ezcCacheLruReplacementStrategy
 *           is the default.
 * @property bool $bubbleUpOnRestore
 *           This option determines if data that is restored from a storage in
 *           the stack will be bubbled up to higher caches. The default here is
 *           false, since it might significantly reduce the {@link
 *           ezcCacheStack::restore()} performance. In addition, for bubbled up
 *           items, only the attributes will be used that have been provided
 *           while restoring the desired item. Also the lifetime of the item
 *           will practically be reset, since higher storages will start with a
 *           fresh TTL value.
 * @package Cache
 * @version 1.5
 */
class ezcCacheStackOptions extends ezcBaseOptions
{
    /**
     * Construct a new options object.
     * Options are constructed from an option array by default. The constructor
     * automatically passes the given options to the __set() method to set them 
     * in the class.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If trying to access a non existent property.
     * @throws ezcBaseValueException
     *         If the value for a property is out of range.
     * @param array(string=>mixed) $options The initial options to set.
     */
    public function __construct( array $options = array() )
    {
        $this->properties['configurator']        = null;
        $this->properties['metaStorage']         = null;
        $this->properties['replacementStrategy'] = 'ezcCacheStackLruReplacementStrategy';
        $this->properties['bubbleUpOnRestore']   = false;
        parent::__construct( $options );
    }

    /**
     * Sets an option.
     * This method is called when an option is set.
     * 
     * @param string $propertyName  The name of the option to set.
     * @param mixed $propertyValue The option value.
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
            case 'configurator':
                if ( $propertyValue !== null && ( !is_string( $propertyValue ) || ( !class_exists( $propertyValue ) )
                     || !in_array( 'ezcCacheStackConfigurator', class_implements( $propertyValue ) ) ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'existsing class implementing ezcCacheStackConfigurator or null'
                    );
                }
                break;
            case 'metaStorage':
                if ( $propertyValue !== null && !( $propertyValue instanceof ezcCacheStackMetaDataStorage ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcCacheStackMetaDataStorage or null'
                    );
                }
                break;
            case 'replacementStrategy':
                if ( !is_string( $propertyValue ) || !class_exists( $propertyValue ) || !in_array( 'ezcCacheStackReplacementStrategy', class_implements( $propertyValue ) ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'existing class implementing ezcCacheStackReplacementStrategy'
                    );
                }
                break;
            case 'bubbleUpOnRestore':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'bool'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}

?>
