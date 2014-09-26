<?php
/**
 * File containing the ezcDbSchemaTypesValidator class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcDbSchemaTypesValidator validates field definition types.
 *
 * @todo implement from an interface
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaTypesValidator
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

        /* For each table we check all field's types. */
        foreach ( $schema->getSchema() as $tableName => $table )
        {
            foreach ( $table->fields as $fieldName => $field )
            {
                if ( !in_array( $field->type, ezcDbSchema::$supportedTypes ) )
                {
                    $errors[] = "Field '$tableName:$fieldName' uses the unsupported type '{$field->type}'.";
                }
            }
        }

        return $errors;
    }
}
?>
