<?php
/**
 * File containing the ezcLogDatabaseWriter class.
 *
 * @package EventLogDatabaseTiein
 * @version 1.0.2
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcLogDatabaseWriter provides an implementation to write log messages to the database.
 *
 * Example to use the ezcLogDatabaseWriter:
 * <code>
 *     // Get the database instance
 *     $db = ezcDbInstance::get();
 *
 *     // Get the log instance
 *     $log = ezcLog::getInstance();
 *
 *     // Create a new ezcLogDatabaseWriter object based on the database instance
 *     // and with the default table name "log".
 *     // The "log" table must exist already in the database, and must have a compatible structure,
 *     // with any additional fields that you may require, eg. you can use this example schema,
 *     // where the default fields are: id, category, message, severity, source, time
 *     // and the additional fields are: file, line
 *     // DROP TABLE IF EXISTS log;
 *     // CREATE TABLE log (
 *     //   category varchar(255) NOT NULL,
 *     //   file varchar(255),
 *     //   id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
 *     //   line bigint,
 *     //   message varchar(255) NOT NULL,
 *     //   severity varchar(255) NOT NULL,
 *     //   source varchar(255) NOT NULL,
 *     //   time timestamp NOT NULL
 *     // );
 *     $writer = new ezcLogDatabaseWriter( $db, "log" );
 *
 *     // Specify that log messages will be written to the database
 *     $log->getMapper()->appendRule( new ezcLogFilterRule( new ezcLogFilter, $writer, true ) );
 *
 *     // Write a log entry ( message, severity, source, category )
 *     $log->log( "File '/images/spacer.gif' does not exist.", ezcLog::WARNING,
 *          array( "source" => "Application", "category" => "Design" ) );
 *
 *     // Write a log entry ( message, severity, source, category, file, line )
 *     $log->log( "File '/images/spacer.gif' does not exist.", ezcLog::WARNING,
 *          array( "source" => "Application", "category" => "Design" ),
 *          array( "file" => "/index.php", "line" => 123 ) );
 * </code>
 *
 * @property string $table
 *                  The table name.
 * @property string $message
 *                  The name of the column message.
 * @property string $datetime
 *                  The name of the column datetime.
 * @property string $severity
 *                  The name of the column severity.
 * @property string $source
 *                  The name of the column source.
 * @property string $category
 *                  The name of the column category.
 *
 * @package EventLogDatabaseTiein
 * @version 1.0.2
 * @mainclass
 */
class ezcLogDatabaseWriter implements ezcLogWriter
{
    /**
     * Holds the instance to the database handler.
     *
     * @var ezcDBHandler
     */
    private $db = null;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Holds the default column names in the log tables.
     *
     * @var array(string=>mixed)
     */
    private $defaultColumns = array();

    /**
     * Holds additional column names in the log tables.
     *
     * @var array(string=>mixed)
     */
    private $additionalColumns = array();

    /**
     * Maps tables to ezcLogFilter messages.
     *
     * @var ezcLogFilterSet
     */
    private $map;

    /**
     * Holds the default table name.
     *
     * @var string
     */
    private $defaultTable = false;

    /**
     * Construct a new database log-writer.
     *
     * If $databaseInstance is given, that instance will be used for writing. If it
     * is omitted the default database instance will be retrieved.
     *
     * This constructor is a tie-in.
     *
     * @param ezcDbHandler $databaseInstance
     * @param string $defaultTable
     */
    public function __construct( ezcDbHandler $databaseInstance, $defaultTable = false )
    {
        $this->db = $databaseInstance;

        $this->map = new ezcLogFilterSet();
        $this->defaultTable = $defaultTable;

        $this->message = "message";
        $this->datetime = "time";
        $this->severity = "severity";
        $this->source = "source";
        $this->category = "category";
    }

