<?php
/**
 * File containing the ezcCacheStorageFileOptions class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Option class for the ezcCacheStorageFile class.
 * Instances of this class store the option of ezcCacheStorageFile implementations.
 *
 * @property int $ttl
 *           The time to live of cache entries.
 * @property string $extension
 *           The (file) extension to use for the storage items.
 * @property int $permissions
 *           Permissions to create a file with (Posix only).
 * @property string $lockFile
 *           The name of the file used for locking in the lock() method.
 *           Default is '.ezcLock'.
 * @property int $lockWaitTime
 *           Time to wait between lock availability checks. Measured in
 *           microseconds ({@link usleep()}). Default is 200000.
 * @property int $maxLockTime
 *           Time before a lock is considered dead, measured in seconds.
 *           Default is 5.
 * @property string $metaDataFile
 *           The name of the file used to store meta data. Default is
 *           '.ezcMetaData'.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheStorageFileOptions extends ezcBaseOptions
{
    /**
     * Parent storage options. 
     * 
     * @var ezcCacheStorageOptions
     */
    protected $storageOptions;

    /**
     * Constructs a new options class.
     *
     * It also sets the default values of the format property
     *
     * @param array(string=>mixed) $options The initial options to set.
     
     * @throws ezcBasePropertyNotFoundException
     *         If trying to assign a property which does not exist
     * @throws ezcBaseValueException
     *         If the value for the property is incorrect
     */
    public function __construct( $options = array() )
    {
        $this->properties['permissions']  = 0644;
        $this->properties['lockFile']     = '.ezcLock';
        $this->properties['lockWaitTime'] = 200000;
        $this->properties['maxLockTime']  = 5;
        $this->properties['lockFile']     = '.ezcLock';
        $this->properties['metaDataFile'] = '.ezcMetaData';
        $this->storageOptions = new ezcCacheStorageOptions();
        parent::__construct( $options );
    }

    /**
     * Sets an option.
     * This method is called when an option is set.
     * 
     * @param string $key  The option name.
     * @param mixed $value The option value.
     * @ignore
     */
    public function __set( $key, $value )
    {
        switch ( $key )
        {
            case "permissions":
                if ( !is_int( $value )  || $value < 0 || $value > 0777 )
                {
                    throw new ezcBaseValueException( $key, $value, "int > 0 and <= 0777" );
                }
                break;
            case "lockFile":
                if ( !is_string( $value )  || strlen( $value ) < 1 || strlen( $value ) > 250 )
                {
                    throw new ezcBaseValueException(
                        $key,
                        $value,
                        'string, length > 0 and < 250'
                    );
                }
                break;
            case "lockWaitTime":
                if ( !is_int( $value )  || $value < 1 )
                {
                    throw new ezcBaseValueException(
                        $key,
                        $value,
                        'int > 0'
                    );
                }
                break;
            case "maxLockTime":
                if ( !is_int( $value )  || $value < 1 )
                {
                    throw new ezcBaseValueException(
                        $key,
                        $value,
                        'int > 0'
                    );
                }
                break;
            case "metaDataFile":
                if ( !is_string( $value )  || strlen( $value ) < 1 || strlen( $value ) > 250 )
                {
                    throw new ezcBaseValueException(
                        $key,
                        $value,
                        'string, length > 0 and < 250'
                    );
                }
                break;
            // Delegate
            default:
                $this->storageOptions->$key = $value;
                return;
        }
        $this->properties[$key] = $value;
    }

    /**
     * Property get access.
     * Simply returns a given option.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If trying to assign a property which does not exist
     * @param string $key The name of the option to get.
     * @return mixed The option value.
     * @ignore
     */
    public function __get( $key )
    {
        if ( isset( $this->properties[$key] ) )
        {
            return $this->properties[$key];
        }
        // Delegate
        return $this->storageOptions->$key;
    }

    /**
     * Returns if a option exists.
     * 
     * @param string $key Option name to check for.
     * @return void
     * @ignore
     */
    public function __isset( $key )
    {
        // Delegate
        return ( array_key_exists( $key, $this->properties ) || isset( $this->storageOptions->$key ) );
    }

    /**
     * Merge an ezcCacheStorageOptions object into this object.
     * 
     * @param ezcCacheStorageOptions $options The options to merge.
     * @return void
     * @ignore
     */
    public function mergeStorageOptions( ezcCacheStorageOptions $options )
    {
        $this->storageOptions = $options;
    }
}

?>
