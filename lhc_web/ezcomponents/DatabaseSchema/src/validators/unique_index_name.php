<?php
/**
 * File containing the ezcDbSchemaUniqueIndexNameValidator class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcDbSchemaUniqueIndexNameValidator checks for duplicate index names.
 *
 * @todo implement from an interface
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaUniqueIndexNameValidator
{
    /**
     * Validates if all the index names used are unique accross the schema.
     *
     * This method loops over all the indexes in all tables and checks whether
     * they have been used before.
     *
     * @param ezcDbSchema $schema
     * @return array(string)
     */
    static public function validate( ezcDbSchema $schema )
    {
        $indexes = array();
        $errors = array();

        /* For each table we check all auto increment fields. */
        foreach ( $schema->getSchema() as $tableName => $table )
        {
            foreach ( $table->indexes as $indexName => $dummy )
            {
                $indexes[$indexName][] = $tableName;
            }
        }

        foreach ( $indexes as $indexName => $tableList )
        {
            if ( count( $tableList ) > 1 )
            {
                $errors[] = "The index name '$indexName' is not unique. It exists for the tables: '" . join( "', '", $tableList ) . "'.";
            }
        }

        return $errors;
    }
}
?>
