<?php
/**
 * File containing the ezcDbSchemaXmlWriter class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler that stores database definitions and database difference definitions to a file in an XML format.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaXmlWriter implements ezcDbSchemaFileWriter, ezcDbSchemaDiffFileWriter
{
    /**
     * Stores the XMLWriter object
     *
     * @var XMLWriter
     */
    private $writer;
    
    /**
     * Returns what type of schema writer this class implements.
     *
     * This method always returns ezcDbSchema::FILE
     *
     * @return int
     */
    public function getWriterType()
    {
        return ezcDbSchema::FILE;
    }

    /**
     * Returns what type of schema difference writer this class implements.
     *
     * This method always returns ezcDbSchema::FILE
     *
     * @return int
     */
    public function getDiffWriterType()
    {
        return ezcDbSchema::FILE;
    }

    /**
     * Uses the $writer object to write the field definition $field for $fieldName
     *
     * @param string           $fieldName
     * @param ezcDbSchemaField $field
     */
    private function writeField( $fieldName, ezcDbSchemaField $field )
    {
        $this->writer->startElement( 'name' );
        $this->writer->text( $fieldName );
        $this->writer->endElement();

        $this->writer->startElement( 'type' );
        $this->writer->text( $field->type );
        $this->writer->endElement();

        if ( $field->length )
        {
            $this->writer->startElement( 'length' );
            $this->writer->text( $field->length );
            $this->writer->endElement();
        }

        if ( $field->autoIncrement )
        {
            $this->writer->startElement( 'autoincrement' );
            $this->writer->text( 'true' );
            $this->writer->endElement();
        }

        if ( $field->notNull )
        {
            $this->writer->startElement( 'notnull' );
            $this->writer->text( 'true' );
            $this->writer->endElement();
        }

        if ( !is_null( $field->default ) )
        {
            $this->writer->startElement( 'default' );
            $this->writer->text( $field->default );
            $this->writer->endElement();
        }
    }

    /**
     * Uses the $writer object to write the index field definition $field for $fieldName
     *
     * @param string                $fieldName
     * @param ezcDbSchemaIndexField $field
     */
    private function writeIndexField( $fieldName, ezcDbSchemaIndexField $field )
    {
        $this->writer->startElement( 'name' );
        $this->writer->text( $fieldName );
        $this->writer->endElement();

        if ( !is_null( $field->sorting ) )
        {
            $this->writer->startElement( 'sorting' );
            $this->writer->text( $field->sorting ? 'ascending' : 'descending' );
            $this->writer->endElement();
        }
    }

    /**
     * Uses the $writer object to write the index definition $index for $indexName
     *
     * @param string           $indexName
     * @param ezcDbSchemaIndex $index
     */
    private function writeIndex( $indexName, ezcDbSchemaIndex $index )
    {
        $this->writer->startElement( 'name' );
        $this->writer->text( $indexName );
        $this->writer->endElement();

        if ( $index->primary )
        {
            $this->writer->startElement( 'primary' );
            $this->writer->text( 'true' );
            $this->writer->endElement();
        }

        if ( $index->unique )
        {
            $this->writer->startElement( 'unique' );
            $this->writer->text( 'true' );
            $this->writer->endElement();
        }
        $this->writer->flush();

        foreach ( $index->indexFields as $fieldName => $field )
        {
            $this->writer->startElement( 'field' );
            $this->writeIndexField( $fieldName, $field );

            $this->writer->endElement();
            $this->writer->flush();
        }
    }

    /**
     * Uses the $writer object to write the table definition $table for $tableName
     *
     * @param string           $tableName
     * @param ezcDbSchemaTable $table
     */
    private function writeTable( $tableName, ezcDbSchemaTable $table )
    {
        $this->writer->startElement( 'table' );
        $this->writer->startElement( 'name' );
        $this->writer->text( $tableName );
        $this->writer->endElement();
        $this->writer->flush();

        $this->writer->startElement( 'declaration' );
        $this->writer->flush();

        // fields 
        foreach ( $table->fields as $fieldName => $field )
        {
            $this->writer->startElement( 'field' );
            $this->writeField( $fieldName, $field );

            $this->writer->endElement();
            $this->writer->flush();
        }

        // indices
        foreach ( $table->indexes as $indexName => $index )
        {
            $this->writer->startElement( 'index' );
            $this->writeIndex( $indexName, $index );

            $this->writer->endElement();
            $this->writer->flush();
        }
        $this->writer->endElement();
        $this->writer->flush();

        $this->writer->endElement();
    }

    /**
     * Uses the $writer object to write the table changes definition $changedTable for $tableName
     *
     * @param string               $tableName
     * @param ezcDbSchemaTableDiff $changedTable
     */
    private function writeChangedTable( $tableName, ezcDbSchemaTableDiff $changedTable )
    {
        $this->writer->startElement( 'table' );
        $this->writer->startElement( 'name' );
        $this->writer->text( $tableName );
        $this->writer->endElement();
        $this->writer->flush();

        // added fields 
        $this->writer->startElement( 'added-fields' );
        $this->writer->flush();
        foreach ( $changedTable->addedFields as $fieldName => $field )
        {
            $this->writer->startElement( 'field' );
            $this->writeField( $fieldName, $field );

            $this->writer->endElement();
            $this->writer->flush();
        }
        $this->writer->endElement();
        $this->writer->flush();

        // changed fields 
        $this->writer->startElement( 'changed-fields' );
        $this->writer->flush();
        foreach ( $changedTable->changedFields as $fieldName => $field )
        {
            $this->writer->startElement( 'field' );
            $this->writeField( $fieldName, $field );

            $this->writer->endElement();
            $this->writer->flush();
        }
        $this->writer->endElement();
        $this->writer->flush();

        // Removed fields
        $this->writer->startElement( 'removed-fields' );
        $this->writer->flush();
        foreach ( $changedTable->removedFields as $fieldName => $fieldStatus )
        {
            $this->writer->startElement( 'field' );
            $this->writer->startElement( 'name' );
            $this->writer->text( $fieldName );
            $this->writer->endElement();
            $this->writer->startElement( 'removed' );
            $this->writer->text( $fieldStatus ? 'true' : 'false' );
            $this->writer->endElement();
            $this->writer->endElement();
            $this->writer->flush();
        }
        $this->writer->endElement();
        $this->writer->flush();

        // added indexes 
        $this->writer->startElement( 'added-indexes' );
        $this->writer->flush();
        foreach ( $changedTable->addedIndexes as $indexName => $index )
        {
            $this->writer->startElement( 'index' );
            $this->writeIndex( $indexName, $index );

            $this->writer->endElement();
            $this->writer->flush();
        }
        $this->writer->endElement();
        $this->writer->flush();

        // changed indexes 
        $this->writer->startElement( 'changed-indexes' );
        $this->writer->flush();
        foreach ( $changedTable->changedIndexes as $indexName => $index )
        {
            $this->writer->startElement( 'index' );
            $this->writeIndex( $indexName, $index );

            $this->writer->endElement();
            $this->writer->flush();
        }
        $this->writer->endElement();
        $this->writer->flush();

        // Removed indexes
        $this->writer->startElement( 'removed-indexes' );
        $this->writer->flush();
        foreach ( $changedTable->removedIndexes as $indexName => $indexStatus )
        {
            $this->writer->startElement( 'index' );
            $this->writer->startElement( 'name' );
            $this->writer->text( $indexName );
            $this->writer->endElement();
            $this->writer->startElement( 'removed' );
            $this->writer->text( $indexStatus ? 'true' : 'false' );
            $this->writer->endElement();
            $this->writer->endElement();
            $this->writer->flush();
        }

        $this->writer->endElement();
        $this->writer->flush();

        $this->writer->endElement();
    }

    /**
     * Writes the schema definition in $dbSchema to the file $file.
     *
     * @param string      $file
     * @param ezcDbSchema $dbSchema
     * @todo throw exception when file can not be opened
     */
    public function saveToFile( $file, ezcDbSchema $dbSchema )
    {
        $schema = $dbSchema->getSchema();
        $data = $dbSchema->getData();

        $this->writer = new XMLWriter();
        if ( ! @$this->writer->openUri( $file ) )
        {
            throw new ezcBaseFilePermissionException( $file, ezcBaseFileException::WRITE );
        }
        $this->writer->startDocument( '1.0', 'utf-8' );
        $this->writer->setIndent( true );

        $this->writer->startElement( 'database' );

        foreach ( $schema as $tableName => $table )
        {
            $this->writer->flush();
            $this->writeTable( $tableName, $table );
        }

        $this->writer->endElement();
        $this->writer->endDocument();
    }

    /**
     * Writes the schema difference definition in $dbSchema to the file $file.
     *
     * @param string          $file
     * @param ezcDbSchemaDiff $dbSchema
     * @todo throw exception when file can not be opened
     */
    public function saveDiffToFile( $file, ezcDbSchemaDiff $dbSchema )
    {
        $this->writer = new XMLWriter();
        if ( ! @$this->writer->openUri( $file ) )
        {
            throw new ezcBaseFilePermissionException( $file, ezcBaseFileException::WRITE );
        }
        $this->writer->startDocument( '1.0', 'utf-8' );
        $this->writer->setIndent( true );

        $this->writer->startElement( 'database' );
        $this->writer->flush();

        // New tables
        $this->writer->startElement( 'new-tables' );
        $this->writer->flush();
        foreach ( $dbSchema->newTables as $tableName => $table )
        {
            $this->writeTable( $tableName, $table );
            $this->writer->flush();
        }
        $this->writer->endElement();
        $this->writer->flush();

        // Removed tables
        $this->writer->startElement( 'removed-tables' );
        $this->writer->flush();
        foreach ( $dbSchema->removedTables as $tableName => $tableStatus )
        {
            $this->writer->startElement( 'table' );
            $this->writer->startElement( 'name' );
            $this->writer->text( $tableName );
            $this->writer->endElement();
            $this->writer->startElement( 'removed' );
            $this->writer->text( $tableStatus ? 'true' : 'false' );
            $this->writer->endElement();
            $this->writer->endElement();
            $this->writer->flush();
        }
        $this->writer->endElement();
        $this->writer->flush();

        // Changed tables
        $this->writer->startElement( 'changed-tables' );
        $this->writer->flush();
        foreach ( $dbSchema->changedTables as $tableName => $table )
        {
            $this->writeChangedTable( $tableName, $table );
            $this->writer->flush();
        }
        $this->writer->endElement();
        $this->writer->flush();

        $this->writer->endElement();
        $this->writer->endDocument();
    }
}
?>
