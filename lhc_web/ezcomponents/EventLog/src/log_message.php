<?php
/**
 * File containing the ezcLogMessage class.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Holds a log message and provides convenience methods to read the information.
 *
 * The ezclogMessage class is used for subtracting the information from the message
 * parameter from {@link trigger_error()}. See the {@link ezcLog::logHandler} for
 * more information.
 *
 * The message formats that can be parsed are:
 *
 * <pre>
 * [ source, category, error_type ] Message
 * </pre>
 *
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
 * The following properties are set after construction or after calling {@link parseMessage()}:
 * - message, contains the message without extra the additional information.
 * - source, contains either the default source or the source set in the incoming message.
 * - category, contains either the default category or the category set in the incoming message.
 * - error_type, any severity without the leading "ezcLog::" (see {@link ezcLogMessage::parseMessage}); which are:
 *   ezcLog::DEBUG, ezcLog::INFO, ezcLog::NOTICE, ezcLog::WARNING, ezcLog::ERROR, ezcLog::FATAL, ezcLog::FAILED_AUDIT, ezcLog::SUCCESS_AUDIT.
 * - severity, if error_type is not set: severity of the error. Which is ezcLog::NOTICE, ezcLog::WARNING, or ezcLog::ERROR.
 *
 * @package EventLog
 * @version 1.4
 * @access private
 */
class ezcLogMessage
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array( "message" => "", "source" => "", "category" => "", "severity" => "" );

    /**
     * Constructs the ezcLogMessage from the $message, $severity, $defaultSource and $defaultCategory.
     *
     * $message is parsed by parseMessage() and properties are set.
     *
     * @param string $message
     * @param int $severity
     * @param string $defaultSource Use this source when not given in the message itself.
     * @param string $defaultCategory Use this category when not give in the message itself.
     */
    public function __construct( $message, $severity, $defaultSource, $defaultCategory )
    {
        $this->parseMessage( $message, $severity, $defaultSource, $defaultCategory );
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
            case 'message':
            case 'source':
            case 'category':
            case 'severity':
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
            case 'message':
            case 'source':
            case 'category':
            case 'severity':
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
            case 'message':
            case 'source':
            case 'category':
            case 'severity':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Parses the message $message and sets the properties.
     *
     * See the general class documentation for message format.
     * The severity $severity can be a E_USER_* PHP constant. The values will be translated accordingly:
     * - E_USER_NOTICE -> ezcLog::NOTICE
     * - E_USER_WARNING -> ezcLog::WARNING
     * - E_USER_ERROR -> ezcLog::ERROR
     *
     * Any other severity from ezcLog can be encapsulated in the message, for example:
     * - [source, message, debug] -> ezcLog::DEBUG
     * - [source, message, info] -> ezcLog::INFO
     * - [source, message, notice] -> ezcLog::NOTICE
     * - [source, message, error] -> ezcLog::ERROR
     * - [source, message, warning] -> ezcLog::WARNING
     * - [source, message, fatal] -> ezcLog::FATAL
     * - [source, message, success_audit] -> ezcLog::SUCCESS_AUDIT
     * - [source, message, failed_audit] -> ezcLog::FAILED_AUDIT
     *
     * @param string $message
     * @param int $severity
     * @param string $defaultSource
     * @param string $defaultCategory
     */
    public function parseMessage( $message, $severity, $defaultSource, $defaultCategory )
    {
        preg_match( "/^\s*(?:\[(?:\s?)(?P<source>[^,\]]*)(?:,\s(?P<category>[^,\]]*))?(?:,\s?(?P<level>[a-zA-Z_]*))?\s?\])?\s*(?P<message>.*)$/", $message, $matches );

        $this->message = $matches['message'] === '' ? false : $matches['message'];

        if ( $matches['category'] === '' )
        {
            $this->category = $matches['source'] === '' ? $defaultCategory : $matches['source'];
            $this->source = $defaultSource;
        }
        else
        {
            $this->category = $matches['category'];
            $this->source = $matches['source'];
        }

        if ( $matches['level'] === '' )
        {
            switch ( $severity )
            {
                case E_USER_NOTICE:  $this->severity = ezcLog::NOTICE; break;
                case E_USER_WARNING: $this->severity = ezcLog::WARNING; break;
                case E_USER_ERROR:  $this->severity = ezcLog::ERROR; break;
                default: $this->severity = false;
            }
        }
        else
        {
            $constantName = 'ezcLog::' . strtoupper( trim( $matches['level'] ) );
            if ( !defined( $constantName ) )
            {
                throw new ezcLogWrongSeverityException( trim( $matches['level'] ) );
            }
            else
            {
                $this->severity = constant( $constantName );
            }
        }
    }
}
?>
