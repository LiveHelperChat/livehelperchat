<?php
/**
 * File containing the ezcLog class.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcLog class records log messages and audit trails to one or multiple
 * writers.
 *
 * Available writers are:
 * - {@link ezcLogUnixFileWriter Unix File} writer
 * - {@link ezcLogDatabaseWriter Database} writer
 *
 * Extra writers can be added by implementing the {@link ezcLogWriter} interface.
 *
 * Use the {@link getMapper()} method to get an instance of the ezcLogMapper.
 * The ezcLogMapper classes specifies incoming log messages with the {@link ezcLogFilter}.
 * Log messages that are accepted, match with the filter, are sent to the
 * {@link ezcLogWriter}.
 *
 * The following example demonstrates how all log messages, except for the
 * audit trailing and debug messages, are written to a file.
 * <code>
 * $filter = new ezcLogFilter();
 * $filter->severity = ezcLog::INFO | ezcLog::NOTICE | ezcLog::WARNING | ezcLog::ERROR | ezcLog::FATAL;
 *
 * $log = ezcLog::getInstance();
 * $log->getMapper()->appendRule( new ezcLogFilterRule( $filter, new ezcLogUnixFileWriter( "/tmp/logs/", "error.log" ), true ) );
 * </code>
 *
 * The log messages with the severity: INFO, NOTICE, WARNING, ERROR, and FATAL will
 * be written to the file: "/tmp/logs/error.log". See {@link ezcLogUnixFileWriter} for
 * the description of the file format.
 *
 * The following example will write the audit trails to the database:
 * <code>
 * $filter = new ezcLogFilter();
 * $filter->severity = ezcLog::SUCCESS_AUDIT | ezcLog::FAILED_AUDIT;
 *
 * $log = ezcLog::getInstance();
 * $log->getMapper()->appendRule( new ezcLogFilterRule( $filter, new ezcLogDatabaseWriter( "audits" ), true ) );
 * </code>
 *
 * The audit trails will be stored in the table "audits". See {@link ezcLogDatabaseWriter}
 * for creating the appropriate tables and setting up the database. See the {@link ezcLogFilter}
 * for more details.
 *
 * Use the {@link log()} method to log messages at the specified writers. This
 * method expects a:
 * - Message, contains a single log message.
 * - Severity, indicates the level of importance.
 * - Extra attributes (optional).
 *
 * Although the interpretation of the severity levels are up to the programmer,
 * the most common interpretations are:
 * - DEBUG: Records information about the progress in the program and references
 *   source code functions. Knowledge of the source code is needed to interpret
 *   this log message.
 * - INFO: Informative logging at a detailed level. This logging method produces a
 *   high level of logging, which is unmanageable on a production environment.
 *   Usually INFO logging is only enabled to help by analysing a problem.
 * - NOTICE: Informative logging at a lower detail level than INFO logging.
 *   Only major stages are recorded and is useful to monitor a low volume system.
 * - WARNING: Something unexpected happened, but did not cause any loss of service.
 * - ERROR: An error occured, which may cause partial loss of service. Usually the
 *   system can recover.
 * - FATAL: An serious error occured and the system is unlikely to recover.
 * - SUCCESS_AUDIT: Informative logging about a successful completion of work by
 *   a module completed. Useful to trace system changes directly or indirectly
 *   done by a user.
 * - FAILED_AUDIT: Informative logging about an action from a module
 *   with a negative result. A failed login will most likely added to this severity.
 *
 * The next example logs a fatal error and has no extra attributes:
 * <code>
 * ezcLog::getInstance()->log( "Cannot open ini file: <$file>", ezcLog::FATAL );
 * </code>
 *
 * The log message will get by default the category and source: "default". The
 * default values can be modified by changing, respectively, the properties
 * $category and $source.
 *
 * An example of a Payment checker is as follows:
 * <code>
 * // The start of the Payment module.
 * $log = ezcLog::getInstance();
 * $log->source = "Payment checker"; // Change the default source.
 *
 * $log->log( "Checking the received amount", ezcLog::INFO, array( "shop" ) );
 *
 * if ( !$eZPay->receivedAmount() != $requiredAmount )
 * {
 *     $log->log( "Received amount: <".$eZPay->receivedAmount()."> expected: <$requiredAmount>.",
 *                 ezcLog::DEBUG,
 *                 array( "category" => "shop", "file" => __FILE__, "line" => __LINE )
 *              );
 *
 *     $log->log( "Insufficient amount.",
 *                ezcLog::FAILED_AUDIT,
 *                array( "UserName" => getCurrentUser(), category => "Payment" )
 *              )
 *
 *     $log->log( "Rollback amount not implemented, cannot recover, ezcLog::FATAL );
 *     exit();
 * }
 * </code>
 *
 * Sometimes information repeats for specific severities or categories. For example that
 * for the audit trails an username is required. Convenience methods like:
 * {@link setSeverityAttributes()} and {@link setSourceAttributes()} exist to append
 * information automatically to the log message.
 *
 * The ezcLog class provides a {@link trigger_error()} log handler: {@link ezcLog::logHandler()}.
 * Using the trigger_error method makes your code less Log package dependent and
 * produces less overhead when logging is disabled.
 *
 * See the {@link ezcLog::logHandler()} method for more information about how to set up the
 * trigger_error functionality.
 *
 * See the {@link ezcDebug} package for more detailed information about writing DEBUG
 * messages.
 *
 * @property string $source
 *           Definition of the global location where the log message comes
 *           from.  Some examples are: module, source file, extension, etc. The
 *           source depends also on the severity of the message. For DEBUG
 *           messages is the source file more important whereas for a FATAL
 *           error the module is sufficient.
 * @property string $category
 *           Definition of the message group. Again the category is related to
 *           the severity. The non audit trails can group the log messages
 *           like: Database (or even the database types), Templates, etc. For
 *           audit trails it makes much sense to categorize the actions. For
 *           example: security, modified content, published content, shop, etc.
 *
 * @package EventLog
 * @version 1.4
 * @mainclass
 */
