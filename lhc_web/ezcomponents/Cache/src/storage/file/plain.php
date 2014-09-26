<?php
/**
 * File containing the ezcCacheStoragePlain class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * This class implements a simple storage to cache plain text 
 * on the filesystem. It takes its base methods from the extended 
 * storage base class {@link ezcCacheStorageFile}.
 *
 * In contrast to other {@link ezcCacheStorageFile} implementations, the stored
 * cache data is restored using PHP's file_get_contents() class. This cache
 * is not capable to store array values. If numeric values are stored the 
 * restored values will be of type string. The same applies to values of the 
 * simple type bool. It is highly recommended that you cast the resulting
 * value to its correct type, also PHP will automatically perform this cast
 * when necessary. An explicit cast ensures that type consistent comparisons
 * (using the === or !== operators) will not fail on restored cache data.
 *
 * An even better solution, if you want to store non-string values, are the
 * usage of {@link ezcCacheStorageFileArray} and 
 * {@link ezcCacheStorageFileEvalArray} storage classes, since those keep the
 * variable types of you cached data consistent.
 *
 * For example code of using a cache storage, see {@link ezcCacheManager}.
 *
 * The Cache package contains several other implementations of 
 * {@link ezcCacheStorageFile}. As there are:
 *
 * - ezcCacheStorageFileArray
 * - ezcCacheStorageFilePlain
 * 
 * @package Cache
 * @version 1.5
 */
class ezcCacheStorageFilePlain extends ezcCacheStorageFile
{
    /**
     * Fetch data from the cache.
     * This method does the fetching of the data itself. In this case, the
     * method simply includes the file and returns the value returned by the
     * include (or false on failure).
     * 
     * @param string $filename The file to fetch data from.
     * @return string The fetched data or false on failure.
     */
    protected function fetchData( $filename )
    {
        return file_get_contents( $filename );
    }
    
    /**
     * Serialize the data for storing.
     * Serializes a PHP variable (except type resource and object) to a
     * executable PHP code representation string.
     * 
     * @param mixed $data Simple type or array
     * @return string The serialized data
     *
     * @throws ezcCacheInvalidDataException
     *         If the data submitted is an array,object or a resource, since 
     *         this implementation of {@link ezcCacheStorageFile} can only deal 
     *         with scalar values.
     */
    protected function prepareData( $data )
    {
        if ( is_scalar( $data ) === false ) 
        {
            throw new ezcCacheInvalidDataException( gettype( $data ), array( 'simple' ) );
        }
        return ( string ) $data;
    }

    /**
     * Restores and returns the meta data struct.
     *
     * This method fetches the meta data stored in the storage and returns the
     * according struct of type {@link ezcCacheStackMetaData}. The meta data
     * must be stored inside the storage, but should not be visible as normal
     * cache items to the user.
     * 
     * @return ezcCacheStackMetaData
     */
    public function restoreMetaData()
    {
        // Silence require warnings. It's ok that meta data does not exist.
        $dataStr = @$this->fetchData(
            $this->properties['location'] . $this->properties['options']->metaDataFile
        );
        $dataArr = unserialize( $dataStr );


        $result = null;
        if ( $dataArr !== false )
        {
            $result = new $dataArr['class']();
            $result->setState( $dataArr['data'] );
        }
        return $result;
    }

    /**
     * Stores the given meta data struct.
     *
     * This method stores the given $metaData inside the storage. The data must
     * be stored with the same mechanism that the storage itself uses. However,
     * it should not be stored as a normal cache item, if possible, to avoid
     * accedental user manipulation.
     *
     * @param ezcCacheStackMetaData $metaData 
     * @return void
     */
    public function storeMetaData( ezcCacheStackMetaData $metaData )
    {
        $dataArr = array(
            'class' => get_class( $metaData ),
            'data'  => $metaData->getState(),
        );
        // This storage only handles scalar values, so we serialize here.
        $dataStr = serialize( $dataArr );
        $this->storeRawData(
            $this->properties['location'] . $this->properties['options']->metaDataFile,
            $this->prepareData( $dataStr )
        );
    }
}
?>
