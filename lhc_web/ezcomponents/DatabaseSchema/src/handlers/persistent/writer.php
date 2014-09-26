<?php
/**
 * File containing the ezcDbSchemaPersistentWriter class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This handler creates PHP classes to be used with PersistentObject from a
 * DatabaseSchema.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaPersistentWriter implements ezcDbSchemaFileWriter
{

    /**
     * If files should be overwritten. 
     * 
     * @var boolean
     */
    private $overwrite;

    /**
     * Class prefix. 
     * 
     * @var string
     */
    private $prefix;

    /**
     * Creates a new writer instance
     *
     * @param bool    $overwrite   Overwrite existsing files?
     * @param string  $classPrefix Prefix for class names.
     * @return void
     */
    public function __construct( $overwrite = false, $classPrefix = null )
    {
        $this->overwrite = $overwrite;
        $this->prefix    = ( $classPrefix === null ) ? "" : $classPrefix;
    }
    
    /**
     * Returns what type of schema writer this class implements.
     * This method always returns ezcDbSchema::FILE
     *
     * @return int The type of this schema writer.
     */
    public function getWriterType()
    {
        return ezcDbSchema::FILE;
    }

    /**
     * Writes the schema definition in $dbSchema to files located in $dir.
     * This method dumps the given schema to PersistentObject definitions, which
     * will be located in the given directory.
     *
     * @param string $dir           The directory to store definitions in.
     * @param ezcDbSchema $dbSchema The schema object to create defs for.
     *
     * @throws ezcBaseFileNotFoundException If the given directory could not be
     *                                      found.
     * @throws ezcBaseFilePermissionException If the given directory is not 
     *                                        writable.
     */
    public function saveToFile( $dir, ezcDbSchema $dbSchema )
    {
        if ( !is_dir( $dir ) ) 
        {
            throw new ezcBaseFileNotFoundException( $dir, 'directory' );
        }

        if ( !is_writable( $dir ) )
        {
            throw new ezcBaseFilePermissionException( $dir, ezcBaseFileException::WRITE );
        }

        $schema = $dbSchema->getSchema();

        foreach ( $schema as $tableName => $table )
        {
            $this->writeTable( $dir, $tableName, $table );
        }
    }

    /**
     * Write a field of the schema to the PersistentObject definition.
     * This method writes a database field to the PersistentObject definition
     * file.
     *
     * @param resource(file) $file    The file to write to.
     * @param string $fieldName       The name of the field.
     * @param ezcDbSchemaField $field The field object.
     * @param bool $isPrimary         Whether the field is the primary key.
     */
    private function writeField( $file, $fieldName, $field, $isPrimary )
    {
        fwrite( $file, "\n" );
        if ( $isPrimary )
        {
            fwrite( $file, "\$def->idProperty               = new ezcPersistentObjectIdProperty();\n" );
            fwrite( $file, "\$def->idProperty->columnName   = '$fieldName';\n" );
            fwrite( $file, "\$def->idProperty->propertyName = '$fieldName';\n" );
            if ( $field->autoIncrement )
            {
                fwrite( $file, "\$def->idProperty->generator    = new ezcPersistentGeneratorDefinition( 'ezcPersistentSequenceGenerator' );\n" );
            }
            else
            {
                fwrite( $file, "\$def->idProperty->generator    = new ezcPersistentGeneratorDefinition( 'ezcPersistentManualGenerator' );\n" );
                fwrite( $file, "\$def->idProperty->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;\n" );
            }
        }
        else
        {
            fwrite( $file, "\$def->properties['$fieldName']               = new ezcPersistentObjectProperty();\n" );
            fwrite( $file, "\$def->properties['$fieldName']->columnName   = '$fieldName';\n" );
            fwrite( $file, "\$def->properties['$fieldName']->propertyName = '$fieldName';\n" );
            fwrite( $file, "\$def->properties['$fieldName']->propertyType = {$this->translateType($field->type)};\n" );
        }
        fwrite( $file, "\n" );
    }

    /**
     * Translates eZ DatabaseSchema data types to eZ PersistentObject types.
     * This method receives a type string from a ezcDbSchemaField object and
     * returns the corresponding type value from PersistentObject.
     *
     * @todo Why does PersistentObject not support "boolean" types?
     *
     * @see ezcPersistentObjectProperty::TYPE_INT
     * @see ezcPersistentObjectProperty::TYPE_FLOAT
     * @see ezcPersistentObjectProperty::TYPE_STRING
     *
     * @param string $dbType The DatabaseSchema type string.
     * @return int The ezcPersistentObjectProperty::TYPE_* value.
     */
    private function translateType( $dbType )
    {
        switch ( $dbType )
        {
            case 'integer':
            case 'timestamp':
            case 'boolean':
                return 'ezcPersistentObjectProperty::PHP_TYPE_INT';
            case 'float':
            case 'decimal':
                return 'ezcPersistentObjectProperty::PHP_TYPE_FLOAT';
            case 'text':
            case 'time':
            case 'date':
            case 'blob':
            case 'clob':
            default:
                return 'ezcPersistentObjectProperty::PHP_TYPE_STRING';
        }
    }

    /**
     * Writes the PersistentObject defintion for a table.
     *
     * This method writes the PersistentObject definition for a single database
     * table. It creates a new file in the given directory, named in the format
     * <table_name>.php, writes the start of the definition to it and calls the
     * {@link ezcDbschemaPersistentWriter::writeField()} method for each of the
     * database fields.
     *
     * The defition files always contain an object instance $def, which is 
     * returned in the end.
     *
     * @param string $dir              The directory to write the defititions to.
     * @param string $tableName        Name of the database table.
     * @param ezcDbSchemaTable $table  The table definition.
     */
    private function writeTable( $dir, $tableName, ezcDbSchemaTable $table )
    {
        $file = $this->openFile( $dir, $tableName );

        fwrite( $file, "\$def = new ezcPersistentObjectDefinition();\n" );
        fwrite( $file, "\$def->table = '$tableName';\n" );
        fwrite( $file, "\$def->class = '{$this->prefix}$tableName';\n" );

        $primaries = $this->determinePrimaries( $table->indexes );

        // fields 
        foreach ( $table->fields as $fieldName => $field )
        {
            $this->writeField( $file, $fieldName, $field, isset( $primaries[$fieldName] ) );
        }
        $this->closeFile( $file );
    }

    /**
     * Open a file for writing a PersistentObject definition to.
     * This method opens a file for writing a PersistentObject definition to
     * and writes the basic PHP open tag to it.
     * 
     * @param string $dir  The diretory to open the file in.
     * @param string $name The table name.
     * @return resource(file) The file resource used for writing.
     *
     * @throws ezcBaseFileIoException 
     *         if the file to write to already exists.
     * @throws ezcBaseFilePermissionException
     *         if the file could not be opened for writing.
     */
    private function openFile( $dir, $name )
    {
        $filename = $dir . DIRECTORY_SEPARATOR . strtolower( $this->prefix ) . strtolower( $name ) . '.php';
        // We do not want to overwrite files
        if ( file_exists( $filename ) && ( $this->overwrite === false || is_writable( $filename ) === false ) )
        {
            throw new ezcBaseFileIoException( $filename, ezcBaseFileException::WRITE, "File already exists or is not writeable. Use --overwrite to ignore existance." );
        }
        $file = @fopen( $filename, 'w' );
        if ( $file === false )
        {
            throw new ezcBaseFilePermissionException( $file, ezcBaseFileException::WRITE );
        }
        fwrite( $file, "<?php\n" );
        fwrite( $file, "// Autogenerated PersistentObject definition\n" );
        fwrite( $file, "\n" );
        return $file;
    }

    /**
     * Close a file where a PersistentObject definition has been written to.
     * This method closes a file after writing a PersistentObject definition to
     * it and writes the PHP closing tag to it.
     * 
     * @param resource(file) $file The file resource to close.
     * @return void
     */
    private function closeFile( $file )
    {
        fwrite( $file, "return \$def;\n" );
        fwrite( $file, "\n" );
        fwrite( $file, "?>\n" );
        fclose( $file );
    }

    /**
     * Extract primary keys from an index definition. 
     * This method extracts the names of all primary keys from the index
     * defintions of a table.
     * 
     * @param array(string=>ezcDbSchemaIndex) $indexes Indices.
     * @return array(string=>bool) The primary keys.
     */
    private function determinePrimaries( $indexes )
    {
        $primaries = array();
        foreach ( $indexes as $index )
        {
            if ( $index->primary )
            {
                foreach ( $index->indexFields as $field => $definiton )
                {
                    $primaries[$field] = true;
                }
            }
        }
        return $primaries;
    }
}
?>
