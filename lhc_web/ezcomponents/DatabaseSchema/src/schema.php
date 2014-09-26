<?php
/**
 * File containing the ezcDbSchema class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcDbSchema is the main class for schema operations.
 *
 * ezcDbSchema represents the schema itself and provide proxy methods to the
 * handlers that are able to load/save schemas from/to files, databases or other
 * sources/destinations, depending on available schema handlers.
 *
 * A database schema is a definition of all the tables inside a database,
 * including field definitions and indexes.
 *
 * The available builtin handlers are currently for MySQL, XML files and PHP 
 * arrays.
 *
 * The following example shows you how you can load a database schema
 * from the PHP format and store it into the XML format.
 * <code>
 *     $schema = ezcDbSchema::createFromFile( 'array', 'file.php' );
 *     $schema->writeToFile( 'xml', 'file.xml' );
 * </code>
 *
 * The following example shows how you can load a database schema
 * from the XML format and store it into a database.
 * <code>
 *     $db = ezcDbFactory::create( 'mysql://user:password@host/database' );
 *     $schema = ezcDbSchema::createFromFile( 'xml', 'file.php' );
 *     $schema->writeToDb( $db );
 * </code>
 *
 * Example that shows how to make a comparison between a file on disk and a
 * database, and how to apply the changes.
 * <code>
 *     $xmlSchema = ezcDbSchema::createFromFile( 'xml', 'wanted-schema.xml' );
 *     $dbSchema = ezcDbSchema::createFromDb( $db );
 *     $diff = ezcDbSchemaComparator::compareSchemas( $xmlSchema, $dbSchema );
 *     $diff->applyToDb( $db );
 * </code>
 *
 * @see ezcDbSchemaTable
 * @see ezcDbSchemaField
 * @see ezcDbSchemaIndex 
 * @see ezcDbSchemaIndexField
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @mainclass
 */
class ezcDbSchema
{
    /**
     * Used by reader and writer classes to inform that it implements a file
     * based handler.
     */
    const FILE = 1;

    /**
     * Used by reader and writer classes to inform that it implements a
     * database based handler.
     */
    const DATABASE = 2;

    /**
     * Stores the schema information.
     *
     * @var array(string=>ezcDbSchemaTable)
     */
    private $schema;

    /**
     * Meant to store data - not currently in use
     *
     * @var array
     */
    private $data;

    /**
     * A list of all the supported database filed types
     *
     * @var array(string)
     */
    static public $supportedTypes = array(
        'integer', 'boolean', 'float', 'decimal', 'timestamp', 'time', 'date',
        'text', 'blob', 'clob'
    );

    /**
     * Contains the options that are used by creating new schemas.
     *
     * @var ezcDbSchemaOptions
     */
    static public $options;

    /**
     * Constructs a new ezcDbSchema object with schema definition $schema.
     *
     * @param array(ezcDbSchemaTable) $schema
     * @param array                   $data
     */
    public function __construct( array $schema, $data = array() )
    {
        self::initOptions();
        $this->schema = $schema;
        $this->data = $data;
    }

    /**
     * Checks whether the object in $obj implements the correct $type of reader handler.
     *
     * @throws ezcDbSchemaInvalidReaderClassException if the object in $obj is
     *         not a schema reader of the correct type.
     *
     * @param ezcDbSchemaReader $obj
     * @param int               $type
     */
    static private function checkSchemaReader( ezcDbSchemaReader $obj, $type )
    {
        if ( !( ( $obj->getReaderType() & $type ) == $type ) )
        {
            throw new ezcDbSchemaInvalidReaderClassException( get_class( $obj ), $type );
        }
    }

    /**
     * Factory method to create a ezcDbSchema object from the file $file with the format $format.
     *
     * @throws ezcDbSchemaInvalidReaderClassException if the handler associated
     *         with the $format is not a file schema reader.
     *
     * @param string $format
     * @param string $file
     */
    static public function createFromFile( $format, $file )
    {
        $className = ezcDbSchemaHandlerManager::getReaderByFormat( $format );
        $reader = new $className();
        self::checkSchemaReader( $reader, self::FILE );
        return $reader->loadFromFile( $file );
    }

    /**
     * Factory method to create a ezcDbSchema object from the database $db.
     *
     * @throws ezcDbSchemaInvalidReaderClassException if the handler associated
     *         with the $format is not a database schema reader.
     *
     * @param ezcDbHandler $db
     */
    static public function createFromDb( ezcDbHandler $db )
    {
        self::initOptions();
        $className = ezcDbSchemaHandlerManager::getReaderByFormat( $db->getName() );
        $reader = new $className();
        self::checkSchemaReader( $reader, self::DATABASE );
        return $reader->loadFromDb( $db );
    }

    /**
     * Checks whether the object in $obj implements the correct $type of writer handler.
     *
     * @throws ezcDbSchemaInvalidWriterClassException if the object in $obj is
     *         not a schema writer of the correct type.
     *
     * @param ezcDbSchemaWriter $obj
     * @param int               $type
     */
    static private function checkSchemaWriter( $obj, $type )
    {
        if ( !( ( $obj->getWriterType() & $type ) == $type ) )
        {
            throw new ezcDbSchemaInvalidWriterClassException( get_class( $obj ), $type );
        }
    }

