<?php
/**
 * File containing the ezcCacheStorageFileApcArray class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This class is a replacement for the {@link ezcCacheStorageFileArray} class. Tries
 * to serve data from a local APC cache if possible.
 *
 * Options for this class are defined in {@link ezcCacheStorageFileApcArrayOptions}.
 *
 * @apichange This class might be removed in future versions. Please use
 *            {@link ezcCacheStack} to achieve the desired behaior.
 * @package Cache
 * @version 1.5
 */
class ezcCacheStorageFileApcArray extends ezcCacheStorageApc
{
    /**
     * Creates a new cache storage in the given location. The location in case
     * of this storage class must a valid file system directory.
     *
     * Options can contain the 'ttl' (Time-To-Live). This is per default set
     * to 1 day. The option 'permissions' can be used to define the file
     * permissions of created cache items.
     *
     * For details about the options see {@link ezcCacheStorageFileApcArrayOptions}.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If you tried to set a non-existent option value.
     *
     * @param string $location Path to the cache location. Must be a valid path
     * @param array(string=>string) $options Options for the cache storage
     */
    public function __construct( $location, array $options = array() )
    {
        parent::__construct( $location, array() );

        // Overwrite parent set options with new ezcCacheStorageFileApcArrayOptions
        $this->properties['options'] = new ezcCacheStorageFileApcArrayOptions( $options );
    }

    /**
     * Fetches the data from the cache.
     * 
     * @param string $filename The ID/filename from where to fetch the object
     * @param bool $useApc Use APC or the file system
     * @return mixed The fetched data or false on failure
     */
    protected function fetchData( $filename, $useApc = false )
    {
        if ( $useApc === true )
        {
            $data = $this->backend->fetch( $filename );
            return ( is_object( $data ) ) ? $data->data : false;
        }
        else
        {
            return ( include $filename );
        }
    }

    /**
     * Fetches the object from the cache.
     *
     * @param string $filename The ID/filename from where to fetch the data
     * @return mixed The fetched object or false on failure
     */
    protected function fetchObject( $filename )
    {
        $data = $this->backend->fetch( $filename );
        return ( is_object( $data ) ) ? $data : false;
    }

    /**
     * Wraps the data in order to be stored in APC ($useApc = true) or on the
     * file system ($useApc = false).
     *
     * @throws ezcCacheInvalidDataException
     *         If the data submitted can not be handled by this storage (object,
     *         resource).
     *
     * @param mixed $data Simple type or array
     * @param bool $useApc Use APC or not
     * @return mixed Prepared data
     */
    protected function prepareData( $data, $useApc = false )
    {
        if ( $useApc === true )
        {
            if ( is_resource( $data ) )
            {
                throw new ezcCacheInvalidDataException( gettype( $data ), array( 'simple', 'array', 'object' ) );
            }
            return new ezcCacheStorageFileApcArrayDataStruct( $data, $this->properties['location'] );
        }
        else
        {
            if ( is_object( $data )
                 || is_resource( $data ) )
            {
                throw new ezcCacheInvalidDataException( gettype( $data ), array( 'simple', 'array' ) );
            }
            return "<?php\nreturn " . var_export( $data, true ) . ";\n?>\n";
        }
    }

    /**
     * Stores data to the cache storage.
     *
     * @throws ezcBaseFilePermissionException
     *         If the directory to store the cache file could not be created.
     *         This exception means most likely that your cache directory
     *         has been corrupted by external influences (file permission
     *         change).
     * @throws ezcBaseFileIoException
     *         If an error occured while writing the data to the cache. If this
     *         exception occurs, a serious error occured and your storage might
     *         be corruped (e.g. broken network connection, file system broken,
     *         ...).
     * @throws ezcCacheInvalidDataException
     *         If the data submitted can not be handled by the implementation
     *         of {@link ezcCacheStorageFile}. Most implementations can not
     *         handle objects and resources.
     * @throws ezcCacheApcException
     *         If the data could not be stored in APC.
     *
     * @param string $id Unique identifier
     * @param mixed $data The data to store
     * @param array(string=>string) $attributes Attributes describing the cached data
     * @return string The ID string of the newly cached data
     */
    public function store( $id, $data, $attributes = array() )
    {
        // Generates the identifier
        $filename = $this->properties['location'] . $this->generateIdentifier( $id, $attributes );

        // Purges the Registry Cache
        if ( isset( $this->registry[$filename] ) )
        {
            unset( $this->registry[$filename] );
        }

        // Deletes the files if it already exists on the filesystem
        if ( file_exists( $filename ) )
        {
            if ( unlink( $filename ) === false )
            {
                throw new ezcBaseFilePermissionException( $filename, ezcBaseFileException::WRITE, 'Could not delete existing cache file.' );
            }
        }

        // Deletes the data from APC if it already exists
        $this->backend->delete( $filename );

        // Prepares the data for filesystem storage
        $dataStr = $this->prepareData( $data );

        // Tries to create the directory on the filesystem
        $dirname = dirname( $filename );
        if ( !is_dir( $dirname )
             && !mkdir( $dirname, 0777, true ) )
        {
            throw new ezcBaseFilePermissionException( $dirname, ezcBaseFileException::WRITE, 'Could not create directory to store cache file.' );
        }

        // Tries to write the file the filesystem
        if ( @file_put_contents( $filename, $dataStr ) !== strlen( $dataStr ) )
        {
            throw new ezcBaseFileIoException( $filename, ezcBaseFileException::WRITE, 'Could not write data to cache file.' );
        }

        // Tries to set the file permissions
        if ( ezcBaseFeatures::os() !== "Windows" )
        {
            chmod( $filename, $this->options->permissions );
        }

        // Prepares the data for APC storage
        $dataObj = $this->prepareData( $data, true );
        $dataObj->mtime = @filemtime( $filename );
        $dataObj->atime = time();

        // Stores it in APC
        $this->registerIdentifier( $id, $attributes, $filename );
        if ( !$this->backend->store( $filename, $dataObj, $this->properties['options']['ttl'] ) )
        {
            throw new ezcCacheApcException( "APC store failed." );
        }

        // Returns the ID for no good reason
        return $id;
    }

