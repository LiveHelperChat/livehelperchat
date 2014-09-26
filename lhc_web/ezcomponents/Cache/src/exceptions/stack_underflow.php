<?php
/**
 * File containing the ezcCacheStackUnderflowException
 * 
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Thrown if popStorage() is called on an empty stack.
 * 
 * @see ezcCacheStack::popStorage()
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheStackUnderflowException extends ezcCacheException
{
    /**
     * Creates a new ezcCacheStackUnderflowException.
     * 
     * @param mixed $actualType    Type of data received.
     * @param array $expectedTypes Expected data types.
     * @return void
     */
    function __construct()
    {
        parent::__construct( "No storages in stack." );
    }
}
?>