    /**
     * Writes the schema to the file $file in format $format.
     *
     * @throws ezcDbSchemaInvalidWriterClassException if the handler associated
     *         with the $format is not a file schema writer.
     *
     * @param string $format  Available formats are at least: 'array' and 'xml'.
     * @param string $file
     */
    public function writeToFile( $format, $file )
    {
        $className = ezcDbSchemaHandlerManager::getWriterByFormat( $format );
        $reader = new $className();
        self::checkSchemaWriter( $reader, self::FILE );
        $reader->saveToFile( $file, $this );
    }

    /**
     * Creates the tables defined in the schema into the database specified through $db.
     *
     * @throws ezcDbSchemaInvalidWriterClassException if the handler associated
     *         with the $format is not a database schema writer.
     *
     * @param ezcDbHandler $db
     */
    public function writeToDb( ezcDbHandler $db )
    {
        self::initOptions();
        $className = ezcDbSchemaHandlerManager::getWriterByFormat( $db->getName() );
        $writer = new $className();
        self::checkSchemaWriter( $writer, self::DATABASE );
        $writer->saveToDb( $db, $this );
    }

    /**
     * Returns the $db specific SQL queries that would create the tables
     * defined in the schema.
     *
     * The database type can be given as both a database handler (instanceof
     * ezcDbHandler) or the name of the database as string as retrieved through
     * calling getName() on the database handler object.
     *
     * @see ezcDbHandler::getName()
     *
     * @throws ezcDbSchemaInvalidWriterClassException if the handler associated
     *         with the $format is not a database schema writer.
     *
     * @param string|ezcDbHandler $db
     * @return array(string)
     */
    public function convertToDDL( $db )
    {
        self::initOptions();
        if ( $db instanceof ezcDbHandler )
        {
            $db = $db->getName();
        }
        $className = ezcDbSchemaHandlerManager::getDiffWriterByFormat( $db );
        $writer = new $className();
        self::checkSchemaWriter( $writer, self::DATABASE );
        return $writer->convertToDDL( $this );
    }

    /**
     * Returns the internal schema by reference.
     *
     * The method returns an array where the key is the table name, and the
     * value the table definition stored in a ezcDbSchemaTable struct.
     *
     * @return array(string=>ezcDbSchemaTable)
     */
    public function &getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns the internal data.
     *
     * This data is not used anywhere though.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Associates an option object with this static class.
     *
     * @param ezcDbSchemaOptions $options
     */
    static public function setOptions( ezcDbSchemaOptions $options )
    {
        self::$options = $options;
    }

    /**
     * Checks whether the static options have been initialized, and if not it
     * creates a new options class and assigns it to the options statick
     * property.
     *
     * Usually the option object is initialized in the constructor, but that of
     * course does not work for static classes.
     */
    static private function initOptions()
    {
        if ( !ezcDbSchema::$options )
        {
            ezcDbSchema::$options = new ezcDbSchemaOptions();
        }
    }

    /**
     * Returns an object to represent a table in the schema.
     *
     * @param array(string=>ezcDbSchemaField) $fields
     * @param array(string=>ezcDbSchemaIndex) $indexes
     * @return ezcDbSchemaTable or an inherited class
     */
    static public function createNewTable( $fields, $indexes )
    {
        self::initOptions();
        $className = ezcDbSchema::$options->tableClassName;
        return new $className( $fields, $indexes );
    }

    /**
     * Returns an object to represent a table's field in the schema.
     *
     * @param string  $fieldType
     * @param integer $fieldLength
     * @param bool    $fieldNotNull
     * @param mixed   $fieldDefault
     * @param bool    $fieldAutoIncrement
     * @param bool    $fieldUnsigned
     * @return ezcDbSchemaField or an inherited class
     */
    static public function createNewField( $fieldType, $fieldLength, $fieldNotNull, $fieldDefault, $fieldAutoIncrement, $fieldUnsigned )
    {
        self::initOptions();
        $className = ezcDbSchema::$options->fieldClassName;
        return new $className( $fieldType, $fieldLength, $fieldNotNull, $fieldDefault, $fieldAutoIncrement, $fieldUnsigned );
    }

    /**
     * Returns an object to represent a table's field in the schema.
     *
     * @param array(string=>ezcDbSchemaIndexField) $fields
     * @param bool  $primary
     * @param bool  $unique
     * @return ezcDbSchemaIndex or an inherited class
     */
    static public function createNewIndex( $fields, $primary, $unique )
    {
        self::initOptions();
        $className = ezcDbSchema::$options->indexClassName;
        return new $className( $fields, $primary, $unique );
    }

    /**
     * Returns an object to represent a table's field in the schema.
     *
     * @param int $sorting
     * @return ezcDbSchemaIndexField or an inherited class
     */
    static public function createNewIndexField( $sorting = null )
    {
        self::initOptions();
        $className = ezcDbSchema::$options->indexFieldClassName;
        return new $className( $sorting );
    }
}
?>