    /**
     * Restores the data from the cache.
     *
     * @param string $id The item ID to restore
     * @param array(string=>string) $attributes Attributes describing the data to restore
     * @param bool $search Whether to search for items if not found directly
     * @return mixed The cached data on success, otherwise false
     */
    public function restore( $id, $attributes = array(), $search = false )
    {
        // Generates the identifier
        $filename = $this->properties['location'] . $this->generateIdentifier( $id, $attributes );

        // Grabs the data object from the APC
        $dataObj = $this->fetchObject( $filename );
        $useApc = false;

        // Checks the APC object exists
        if ( $dataObj !== false
             && is_object( $dataObj )
             && isset( $dataObj->atime ) )
        {
            $useApc = true;
        }

        // Checks the APC object is still valid
        if ( !isset( $this->registry[$filename] )
             && $useApc === true
             && time() === $dataObj->atime )
        {
            // Make sure the FileSystem still has the file and that it hasn't changed
            if ( file_exists( $filename ) !== false
                 && @filemtime( $filename ) === $dataObj->mtime )
            {
                $dataObj->atime = time();
                $this->backend->store( $filename, $dataObj, $this->properties['options']['ttl'] );
            }
            else
            {
                $useApc = false;
                $this->backend->delete( $filename );
            }
        }

        // Searches the filesystem for the file
        if ( !isset( $this->registry[$filename] )
             && $useApc === false
             && file_exists( $filename ) === false )
        {
            if ( $search === true
                 && count( $files = $this->search( $id, $attributes ) ) === 1 )
            {
                $filename = $files[0][2];
            }
            else
            {
                // There are more elements found during search, so false is returned
                return false;
            }
        }

        // Returns false if no data is stored anywhere
        if ( $useApc === false
             && file_exists( $filename ) === false )
        {
            // Purges APC
            $this->backend->delete( $filename );

            // Purges Registry Cache
            if ( isset( $this->registry[$filename] ) )
            {
                unset( $this->registry[$filename] );
            }

            return false;
        }

        // Creates a Registry Object -- should only happen once per page load
        if ( !isset( $this->registry[$filename] ) )
        {
            $this->registry[$filename] = new stdClass();
            $this->registry[$filename]->data = false;
            $this->registry[$filename]->mtime = $useApc ? $dataObj->mtime : null;
            $this->registry[$filename]->lifetime = $this->calcLifetime( $filename, $useApc );
        }

        // Purges the data if it is expired
        if ( $this->properties['options']['ttl'] !== false
             && $this->calcLifetime( $filename, $useApc ) > $this->properties['options']['ttl'] )
        {
            $this->delete( $id, $attributes, false ); // don't search
            return false;
        }

        // Returns the data from the Registry Cache
        if ( $this->registry[$filename]->data !== false )
        {
            return ( $this->registry[$filename]->data );
        }

        // Returns data from APC
        else if ( $useApc === true
                  && ( isset( $dataObj->data ) || is_null( $dataObj->data ) ) )
        {
            $this->registry[$filename]->data = $dataObj->data; // primes the Registry cache
            return ( $dataObj->data );
        }

        // Returns data from the filesystem
        else if ( file_exists( $filename ) !== false )
        {
            // Grabs the data from the filesystem
            $dataStr = $this->fetchData( $filename );

            // Stores it in the Registry Cache
            $this->registry[$filename]->data = $dataStr;

            // Prepares the data for APC storage
            $dataObj = $this->prepareData( $dataStr, true );
            $dataObj->mtime = @filemtime( $filename );
            $dataObj->atime = time();

            // Stores it in APC
            $this->backend->store( $filename, $dataObj, $this->properties['options']['ttl'] );

            // Returns the data
            return ( $dataStr );
        }
        else
        {
            return false;
        }
    }

