<?php

/**
 * File containing the ezcDebug class.
 *
 * @package Debug
 * @version 1.2.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcDebug class provides functionality to format and store debug messages and timers. 
 *
 * The functionality of the Debug component is two folded: 
 * - Debug log messages
 * - Timers
 * 
 * The log messages are heavily based on the {@link EventLog} log messages. In fact
 * internally the EventLog is used with its own log writer. The {@link log()} method
 * is almost the same as from the EventLog. The next example demonstrates how to instantiate the
 * ezcDebug class and write some log messages: 
 * <code>
 * $debug = ezcDebug::getInstance(); 
 * $debug->log( "Connecting with the paynet server", 2 );
 * // ...
 * $debug->log( "Connection failed, retrying in 5 seconds", 1 );
 * // ...
 * $debug->log( "Could not connect with the server", 0 );
 * </code>
 *
 * The second parameter of the log method is the verbosity. This is a number that
 * specifies the importance of the log message. That makes it easier to sort out messages of less importance.
 * In this example, we assumed the more important the message, the lower the 
 * verbosity number.
 *
 * The ezcDebug timer is designed to allow the next two timing methods:
 * - Timers, the time between two points in the program. 
 * - Accumulators, gets the relative time after the script started. 
 *
 * The "Timers" are simply set with the methods {@link startTimer()} and {@link stopTimer()}. The next example
 * demonstrates the timing of a simple calculation:
 * <code>
 * $debug = ezcDebug::getInstance();
 * $debug->startTimer( "Simple calculation" );
 * 
 * // Simple calculation
 * $result = 4 + 6;
 *
 * $debug->stopTimer( "Simple calculation" ); // Parameter can be omitted.
 * </code>
 *
 * To get timing points, accumulators, use the {@link switchTimer()} method. This is shown in the next example:
 * <code>
 * $debug = ezcDebug::getInstance();
 * $debug->startTimer( "My script" );
 * // ...
 * $debug->switchTimer( "Reading ini file" );
 * // ...
 * $debug->switchTimer( "Initializing template parser" );
 * // ...
 * $debug->switchTimer( "Parsing" );
 * // ...
 * $debug->stopTimer();
 * </code>
 *
 * @property ezcDebugOptions $options
 *           Options to configure the behaviour of ezcDebug, including stack
 *           trace behaviours.
 *
 * @package Debug
 * @version 1.2.1
 * @mainclass
 */
class ezcDebug
{
    /**
     * Properties. 
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Instance of the singleton ezcDebug object.
     *
     * Use the getInstance() method to retrieve the instance.
     *
     * @var ezcDebug
     */
    private static $instance = null;

    /**
     * The formatter that generates the debug output.
     *
     * @var ezcDebugFormatter
     */
    private $formatter = null;

    /**
     * A pointer to the logging system.
     *
     * @var ezcLog
     */
    private $log = null;

    /**
     * The timing object used to store timing information.
     *
     * @var ezcDebugTimer
     */
    private $timer = null;

    /**
     * The writer that holds debug output.
     *
     * @var ezcLogWriter
     */
    private $writer = null;

    /**
     * Constructs a new debug object and attaches it to the log object.
     *
     * This method is private because the getInstance() should be called.
     */
    private function __construct()
    {
        $this->options = new ezcDebugOptions();

        $original = ezcLog::getInstance();

        $this->log = clone( $original ); 
        $this->log->reset();
        $this->log->setMapper( new ezcLogFilterSet() );

        // Set the writer.
        $this->writer = new ezcDebugMemoryWriter();

        $filter = new ezcLogFilter();
        $filter->severity = ezcLog::DEBUG;
        $this->log->getMapper()->appendRule( new ezcLogFilterRule( $filter, $this->writer, true ) );

        $this->reset();
    }


    /**
     * Property get access.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the given property could not be found.
     * @param string $propertyName
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property set access.
     *
     * @throws ezcBasePropertyNotFoundException
     * @param string $propertyName
     * @param string $propertyValue
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'options':
                if ( !( $propertyValue instanceof ezcDebugOptions ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcDebugOptions'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property isset access. 
     * 
     * @param string $propertyName 
     * @return bool
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }


    /**
     * Resets the log messages and timer information.
     * 
     * @return void
     */
    public function reset()
    {
        $this->writer->reset();
        $this->timer = new ezcDebugTimer();
    }

    /**
     * Returns the instance of this class.
     *
     * When the ezcDebug instance is created it is automatically added to the instance
     * of ezcLog.
     *
     * @return ezcDebug
     */
    public static function getInstance()
    {
        if ( is_null( self::$instance ))
        {
            self::$instance = new ezcDebug();
            ezcBaseInit::fetchConfig( 'ezcInitDebug', self::$instance );
        }

        return self::$instance;
    }

    /** 
     * Returns the instance of the EventLog used in this class.
     *
     * The returned instance is not the same as retrieved via the 
     * ezcLog::getInstance() method. 
     * 
     * @return ezcLog
     */ 
    public function getEventLog()
    {
        return $this->log;
    }