class ezcLog
{
    /**
     * Debug severity constant.
     */
     const DEBUG          = 1;

    /**
     * Success audit severity constant.
     */
     const SUCCESS_AUDIT  = 2;

    /**
     * Failed audit severity constant.
     */
     const FAILED_AUDIT   = 4;

     /**
      * Info severity constant.
      */
     const INFO           = 8;

     /**
      * Notice severity constant.
      */
     const NOTICE         = 16;

     /**
      * Warning severity constant.
      */
     const WARNING        = 32;

     /**
      * Error severity constant.
      */
     const ERROR          = 64;

     /**
      * Fatal severity constant.
      */
     const FATAL          = 128;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Contains the logic of mapping an incoming log message to the writer.
     *
     * @var ezcLogFilterSet
     */
    protected $writers;

    /**
     * Stores the attributes from the eventTypes and eventSources.
     *
     * $var ezcLogContext
     */
    protected $context;

    /**
     * Stores the instance of this class.
     *
     * @var ezcLog
     */
    private static $instance = null;

    /**
     * Stores the setting whether writer exceptions should be thrown.
     *
     * @var bool
     */
    private $throwWriterExceptions = true;

    /**
     * Constructs an empty ezcLog instance.
     *
     * This constructor is private as this class should be used as a
     * singleton. Use the getInstance() method instead to get an ezcLog instance.
     */
    private function __construct()
    {
        $this->reset();
    }

