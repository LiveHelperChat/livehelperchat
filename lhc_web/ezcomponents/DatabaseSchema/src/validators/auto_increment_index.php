<?php
/**
 * File containing the ezcDbSchemaAutoIncrementIndexValidator class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcDbSchemaAutoIncrementIndexValidator validates field definition types.
 *
 * @todo implement from an interface
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaAutoIncrementIndexValidator
{
    /**
     * Validates if all the types used in the $schema are supported.
     *
     * This method loops over all the fields in a table and checks whether the
     * type that is used for each field is supported. It will return an array
     * containing error strings for each non-supported type that it finds.
     *
     * @param ezcDbSchema $schema
     * @return array(string)
     */
    static public function validate( ezcDbSchema $schema )
    {
        $errors = array();

        /* For each table we check all auto increment fields. */
        foreach ( $schema->getSchema() as $tableName => $table )
        {
            foreach ( $table->fields as $fieldName => $field )
            {
                if ( $field->autoIncrement === true )
                {
                    $found = false;
                    // Loop over de indexes to see if there is a primary
                    foreach ( $table->indexes as $indexName => $index )
                    {
                        if ( $index->primary === true )
                        {
                            $found = true;
                            break;
                        }
                    }

                    if ( !$found )
                    {
                        $errors[] = "Field '$tableName:$fieldName' is auto increment but there is no primary index defined.";
                    }

                }
            }
        }

        return $errors;
    }
}
?>