    /**
     * Sets the property $name to $value.
     *
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'table':
                $this->properties[$name] = $value;
                break;

            case 'message':
            case 'datetime':
            case 'severity':
            case 'source':
            case 'category':
                $this->defaultColumns[$name] = $value;
                break;

            default:
                $this->additionalColumns[$name] = $value;
                break;
        }
    }

    /**
     * Returns the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the property $name does not exist
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'table':
                return $this->properties[$name];

            case 'message':
            case 'datetime':
            case 'severity':
            case 'source':
            case 'category':
                return $this->defaultColumns[$name];

            default:
                if ( isset( $this->additionalColumns[$name] ) )
                {
                    return $this->additionalColumns[$name];
                }
                else
                {
                    throw new ezcBasePropertyNotFoundException( $name );
                }
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'table':
                return isset( $this->properties[$name] );
            case 'message':
            case 'datetime':
            case 'severity':
            case 'source':
            case 'category':
                return isset( $this->defaultColumns[$name] );

            default:
                return isset( $this->additionalColumns[$name] );
        }
    }

    /**
     * Writes the message $message to the log.
     *
     * The writer can use the severity, source, and category to filter the
     * incoming messages and determine the location where the messages should
     * be written.
     *
     * $optional may contain extra information that can be added to the log. For example:
     * line numbers, file names, usernames, etc.
     *
     * @throws ezcLogWriterException
     *         If the log writer was unable to write the log message
     * @param string $message
     * @param int $severity
     *        ezcLog:: DEBUG, SUCCES_AUDIT, FAILED_AUDIT, INFO, NOTICE, WARNING, ERROR or FATAL.
     * $param string $source
     * @param string $category
     * @param array(string=>string) $optional
     */
    public function writeLogMessage( $message, $severity, $source, $category, $optional = array() )
    {
        $severityName = ezcLog::translateSeverityName( $severity );
        $tables = $this->map->get( $severity, $source, $category );
        $query = $this->db->createSelectQuery();

        if ( count( $tables ) > 0 )
        {
            foreach ( $tables as $t )
            {
                try
                {
                    $q = $this->db->createInsertQuery();
                    $q->insertInto( $t )
                           ->set( $this->message, $q->bindValue( $message ) )
                           ->set( $this->severity, $q->bindValue( $severityName ) )
                           ->set( $this->source, $q->bindValue( $source ) )
                           ->set( $this->category, $q->bindValue( $category ) )
                           ->set( $this->datetime, $query->expr->now() );
                    foreach ( $optional as $key => $val )
                    {
                        $q->set( ( isset( $this->additionalColumns[$key] ) ? $this->additionalColumns[$key] : $key ), $q->bindValue( $val ) );
                    }
                    $stmt = $q->prepare();
                    $stmt->execute();

                }
                catch ( PDOException $e )
                {
                    throw new ezcLogWriterException( $e );
                }
            }
        }
        else
        {
            if ( $this->defaultTable !== false )
            {
                try
                {
                    $q = $this->db->createInsertQuery();
                    $q->insertInto( $this->defaultTable )
                           ->set( $this->message, $q->bindValue( $message ) )
                           ->set( $this->severity, $q->bindValue( $severityName ) )
                           ->set( $this->source, $q->bindValue( $source ) )
                           ->set( $this->category, $q->bindValue( $category ) )
                           ->set( $this->datetime, $query->expr->now() );
                    foreach ( $optional as $key => $val )
                    {
                        $q->set( ( isset( $this->additionalColumns[$key] ) ? $this->additionalColumns[$key] : $key ), $q->bindValue( $val ) );
                    }
                    $stmt = $q->prepare();
                    $stmt->execute();
                }
                catch ( PDOException $e )
                {
                    throw new ezcLogWriterException( $e );
                }
            }
        }
    }

    /**
     * Returns an array that describes the coupling between the logMessage
     * information and the columns in the database.
     *
     * @return array(string=>string)
     */
    public function getColumnTranslations()
    {
        return array_merge( $this->defaultColumns, $this->additionalColumns );
    }

    /**
     * Maps the table $tableName to the messages specified by the {@link ezcLogFilter} $logFilter.
     *
     * Log messages that matches with the filter are written to the table $tableName. 
     * This method works the same as {@link ezclog::map()}.
     *
     * @param ezcLogFilter $logFilter 
     * @param string $tableName
     */
    public function setTable( ezcLogFilter $logFilter, $tableName )
    {
        $this->map->appendRule( new ezcLogFilterRule( $logFilter, $tableName, true ) );
    }
}
?>
