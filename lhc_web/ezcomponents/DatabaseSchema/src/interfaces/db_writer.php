<?php
/**
 * File containing the ezcDbSchemaDbWriter interface
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * This class provides the interface for database schema writers
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
interface ezcDbSchemaDbWriter extends ezcDbSchemaWriter
{
    /**
     * Creates the tables contained in $schema in the database that is related to $db
     *
     * This method takes the table definitions from $schema and will create the
     * tables according to this definition in the database that is references
     * by the $db handler. If tables with the same name as contained in the
     * definitions already exist they will be removed and recreated with the
     * new definition.
     *
     * @param ezcDbHandler $db
     * @param ezcDbSchema  $dbSchema
     */
    public function saveToDb( ezcDbHandler $db, ezcDbSchema $dbSchema );

    /**
     * Returns an array with SQL DDL statements that creates the database definition in $dbSchema
     *
     * Converts the schema definition contained in $dbSchema to DDL SQL. This
     * SQL can be used to create tables in an existing database according to
     * the definition.  The SQL queries are returned as an array.
     * 
     * @param ezcDbSchema $dbSchema
     * @return array(string)
     */
    public function convertToDDL( ezcDbSchema $dbSchema );

    /**
     * Checks if the query is allowed.
     *
     * Perform testing if table exist for DROP TABLE query 
     * to avoid stoping execution while try to drop not existent table. 
     * 
     * @param ezcDbHandler $db
     * @param string       $query
     * 
     * @return boolean
     */
    public function isQueryAllowed( ezcDbHandler $db, $query );
}
?>
