<?php
/**
 * File containing the ezcCacheStackStorageUsedTwiceException.
 * 
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown if a storage is used twice in a stack.
 *
 * @see ezcCacheStack::pushStorage()
 * @package Cache
 * @version 1.5
 */
class ezcCacheStackStorageUsedTwiceException extends ezcCacheException
{
    /**
     * Creates a new ezcCacheStackStorageUsedTwiceException.
     *
     * The $storage is the object that is used twice in the {@link
     * ezcCacheStack}.
     * 
     * @param ezcCacheStackableStorage $storage
     * @return void
     */
    function __construct( ezcCacheStackableStorage $storage )
    {
        parent::__construct(
            "The storage of type '"
            . get_class( $storage ) 
            . "' with location '"
            . $storage->location 
            . "' is already used in the stack."
        );
    }
}
?>