    /**
     * Deletes the data associated with $id or $attributes from the cache.
     *
     * @throws ezcBaseFilePermissionException
     *         If an already existsing cache file could not be unlinked.
     *         This exception means most likely that your cache directory
     *         has been corrupted by external influences (file permission
     *         change).
     *
     * @param string $id The item ID to purge
     * @param array(string=>string) $attributes Attributes describing the data to restore
     * @param bool $search Whether to search for items if not found directly
     */
    public function delete( $id = null, $attributes = array(), $search = false )
    {
        $location = $this->properties['location'];
        // Generates the identifier
        $filename = $location . $this->generateIdentifier( $id, $attributes );

        // Initializes the array
        $delFiles = array();

        clearstatcache();

        // Checks if the file exists on the filesystem
        if ( file_exists( $filename ) )
        {
            $delFiles[] = array( $id, $attributes, $filename );
        }
        else if ( $search === true )
        {
            $delFiles = $this->search( $id, $attributes );
        }

        $deletedIds = array();
        // Deletes the files
        foreach ( $delFiles as $count => $filename )
        {
            // Deletes from Registry Cache
            if ( isset( $this->registry[$filename[2]] ) )
            {
                unset( $this->registry[$filename[2]] );
            }

            // Deletes from APC
            $this->backend->delete( $filename[2] );
            $this->unRegisterIdentifier( $filename[0], $filename[1], $filename[2], true );
            if ( isset( $this->registry[$location][$filename[0]][$filename[2]] ) )
            {
                unset( $this->registry[$location][$filename[0]][$filename[2]] );
            }

            // Deletes from the filesystem
            if ( @unlink( $filename[2] ) === false )
            {
                throw new ezcBaseFilePermissionException(
                    $filename,
                    ezcBaseFileException::WRITE,
                    'Could not unlink cache file.'
                );
            }
            $deletedIds[] = $filename[0];
        }
        $this->storeSearchRegistry();
        return $deletedIds;
    }

    /**
     * Checks the path in the location property exists, and is read-/writable. It
     * throws an exception if not.
     *
     * @throws ezcBaseFileNotFoundException
     *         If the storage location does not exist. This should usually not 
     *         happen, since {@link ezcCacheManager::createCache()} already
     *         performs sanity checks for the cache location. In case this 
     *         exception is thrown, your cache location has been corrupted 
     *         after the cache was configured.
     * @throws ezcBaseFileNotFoundException
     *         If the storage location is not a directory. This should usually 
     *         not happen, since {@link ezcCacheManager::createCache()} already
     *         performs sanity checks for the cache location. In case this 
     *         exception is thrown, your cache location has been corrupted 
     *         after the cache was configured.
     * @throws ezcBaseFilePermissionException
     *         If the storage location is not writeable. This should usually not 
     *         happen, since {@link ezcCacheManager::createCache()} already
     *         performs sanity checks for the cache location. In case this 
     *         exception is thrown, your cache location has been corrupted 
     *         after the cache was configured.
     */
    protected function validateLocation()
    {
        if ( file_exists( $this->properties['location'] ) === false )
        {
            throw new ezcBaseFileNotFoundException( $this->properties['location'], 'cache location' );
        }

        if ( is_dir( $this->properties['location'] ) === false ) 
        {
            throw new ezcBaseFileNotFoundException( $this->properties['location'], 'cache location', 'Cache location not a directory.' );
        }

        if ( is_writeable( $this->properties['location'] ) === false ) 
        {
            throw new ezcBaseFilePermissionException( $this->properties['location'], ezcBaseFileException::WRITE, 'Cache location is not a directory.' );
        }
    }

    /**
     * Calculates the lifetime remaining for a cache object.
     *
     * If the TTL option is set to false, this method will always return 1 for
     * existing items.
     *
     * @param string $filename The file to calculate the remaining lifetime for
     * @param bool $useApc Use APC or not
     * @return int The remaining lifetime in seconds (0 if no time remaining)
     */
    protected function calcLifetime( $filename, $useApc = false )
    {
        $ttl = $this->options->ttl;
        // Calculate when the APC object was created
        if ( $useApc === true )
        {
            // we've likely already looked this thing up in APC, so we'll grab the local object
            if ( isset( $this->registry[$filename] ) )
            {
                $dataObj = $this->registry[$filename];
            }
            else // otherwise we'll grab it from APC
            {
                $dataObj = $this->fetchObject( $filename );
            }

            if ( is_object( $dataObj ) )
            {
                if ( $ttl === false )
                {
                    return 1;
                }
                return (
                    ( $lifeTime = ( time() - $dataObj->mtime ) ) > $ttl
                    ? $ttl - $lifeTime 
                    : 0
                );
            }
            else
            {
                return 0;
            }
        }

        // Calculate when the filesystem file was created
        else
        {
            if ( ( file_exists( $filename ) !== false )
                 && ( ( $modTime = @filemtime( $filename ) ) !== false ) )
            {
                if ( $ttl === false )
                {
                    return 1;
                }
                return (
                    ( $lifeTime = ( time() - $modTime ) ) < $ttl
                    ? $ttl - $lifeTime 
                    : 0
                );
            }
            else
            {
                return 0;
            }
        }
    }
}
?>
