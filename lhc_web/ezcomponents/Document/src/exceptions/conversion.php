<?php
/**
 * File containing the ezcDocumentConversionException class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, when the RST parser could not parse asome token sequence.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentConversionException extends ezcDocumentException
{
    /**
     * Mapping of error levels to strings
     *
     * @var array
     */
    protected $levelMapping = array(
        E_NOTICE  => 'Notice',
        E_WARNING => 'Warning',
        E_ERROR   => 'Error',
        E_PARSE   => 'Fatal error',
    );

    /**
     * Error string
     *
     * String describing the general type of this error
     *
     * @var string
     */
    protected $errorString = 'Conversion error';

    /**
     * Original exception message
     *
     * @var string
     */
    public $parseError;

    /**
     * Construct exception from errnous string and current position
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $position
     * @param Exception $exception
     * @return void
     */
    public function __construct( $level, $message, $file = null, $line = null, $position = null, Exception $exception = null )
    {
        $this->parseError = $message;

        $message = "{$this->errorString}: {$this->levelMapping[$level]}: '$message'";

        if ( $file !== null )
        {
            $message .= " in file '$file'";
        }

        if ( $line !== null )
        {
            $message .= " in line $line at position $position";
        }

        parent::__construct( $message . '.', 0, $exception );
    }
}

?>
