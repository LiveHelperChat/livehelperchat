<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4
 * @filesource
 * @package EventLog
 */

/**
 * File containing the ezcLogEntry class.
 *
 * The ezcLogEntry class provides a structure to represent a log entry with all
 * data passed to ezcLog::log().
 *
 * This class is used for entries hold by the ezcLogStackWriter.
 *
 * @package EventLog
 * @version 1.4
 */
class ezcLogEntry extends ezcBaseStruct
{
    /**
     * The textual log message.
     *
     * @var string
     */
    public $message;

    /**
     * Severity of the log message.
     *
     * @var int
     */
    public $severity;

    /**
     * The source of the log message.
     *
     * @var string
     */
    public $source;

    /**
     * The category of the log message.
     *
     * @var string
     */
    public $category;

    /**
     * Optional informations
     *
     * @var array
     */
    public $optional;

    /**
     * The timestamp of the moment when this object was created
     *
     * @var int
     */
    public $timestamp;

    /**
     * Constructs a new ezcLogEntry.
     *
     * @param string $message
     * @param int $severity
     * @param string $source
     * @param string $category
     * @param array $optional
     * @param int $timestamp
     */
    public function __construct( $message = '', $severity = 0,
        $source = '', $category = '', $optional = array(), $timestamp = null )
    {
        $this->message = $message;
        $this->severity = $severity;
        $this->source = $source;
        $this->category = $category;
        $this->optional = $optional;
        $this->timestamp = $timestamp === null ? time() : $timestamp;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcLogEntry
     */
    static public function __set_state( array $array )
    {
        return new ezcLogEntry( $array['message'], $array['severity'],
            $array['source'], $array['category'], $array['optional'],
            $array['timestamp'] );
    }
}
?>
