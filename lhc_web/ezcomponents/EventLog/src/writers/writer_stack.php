<?php
/**
 * File containing the ezcLogStackWriter class.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcLogStackWriter class stores all received log messages in memory.
 *
 * The main purpose is to get all received log message at once, for example to
 * output them in the webpage.
 *
 * @package EventLog
 * @version 1.4
 */
class ezcLogStackWriter implements ezcLogWriter, IteratorAggregate
{
    /**
     * Stores all entries received by this writer.
     *
     * @var array(int=>ezcLogEntry)
     */
    protected $entries = array();

    /**
     * Writes the message $message to the log.
     *
     * The writer can use the severity, source, and category to filter the
     * incoming messages and determine the location where the messages should
     * be written.
     *
     * The array $optional contains extra information that can be added to the
     * log. For example: line numbers, file names, usernames, etc.
     *
     * @throws ezcLogWriterException
     *         If the log writer was unable to write the log message
     *
     * @param string $message
     * @param int $severity
     *        ezcLog::DEBUG, ezcLog::SUCCESS_AUDIT, ezcLog::FAILED_AUDIT,
     *        ezcLog::INFO, ezcLog::NOTICE, ezcLog::WARNING, ezcLog::ERROR or
     *        ezcLog::FATAL.
     * @param string $source
     * @param string $category
     * @param array(string=>string) $optional
     */
    public function writeLogMessage( $message, $severity, $source, $category, $optional = array() )
    {
        $this->entries[] = new ezcLogEntry( $message, $severity, $source, $category, $optional );
    }

    /**
     * Implements IteratorAggreagate, returns iterator for all entries.
     *
     * @see entries
     * @return ArrayObject
     */
    public function getIterator()
    {
        return new ArrayObject( $this->entries );
    }
}
?>
