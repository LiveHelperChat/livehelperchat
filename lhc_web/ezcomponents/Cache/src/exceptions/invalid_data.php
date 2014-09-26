<?php
/**
 * File containing the ezcCacheInvalidDataException
 * 
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Thrown if the data to be stored in a cache can not be handled by the storage.
 * Most {@link ezcCacheStorage} implementations are only capable of storing 
 * scalar and array values, so this exception will be thrown when an incompatible
 * type is submitted for storage, like object or resource.
 * 
 * {@link ezcCacheStorage::store()}
 * {@link ezcCacheStorageFile::store()}
 *
 * {@link ezcCacheStorageFileArray::prepareData()}
 * {@link ezcCacheStorageFileEvalArray::prepareData()}
 * {@link ezcCacheStorageFilePlain::prepareData()}
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheInvalidDataException extends ezcCacheException
{
    /**
     * Creates a new ezcCacheInvalidDataException.
     * 
     * @param mixed $actualType    Type of data received.
     * @param array $expectedTypes Expected data types.
     * @return void
     */
    function __construct( $actualType, array $expectedTypes )
    {
        parent::__construct( "The given data was of type '{$actualType}', which can not be stored. Expecting: '" . implode( ', ', $expectedTypes ) . "'." );
    }
}
?>
