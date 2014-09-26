<?php
/**
 * File containing the ezcDbSchemaCommonSqlReader class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An abstract class that implements some common functionality required by
 * multiple database backends.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
abstract class ezcDbSchemaCommonSqlReader implements ezcDbSchemaDbReader
{
    /**
     * Returns an ezcDbSchema created from the database schema in the database referenced by $db
     *
     * This method analyses the current database referenced by $db and creates
     * a schema definition out of this. This schema definition is returned as
     * an (@link ezcDbSchema) object.
     *
     * @param ezcDbHandler $db
     * @return ezcDbSchema
     */
    public function loadFromDb( ezcDbHandler $db )
    {
        $this->db = $db;
        return new ezcDbSchema( $this->fetchSchema() );
    }

    /**
     * Returns what type of schema reader this class implements.
     *
     * This method always returns ezcDbSchema::DATABASE
     *
     * @return int
     */
    public function getReaderType()
    {
        return ezcDbSchema::DATABASE;
    }

    /**
     * Loops over all the table names in the array and extracts schema
     * information.
     *
     * This method extracts information about a database's schema from the
     * database itself and returns this schema as an ezcDbSchema object.
     *
     * @param array(string) $tables
     * @return ezcDbSchema
     */
    protected function processSchema( array $tables )
    {
        $schemaDefinition = array();
        array_walk( $tables, create_function( '&$item,$key', '$item = $item[0];' ) );

        // strip out the prefix and only return tables with the prefix set.
        $prefix = ezcDbSchema::$options->tableNamePrefix;

        foreach ( $tables as $tableName )
        {
            $tableNameWithoutPrefix = substr( $tableName, strlen( $prefix ) );
            // Process table if there was no prefix, or when a prefix was
            // found. In the latter case the prefix would be missing from
            // $tableNameWithoutPrefix due to the substr() above, and hence,
            // $tableName and $tableNameWithoutPrefix would be different.
            if ( $prefix === '' || $tableName !== $tableNameWithoutPrefix )
            {
                $fields  = $this->fetchTableFields( $tableName );
                $indexes = $this->fetchTableIndexes( $tableName );

                $schemaDefinition[$tableNameWithoutPrefix] = ezcDbSchema::createNewTable( $fields, $indexes );
            }
        }

        return $schemaDefinition;
    }

}
?>
