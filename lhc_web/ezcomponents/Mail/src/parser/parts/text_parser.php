<?php
/**
 * File containing the ezcMailTextParser class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Parses mail parts of type "text".
 *
 * @package Mail
 * @version 1.7.1
 * @access private
 */
class ezcMailTextParser extends ezcMailPartParser
{
    /**
     * Stores the parsed text of this part.
     *
     * @var string $text
     */
    private $text = null;

    /**
     * Holds the headers of this text part.
     *
     * @var ezcMailHeadersHolder
     */
    private $headers = null;

    /**
     * Holds the subtype of the parsed part.
     *
     * @var string
     */
    private $subType = null;

    /**
     * Constructs a new ezcMailTextParser of the subtype $subType and
     * additional headers $headers.
     *
     * @param string $subType
     * @param ezcMailHeadersHolder $headers
     */
    public function __construct( $subType, ezcMailHeadersHolder $headers )
    {
        $this->subType = $subType;
        $this->headers = $headers;
    }

    /**
     * Adds each line to the body of the text part.
     *
     * @param string $line
     */
    public function parseBody( $line )
    {
        $line = rtrim( $line, "\r\n" );
        if ( $this->text === null )
        {
            $this->text = $line;
        }
        else
        {
            $this->text .= "\n" . $line;
        }
    }

    /**
     * Returns the ezcMailText part corresponding to the parsed message.
     *
     * @return ezcMailText
     */
    public function finish()
    {
        $charset = "us-ascii"; // RFC 2822 default
        if ( isset( $this->headers['Content-Type'] ) )
        {
            preg_match( '/\s*charset\s?=\s?"?([^;"\s]*);?/',
                            $this->headers['Content-Type'],
                            $parameters );
            if ( count( $parameters ) > 0 )
            {
                $charset = strtolower( trim( $parameters[1], '"' ) );
            }
        }

        $encoding = strtolower( $this->headers['Content-Transfer-Encoding'] );
        if ( $encoding == ezcMail::QUOTED_PRINTABLE )
        {
            $this->text = quoted_printable_decode( $this->text );
        }
        else if ( $encoding == ezcMail::BASE64 )
        {
            $this->text = base64_decode( $this->text );
        }

        $this->text = ezcMailCharsetConverter::convertToUTF8( $this->text, $charset );

        $part = new ezcMailText( $this->text, 'utf-8', ezcMail::EIGHT_BIT, $charset );
        $part->subType = $this->subType;
        $part->setHeaders( $this->headers->getCaseSensitiveArray() );
        ezcMailPartParser::parsePartHeaders( $this->headers, $part );
        $part->size = strlen( $this->text );
        return $part;
    }
}
?>
