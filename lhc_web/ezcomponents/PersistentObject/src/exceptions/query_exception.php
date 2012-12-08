<?php
/**
 * File containing the ezcPersistentQueryException class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown when a query failed internally in Persistent Object.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentQueryException extends ezcPersistentObjectException
{

    /**
     * Constructs a new ezcPersistentQueryException with additional information in $msg.
     *
     * You can also provide the query for debugging purposes.
     *
     * @param string $msg
     * @param string $query
     * @return void
     */
    public function __construct( $msg, $query = null )
    {
        parent::__construct(
            "A query failed internally in Persistent Object: {$msg}"
            . ( $query !== null ? " Query: '{$query->getQuery()}'" : "" )
        );
    }
}
?>
