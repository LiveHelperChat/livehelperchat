<?php
/**
 * File containing the ezcLogSyslogWriter class.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcLogSyslogWriter class provides functionality to write log messages to the
 * UNIX syslog.
 *
 * The writer uses the {@link syslog() syslog} method and works on Windows as
 * well. Please see the documentation of {@link syslog() syslog} for further information
 * on how to set it up correctly.
 *
 * The EventLog severity levels are mapped to the syslog error levels.
 * The mapping is as follows:
 * - ezcLog::DEBUG: LOG_DEBUG
 * - ezcLog::SUCCES_AUDIT: LOG_INFO
 * - ezcLog::FAIL_AUDIT: LOG_INFO
 * - ezcLog::INFO: LOG_INFO
 * - ezcLog::NOTICE: LOG_NOTICE
 * - ezcLog::WARNING: LOG_WARNING
 * - ezcLog::ERROR: LOG_ERR
 * - ezcLog::FATAL: LOG_CRIT
 *
 * @package EventLog
 * @version 1.4
 */
class ezcLogSyslogWriter implements ezcLogWriter
{

    /**
     * Constructs a new syslog writer with the identity $ident, options $option
     * and the facility $facility.
     *
     * The identity will be prepended to each log message in the syslog.
     *
     * The $option argument is used to indicate what logging options will be used
     * when generating a log message. See
     * {@link syslog() syslog} for more information on valid values for $option.
     * The default options are LOG_PID and LOG_ODELAY.
     *
     * The $facility argument is used to specify what type of program is logging
     * the message. This allows you to specify (in your machine's syslog configuration)
     * how messages coming from different facilities will be handled. See
     * {@link syslog() syslog} for more information on valid values for $facility.
     *
     * @param string $ident
     * @param int $option
     * @param int $facility
     */
    public function __construct( $ident, $option = null, $facility = LOG_USER )
    {
        if ( $option == null )
        {
            $option = LOG_PID|LOG_ODELAY;
        }
        openlog( $ident, $option, $facility );
    }

    /**
     * Writes the message $message to the log.
     *
     * The writer can use the severity, source, and category to filter the
     * incoming messages and determine the location where the messages should
     * be written.
     *
     * The array $optional contains extra information that can be added to the log. For example:
     * line numbers, file names, usernames, etc.
     *
     * @throws ezcLogWriterException
     *         If the log writer was unable to write the log message
     *
     * @param string $message
     * @param int $severity
     *        ezcLog::DEBUG, ezcLog::SUCCESS_AUDIT, ezcLog::FAILED_AUDIT, ezcLog::INFO, ezcLog::NOTICE,
     *        ezcLog::WARNING, ezcLog::ERROR or ezcLog::FATAL.
     * @param string $source
     * @param string $category
     * @param array(string=>string) $extraInfo
     */
    public function writeLogMessage( $message, $severity, $source, $category, $extraInfo = array() )
    {
        // generate the log message
        $extra = "";
        if ( sizeof( $extraInfo ) > 0 )
        {
            $extra =  " (" . $this->implodeWithKey( ", ", ": ", $extraInfo ) . ")";
        }

        $logMsg = "[".ezcLog::translateSeverityName( $severity ) . "] ".
                  ( $source == "" ? "" : "[$source] ") .
                  ( $category == "" ? "" : "[$category] " ).
                  "{$message}{$extra}";


        // Map severity to syslog severity
        $syslogSeverity = LOG_INFO;
        switch ( $severity )
        {
            case ezcLog::DEBUG:
                $syslogSeverity = LOG_DEBUG;
                break;
            case ezcLog::SUCCESS_AUDIT:
            case ezcLog::FAILED_AUDIT:
            case ezcLog::INFO:
                $syslogSeverity = LOG_INFO;
                break;
            case ezcLog::NOTICE:
                $syslogSeverity = LOG_NOTICE;
                break;
            case ezcLog::WARNING:
                $syslogSeverity = LOG_WARNING;
                break;
            case ezcLog::ERROR:
                $syslogSeverity = LOG_ERR;
                break;
            case ezcLog::FATAL:
                $syslogSeverity = LOG_CRIT;
                break;
            default:
                $syslogSeverity = LOG_INFO;
                break;
        }

        // write to syslog
        $success = syslog( $syslogSeverity, $logMsg );
        if ( !$success )
        {
            throw new ezcLogWriterException( new Exception( "Couldn't not write to syslog" ) );
        }
    }

    /**
     * Returns a string from the hash $data.
     *
     * The string $splitEntry specifies the string that will be inserted between the pairs.
     * The string $splitKeyVal specifies the string that will be inserted in each pair.
     *
     * Example:
     * <code>
     * $this->implodeWithKey( ", ", ": ", array( "Car" => "red", "Curtains" => "blue" );
     * </code>
     *
     * Will create the following string:
     * <pre>
     * Car: red, Curtains: blue
     * </pre>
     *
     * @param string $splitEntry
     * @param string $splitKeyVal
     * @param array(mixed=>mixed) $data
     * @return string
     */
    protected function implodeWithKey( $splitEntry, $splitKeyVal, $data)
    {
        $total = "";
        if ( is_array( $data ) )
        {
            foreach ( $data as $key => $val )
            {
                $total .=  $splitEntry . $key . $splitKeyVal . $val;
            }
        }

        return substr( $total, strlen( $splitEntry ) );
    }
}
?>
