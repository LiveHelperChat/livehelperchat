<?php
/**
 * File containing the ezcDbSchemaDbDiffWriter interface
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * This class provides the interface for database schema difference writers
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
interface ezcDbSchemaDiffDbWriter extends ezcDbSchemaDiffWriter
{
    /**
     * Applies the differences contained in $schemaDiff to the database handler $db
     * 
     * @param ezcDbHandler    $db
     * @param ezcDbSchemaDiff $schemaDiff
     */
    public function applyDiffToDb( ezcDbHandler $db, ezcDbSchemaDiff $schemaDiff );

    /**
     * Returns an array with SQL DDL statements from the differences from $schemaDiff
     *
     * Converts the schema differences contained in $schemaDiff to SQL DDL that
     * can be used to upgrade an existing database to the new version with the
     * differences from $schemaDiff. The SQL queries are returned as an array.
     * 
     * @param ezcDbSchemaDiff $schemaDiff
     * @return array(string)
     */
    public function convertDiffToDDL( ezcDbSchemaDiff $schemaDiff );
}
?>
