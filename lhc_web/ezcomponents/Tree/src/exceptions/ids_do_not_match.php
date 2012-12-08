<?php
/**
 * File containing the ezcTreeIdsDoNotMatchException class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * Exception that is thrown when a node is added through the ArrayAccess
 * interface with a key that is different from the node's ID.
 *
 * @package Tree
 * @version 1.1.4
 */
class ezcTreeIdsDoNotMatchException extends ezcTreeException
{
    /**
     * Constructs a new ezcTreeIdsDoNotMatchException.
     *
     * @param string $expectedId
     * @param string $actualId
     */
    public function __construct( $expectedId, $actualId )
    {
        parent::__construct( "You cannot add the node with node ID '$expectedId' to the list with key '$actualId'. The key needs to match the node ID." );
    }
}
?>
