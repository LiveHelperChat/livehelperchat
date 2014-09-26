<?php
/**
 * File containing the ezcDbSchemaComparator class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class provides database comparison functionality.
 *
 * Example that shows how to make a comparison between a file on disk and a
 * database.
 * <code>
 *     $xmlSchema = ezcDbSchema::createFromFile( 'xml', 'wanted-schema.xml' );
 *     $dbSchema = ezcDbSchema::createFromDb( $db );
 *     $diff = ezcDbSchemaComparator::compareSchemas( $xmlSchema, $dbSchema );
 * </code>
 * @see ezcDbSchemaDiff
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @mainclass
 */
class ezcDbSchemaComparator
{
    /**
     * Returns a ezcDbSchemaDiff object containing the differences between the schemas $fromSchema and $toSchema.
     *
     * The returned diferences are returned in such a way that they contain the
     * operations to change the schema stored in $fromSchema to the schema that is
     * stored in $toSchema.
     *
     * @param ezcDbSchema $fromSchema
     * @param ezcDbSchema $toSchema
     *
     * @return ezcDbSchemaDiff
     */
    public static final function compareSchemas( ezcDbSchema $fromSchema, ezcDbSchema $toSchema )
    {
        $diff = new ezcDbSchemaDiff();
        $fromSchema = $fromSchema->getSchema();
        $toSchema = $toSchema->getSchema();

        foreach ( $toSchema as $tableName => $tableDefinition )
        {
            if ( !isset( $fromSchema[$tableName] ) )
            {
                $diff->newTables[$tableName] = $tableDefinition;
            }
            else
            {
                $tableDifferences = ezcDbSchemaComparator::diffTable( $fromSchema[$tableName], $tableDefinition );
                if ( $tableDifferences !== false )
                {
                    $diff->changedTables[$tableName] = $tableDifferences;
                }
            }
        }

        /* Check if there are tables removed */
        foreach ( $fromSchema as $tableName => $tableDefinition )
        {
            if ( !isset( $toSchema[$tableName] ) )
            {
                $diff->removedTables[$tableName] = true;
            }
        }

        return $diff;
    }

    /**
     * Returns the difference between the tables $table1 and $table2.
     *
     * If there are no differences this method returns the boolean false.
     *
     * @param ezcDbSchemaTable $table1
     * @param ezcDbSchemaTable $table2
     *
     * @return bool|ezcDbSchemaTableDiff
     */
    private static final function diffTable( ezcDbSchemaTable $table1, ezcDbSchemaTable $table2 )
    {
        $changes = 0;
        $tableDifferences = new ezcDbSchemaTableDiff();

        /* See if all the fields in table 1 exist in table 2 */
        foreach ( $table2->fields as $fieldName => $fieldDefinition )
        {
            if ( !isset( $table1->fields[$fieldName] ) )
            {
                $tableDifferences->addedFields[$fieldName] = $fieldDefinition;
                $changes++;
            }
        }
        /* See if there are any removed fields in table 2 */
        foreach ( $table1->fields as $fieldName => $fieldDefinition )
        {
            if ( !isset( $table2->fields[$fieldName] ) )
            {
                $tableDifferences->removedFields[$fieldName] = true;
                $changes++;
            }
        }
        /* See if there are any changed fieldDefinitioninitions */
        foreach ( $table1->fields as $fieldName => $fieldDefinition )
        {
            if ( isset( $table2->fields[$fieldName] ) )
            {
                $fieldDifferences = ezcDbSchemaComparator::diffField( $fieldDefinition, $table2->fields[$fieldName] );
                if ( $fieldDifferences )
                {
                    $tableDifferences->changedFields[$fieldName] = $fieldDifferences;
                    $changes++;
                }
            }
        }

        $table1Indexes = $table1->indexes;
        $table2Indexes = $table2->indexes;

        /* See if all the indexes in table 1 exist in table 2 */
        foreach ( $table2Indexes as $indexName => $indexDefinition )
        {
            if ( !isset( $table1Indexes[$indexName] ) )
            {
                $tableDifferences->addedIndexes[$indexName] = $indexDefinition;
                $changes++;
            }
        }
        /* See if there are any removed indexes in table 2 */
        foreach ( $table1Indexes as $indexName => $indexDefinition )
        {
            if ( !isset( $table2Indexes[$indexName] ) )
            {
                $tableDifferences->removedIndexes[$indexName] = true;
                $changes++;
            }
        }
        /* See if there are any changed indexDefinitions */
        foreach ( $table1Indexes as $indexName => $indexDefinition )
        {
            if ( isset( $table2Indexes[$indexName] ) )
            {
                $indexDifferences = ezcDbSchemaComparator::diffIndex( $indexDefinition, $table2Indexes[$indexName] );
                if ( $indexDifferences )
                {
                    $tableDifferences->changedIndexes[$indexName] = $indexDifferences;
                    $changes++;
                }
            }
        }

        return $changes ? $tableDifferences : false;
    }

    /**
     * Returns the difference between the fields $field1 and $field2.
     *
     * If there are differences this method returns $field2, otherwise the
     * boolean false.
     *
     * @param ezcDbSchemaField $field1
     * @param ezcDbSchemaField $field2
     *
     * @return bool|ezcDbSchemaField
     */
    private static final function diffField( ezcDbSchemaField $field1, ezcDbSchemaField $field2 )
    {
        /* Type is always available */
        if ( $field1->type != $field2->type )
        {
            return $field2;
        }

        $testFields = array( 'type', 'length', 'notNull', 'default', 'autoIncrement' );
        foreach ( $testFields as $property )
        {
            if ( $field1->$property !== $field2->$property )
            {
                return $field2;
            }
        }

        return false;
    }

    /**
     * Finds the difference between the indexes $index1 and $index2.
     *
     * Compares $index1 with $index2 and returns $index2 if there are any
     * differences or false in case there are no differences.
     *
     * @param ezcDbSchemaIndex $index1
     * @param ezcDbSchemaIndex $index2
     * @return bool|ezcDbSchemaIndex
     */
    private static final function diffIndex( ezcDbSchemaIndex $index1, ezcDbSchemaIndex $index2 )
    {
        $testFields = array( 'primary', 'unique' );
        foreach ( $testFields as $property )
        {
            if ( $index1->$property !== $index2->$property )
            {
                return $index2;
            }
        }

        // Check for removed index fields in $index2
        foreach ( $index1->indexFields as $indexFieldName => $indexFieldDefinition )
        {
            if ( !isset( $index2->indexFields[$indexFieldName] ) )
            {
                return $index2;
            }
        }

        // Check for new index fields in $index2
        foreach ( $index2->indexFields as $indexFieldName => $indexFieldDefinition )
        {
            if ( !isset( $index1->indexFields[$indexFieldName] ) )
            {
                return $index2;
            }
        }

        $testFields = array( 'sorting' );
        foreach ( $index1->indexFields as $indexFieldName => $indexFieldDefinition )
        {
            foreach ( $testFields as $testField )
            {
                if ( $indexFieldDefinition->$testField != $index2->indexFields[$indexFieldName]->$testField )
                {
                    return $index2;
                }
            }
        }
        return false;
    }
}
?>