    /**
     * Sets the formatter $reporter for the output.
     *
     * If no formatter is set {@link ezcDebugHtmlReporter} will be used by default.
     *
     * @param ezcDebugOutputFormatter $formatter
     * @return void
     */
    public function setOutputFormatter( ezcDebugOutputFormatter $formatter )
    {
        $this->formatter = $formatter;
    }

    /**
     * Returns the formatted debug output.
     *
     * @return string
     */
    public function generateOutput()
    {
        if ( is_null( $this->formatter ) )
            $this->formatter = new ezcDebugHtmlFormatter();

        return $this->formatter->generateOutput( $this->writer->getStructure(), $this->timer->getTimeData() );
    }


    /**
     * Starts the timer with the identifier $name.
     *
     * Optionally, a timer group can be given with the $group parameter.
     *
     * @param string $name
     * @param string $group
     */
    public function startTimer( $name, $group = null )
    {
        $this->timer->startTimer( $name, $group );
    }

    /**
     * Stores the time from the running timer, and starts a new timer.
     *
     * Stores the time for $oldTimer (maybe omitted if only 1 timer is running)
     * and starts a new timer with $newName.
     *
     * @param string      $newName
     * @param string|bool $oldName
     */
    public function switchTimer( $newName, $oldName = false )
    {
        $this->timer->switchTimer( $newName, $oldName );
    }

    /**
     * Stops the timer identified by $name.
     *
     * $name can be omitted (false) if only one timer is running.
     *
     * @param string|bool $name
     */
    public function stopTimer( $name = false )
    {
        $this->timer->stopTimer( $name );
    }

    /**
     * Writes the debug message $message with verbosity $verbosity.
     *
     * Arbitrary $extraInfo can be submitted. If $stackTrace is set to true, a
     * stack trace will be stored at the current program position.
     *
     * @param string $message
     * @param int $verbosity
     * @param array(string=>string) $extraInfo
     * @param bool $stackTrace
     */
    public function log( $message, $verbosity, array $extraInfo = array(), $stackTrace = false )
    {
        // Add the verbosity
        $extraInfo = array_merge( array( "verbosity" => $verbosity ), $extraInfo );
        if ( $this->options->stackTrace === true || $stackTrace === true )
        {
            $extraInfo['stackTrace'] = $this->getStackTrace();
        }
        $this->log->log( $message, ezcLog::DEBUG, $extraInfo );
    }

    /**
     * Returns a stack trace iterator for the current call.
     *
     * Returns a
     * - {@link ezcDebugXdebugStacktraceIterator} if Xdebug is available
     * - {@link ezcDebugPhpStacktraceIterator} otherwise
     * representing a stack trace of the current function environment.
     * 
     * @return ezcDebugStacktraceIterator
     */
    private function getStackTrace()
    {
        if ( extension_loaded( 'xdebug' ) )
        {
            return new ezcDebugXdebugStacktraceIterator(
                xdebug_get_function_stack(),
                2,
                $this->options
            );
        }
        else
        {
            return new ezcDebugPhpStacktraceIterator(
                debug_backtrace(),
                2,
                $this->options
            );
        }
    }

    /**
     * Dispatches the message and error type to the correct debug or log
     * function.
     *
     * This function should be used as the set_error_handler from the
     * trigger_error function.
     *
     * Use for example the following code in your application:
     *
     * <code>
     * function debugHandler( $a, $b, $c, $d )
     * {
     *     ezcDebug::debugHandler( $a, $b, $c, $d );
     * }
     *
     * set_error_handler( "debugHandler" );
     * </code>
     *
     * Use trigger_error() to log warning, error, etc:
     *
     * <code>
     * trigger_error( "[Paynet, templates] Cannot load template", E_USER_WARNING );
     * </code>
     *
     * See the PHP documentation of
     * {@link http://php.net/trigger_error trigger_error} for more information.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return void
     */
    public static function debugHandler( $errno, $errstr, $errfile, $errline )
    {
        $debug = ezcDebug::getInstance();
        $log   = $debug->getEventLog();
        
        preg_match(
            '/^\s*(?:\[([^,\]]*)(?:,\s(.*))?\])?\s*(?:(\d+):)?\s*(.*)$/',
            $errstr,
            $matches
        );
        
        $message = ( $matches[4] === '' ? false : $matches[4] );
        $verbosity = ( $matches[3] === '' ? false : $matches[3] );

        if ( strlen( $matches[2] ) == 0 )
        {
            $category = ( $matches[1] === '' ? $log->category : $matches[1] );
            $source   = $log->source;
        }
        else
        {
            $category = $matches[2];
            $source   = $matches[1];
        }
        
        $severity = false;
        switch ( $errno )
        {
            case E_USER_NOTICE:
                $severity = ezcLog::NOTICE;
                break;
            case E_USER_WARNING:
                $severity = ezcLog::WARNING;
                break;
            case E_USER_ERROR:
                $severity = ezcLog::ERROR;
                break;
        }

        $debug->log(
            $message,
            $severity,
            array(
                'source'    => $source,
                'category'  => $category,
                'verbosity' => $verbosity,
                'file'      => $errfile,
                'line'      => $errline
            )
        );
    }
}
?>
