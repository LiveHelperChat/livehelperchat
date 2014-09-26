<?php
/**
 * File containing the ezcMailDeliveryStatusParser class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Parses mail parts of type "delivery-status".
 *
 * @package Mail
 * @version 1.7.1
 * @access private
 */
class ezcMailDeliveryStatusParser extends ezcMailPartParser
{
    /**
     * This mail part will be returned by the method finish().
     *
     * @var ezcMailDeliveryStatus
     */
    private $part = null;

    /**
     * The current section of the parsing of delivery-status headers.
     *
     * 0      = the per-message section
     * 1, ... = the per-recipient section
     *
     * @var int
     */
    private $section;

    /**
     * Holds the size of the mail part.
     *
     * @var int
     */
    private $size;

    /**
     * Constructs a new ezcMailDeliveryStatusParser with additional headers $headers.
     *
     * @param ezcMailHeadersHolder $headers
     */
    public function __construct( ezcMailHeadersHolder $headers )
    {
        $this->headers = $headers;
        $this->section = 0;
        $this->part = new ezcMailDeliveryStatus();
        $this->size = 0;
    }

    /**
     * Parses each line of the mail part.
     *
     * @param string $line
     */
    public function parseBody( $line )
    {
        $this->parseHeader( $line, $this->headers );
        $this->size += strlen( $line );
    }

    /**
     * Parses the header given by $line.
     *
     * @param string $line
     * @param ezcMailHeadersHolder $headers
     */
    protected function parseHeader( $line, ezcMailHeadersHolder $headers )
    {
        $matches = array();
        preg_match_all( "/^([\w-_]*):\s?(.*)/", $line, $matches, PREG_SET_ORDER );
        if ( count( $matches ) > 0 )
        {
            $this->lastParsedHeader = $matches[0][1];
            $this->headerValue = trim( $matches[0][2] );
        }
        else if ( isset( $this->lastParsedHeader ) && $this->lastParsedHeader !== null ) // take care of folding
        {
            $this->headerValue .= $line;
        }
        if ( strlen( trim( $line ) ) == 0 )
        {
            $this->section++;
            $this->part->createRecipient();
            return;
        }
        if ( $this->section == 0 )
        {
            $this->part->message[$this->lastParsedHeader] = $this->headerValue;
        }
        else
        {
            $this->part->recipients[$this->section - 1][$this->lastParsedHeader] = $this->headerValue;
        }
    }

    /**
     * Returns the ezcMailDeliveryStatus part corresponding to the parsed message.
     *
     * @return ezcMailDeliveryStatus
     */
    public function finish()
    {
        unset( $this->part->recipients[$this->section - 1] ); // because one extra recipient is created in parseHeader()
        $this->part->size = $this->size;
        return $this->part;
    }
}
?>
