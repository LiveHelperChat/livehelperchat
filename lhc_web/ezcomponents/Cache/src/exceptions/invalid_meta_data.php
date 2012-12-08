<?php
/**
 * File containing the ezcCacheInvalidMetaDataException
 * 
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Thrown if an {@link ezcCacheStackReplacementStrategy} could not process the
 * given {@link ezcCacheStackMetaData}.
 *
 * @see ezcCacheStackReplacementStrategy::store()
 * @see ezcCacheStackReplacementStrategy::restore()
 * @see ezcCacheStackReplacementStrategy::delete()
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheInvalidMetaDataException extends ezcCacheException
{
    /**
     * Creates a new ezcCacheInvalidMetaDataException.
     * 
     * @param ezcCacheStackMetaData $metaData 
     * @param string $class Expected class of $metaData.
     */
    function __construct( ezcCacheStackMetaData $metaData, $class )
    {
        parent::__construct(
            "The given meta data of class '" . get_class( $metaData )
            . "'could not be handled by the replacement strategy. Expected: '$class'."
        );
    }
}
?>
