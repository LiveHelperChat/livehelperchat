<?php
/**
 * File containing the ezcCacheStackIdAlreadyUsedException.
 * 
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown if an ID is already in use in a stack.
 *
 * @see ezcCacheStack::pushStorage()
 * @package Cache
 * @version 1.5
 */
class ezcCacheStackIdAlreadyUsedException extends ezcCacheException
{
    /**
     * Creates a new ezcCacheStackIdAlreadyUsedException.
     * 
     * @param string $id The ID that is already in use.
     * @return void
     */
    function __construct( $id )
    {
        parent::__construct(
            "The ID '$id' is already used in the stack."
        );
    }
}
?>