    /**
     * Returns the instance of the class.
     *
     * @return ezcLog
     */
    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new self();
            ezcBaseInit::fetchConfig( 'ezcInitLog', self::$instance );
        }
        return self::$instance;
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the property $name does not exist
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case "source":
            case "category":
                $this->properties[$name] = $value;
                return;
        }

        throw new ezcBasePropertyNotFoundException( $name );
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
            case "source":
            case "category":
                return $this->properties[$name];
        }

        throw new ezcBasePropertyNotFoundException( $name );
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
            case 'source':
            case 'category':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Resets the log instance to its initial state.
     *
     * All sourceAttributes, severityAttributes, and writers will be removed.
     * The default source and category are also reset.
     */
    public function reset()
    {
        $this->writers = new ezcLogFilterSet();
        $this->context = new ezcLogContext();

        $this->setDefaults();
    }

    /**
     * Sets the given ezcLogMapper $mapper as the log message to writer map.
     *
     * By default the ezcLogFilterSet is the default writer map. The default
     * ezcLogMapper can be replaced with this method.
     *
     * @param ezcLogMapper $mapper
     */
    public function setMapper( ezcLogMapper $mapper )
    {
        $this->writers = $mapper;
    }

    /**
     * Returns an instance of the current ezcLogMapper.
     *
     * @return ezcLogMapper
     */
    public function getMapper()
    {
        return $this->writers;
    }

    /**
     * Sets the source and category defaults to "default".
     */
    protected function setDefaults()
    {
        $this->properties['source'] = "default";
        $this->properties['category'] = "default";
    }

    /**
     * Enables or disables writer exceptions with the boolean $enable.
     *
     * Typically you want to have exceptions enabled while developing your application
     * in order to catch potential problems. A live server however, should not throw
     * a deadly exception when a relatively unimportant debug message could not be written to
     * the log file. For these setups you can disable writer exceptions.
     *
     * @param bool $enable
     */
    public function throwWriterExceptions( $enable )
    {
        $this->throwWriterExceptions = $enable;
    }

    /**
     * Write the message $message with additional information to one or multiple log writers.
     *
     * The log message $message, severity $severity, and extra attributes $attributes are sent to
     * the writers that matches with the {@link ezcLogFilter}. The following parameters are
     * taken in the comparation with the ezcLogFilter:
     * - $severity: the severity of the log message.
     * - $attributes[ "source" ]: the source from where the log message comes.
     * - $attributes[ "category" ]: the category of the log message.
     *
     * See for more information about filter matching the classes {@link ezcLog} and
     * {@link ezcLogFilter}.
     *
     * The message $message describes what happened. The severity $severity is one of the ezcLog constants:
     * - DEBUG: Records information about the progress in the program and references
     *   source code functions. Knowledge of the source code is needed to interpret
     *   this log message.
     * - INFO: Informative logging at a detailed level. This logging method produces a
     *   high level of logging, which is unmanageable on a production environment.
     *   Usually INFO logging is only enabled to help by analysing a problem.
     * - NOTICE: Informative logging at a lower detail level than INFO logging.
     *   Only major stages are recorded and is useful to monitor a low volume system.
     * - WARNING: Something unexpected happened, but did not cause any loss of service.
     * - ERROR: An error occured, which may cause partial loss of service. Usually the
     *   system can recover.
     * - FATAL: An serious error occured and the system is unlikely to recover.
     * - SUCCESS_AUDIT: Informative logging about a successful completion of work by
     *   a module completed. Useful to trace system changes directly or indirectly
     *   done by a user.
     * - FAILED_AUDIT: Informative logging about an action from a module
     *   with a negative result. A failed login will most likely added to this severity.
     *
     * The attributes array $attributes can have one or multiple attributes that will
     * be added to the log. If source and category are given, they will override the default
     * source or category given as property to this object. Further more it is up to the
     * application what to include in the log. It may be useful to add the
     * file and linenumber to the attributes array. Use the magic PHP constants: {@link __FILE__}
     * and {@link __LINE__}  for this purpose. The next example adds an warning to the log.
     *
     * <code>
     * ezcLog::getInstance()->source = "templateEngine"; // Set the default source.
     * ezcLog::getInstance()->log( "ezcPersistentObject <$obj> does not exist.",
     *     ezcLog::WARNING,
     *     array( "category" => "Database", "line" => __LINE__, "file" => __FILE__, "code" => 123 )
     *     );
     * </code>
     *
     * The methods {@link setSeverityAttributes()} and {@link setSourceAttributes()} can automatically
     * add attributes to log messages based on, respectively, the severity and source.
     *
     * See also {@link logHandler()} on how to use {@link trigger_error()} to write log messages.
     *
     * @throws ezcLogWriterException if {@link throwWriterExceptions} are enabled and a log entry
     *                               could not be written.
     *
     * @param string $message
     * @param int $severity  One of the following severity constants:
     *                       DEBUG, SUCCES_AUDIT, FAIL_AUDIT, INFO, NOTICE, WARNING, ERROR, or FATAL.
     * @param array(string=>string) $attributes
     */
    public function log( $message, $severity, array $attributes = array() )
    {
        $source = ( isset( $attributes["source"] ) ? $attributes["source"] : $this->properties["source"] );
        $category = ( isset( $attributes["category"] ) ? $attributes["category"] : $this->properties["category"] );

        unset( $attributes["source"] );
        unset( $attributes["category"] );

        $attributes = array_merge( $this->context->getContext( $severity, $source ), $attributes );

        $writers = $this->writers->get( $severity, $source, $category );
        foreach ( $writers as $writer )
        {
            try
            {
                $writer->writeLogMessage( $message, $severity, $source, $category, $attributes );
            }
            catch ( ezcLogWriterException $e )
            {
                if ( $this->throwWriterExceptions )
                {
                    throw $e;
                }
            }
        }
    }

    /**
     * Sets the attributes $attributes for a group of severities $severityMask.
     *
     * The severities are specified with a bit mask. These attributes will be
     * added to the log message when the log severity is the same as specified
     * here.
     *
     * Example:
     * <code>
     * ezcLog::getInstance()->setSeverityAttributes(
     *     ezcLog::SUCCESS_AUDIT | ezcLog::FAILED_AUDIT
     *     array( "username" => "Jan K. Doodle" )
     * );
     * </code>
     *
     * Every log message that has the severity SUCCESS_AUDIT or FAILED_AUDIT
     * includes the user name: "Jan K. Doodle".
     *
     * @param integer $severityMask Multiple severities are specified with a logic-or.
     * @param array(string=>string) $attributes
     */
    public function setSeverityAttributes( $severityMask, $attributes )
    {
        $this->context->setSeverityContext( $severityMask, $attributes );
    }

    /**
     * Sets the attributes $attributes for a group of sources $sources.
     *
     * The sources are specified in an array. These attributes will be added to the
     * log message when it matches with the given $sources.
     *
     * Example:
     * <code>
     * ezcLog::getInstance()->setSourceAttributes(
     *     array( "Paynet", "Bibit", "Paypal" ),
     *     array( "MerchantID" => $merchantID )
     * );
     * </code>
     *
     * Every log message that comes from the payment module: Paynet, Bibit, or Paypal
     * includes the Merchant ID.
     *
     * @param array(string) $sources
     * @param array(string=>string) $attributes
     */
    public function setSourceAttributes ( $sources, $attributes )
    {
        $this->context->setSourceContext( $sources, $attributes );
    }

    /**
     * This method can be set as error_handler to log using {@link trigger_error()}.
     *
     * This method can be assigned with the {@link set_error_handler()} to handle the
     * trigger_error calls. This method will get the log instance and forward the
     * message. But includes the following information:
     * - The file and linenumber are automatically added.
     * - Source and category can be 'encoded' in the message.
     *
     * The message format is as follows:
     * <pre>
     * [ source, category ] Message
     * </pre>
     *
     * When one name is given between the brackets, the category will be set and the message has a default source:
     * <pre>
     * [ category ] Message
     * </pre>
     *
     * Without any names between the brackets, the default category and source are used:
     * <pre>
     * Message
     * </pre>
     *
     * The following example creates manually an error handler and forwards the
     * ERROR, WARNING and NOTICE severities.
     * <code>
     * function myLogHandler($errno, $errstr, $errfile, $errline)
     * {
     *     switch ($errno)
     *     {
     *         case E_USER_ERROR:
     *         case E_USER_WARNING:
     *         case E_USER_NOTICE:
     *             if ( $loggingEnabled )
     *             {   // Forward the message to the log handler.
     *                 ezcLog::LogHandler( $errno, $errstr, $errfile, $errline );
     *             }
     *             break;
     *
     *         default:
     *             print( "$errstr in $errfile on line $errline\n" );
     *             break;
     *     }
     * }
     *
     * // Register myLogHandler
     * set_error_handler( "myLogHandler" );
     *
     * // Write an warning to the log.
     * trigger_error( "[paynet, transaction] Didn't get a callback from the Paynet service", E_USER_WARNING );
     *
     * // Add a notice.
     * trigger_error( "Getting paynet status information", E_USER_NOTICE );
     *
     * </code>
     *
     * Notice that the ezcLog component is not loaded at all when the logging is disabled.
     *
     * @param int $errno
     * @param int $errstr
     * @param string $errfile
     * @param int $errline
     */
     public static function logHandler( $errno, $errstr, $errfile, $errline )
     {
         $log = ezcLog::getInstance();
         $lm = new ezcLogMessage( $errstr, $errno, $log->source, $log->category );
         $log->log(
             $lm->message, $lm->severity,
             array( "source" => $lm->source, "category" => $lm->category, "file" => $errfile, "line" => $errline )
         );
     }

    /**
     * Translates the severity constant to a string and returns this.
     *
     * Null is returned when the severity constant is invalid.
     *
     * @param int $severity
     * @return string
     */
    public static function translateSeverityName( $severity )
    {
        switch ( $severity )
        {
            case self::DEBUG:           return "Debug";
            case self::SUCCESS_AUDIT:   return "Success audit";
            case self::FAILED_AUDIT:    return "Failed audit";
            case self::INFO:            return "Info";
            case self::NOTICE:          return "Notice";
            case self::WARNING:         return "Warning";
            case self::ERROR:           return "Error";
            case self::FATAL:           return "Fatal";
            default:                    return null;
        }
    }
}
?>
