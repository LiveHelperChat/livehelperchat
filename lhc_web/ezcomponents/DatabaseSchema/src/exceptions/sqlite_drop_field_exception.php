<?php
/**
 * File containing the ezcDbSchemaSqliteDropFieldException class
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception that is thrown if SQLite drop field operation failed for some table.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaSqliteDropFieldException extends ezcDbSchemaException
{
    /**
     * Constructs an ezcDbSchemaSqliteDropFieldException 
     *
     * @param string $message reason of fail.
     */
    function __construct( $message )
    {
        parent::__construct( "SQLite handler couldn't drop table field properly. $message" );
    }
}
?>
