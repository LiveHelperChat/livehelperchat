<?php
/**
 * File containing the ezcMailMultipartParser class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Base class for Multipart parsers.
 *
 * @package Mail
 * @version 1.7.1
 * @access private
 */
abstract class ezcMailMultipartParser extends ezcMailPartParser
{
    /**
     * The boundary separator string.
     *
     * @var string
     */
    private $boundary = null;

    /**
     * The headers for the multipart.
     *
     * @var ezcMailHeadersHolder
     */
    protected $headers = null;

    /**
     * The headers for the current subpart.
     *
     * @var ezcMailHeadersHolder
     */
    private $currentPartHeaders = null;

    /**
     * The current part.
     *
     * @var ezcMailPartParser
     */
    private $currentPartParser = null;

    /**
     * This state is used prior to hitting the first part.
     */
    const PARSE_STATE_PRE_FIRST = 1;

    /**
     * This state is used when the parser is parsing headers.
     */
    const PARSE_STATE_HEADERS = 2;

    /**
     * This state is used when the parser is parsing the body.
     */
    const PARSE_STATE_BODY = 3;

    /**
     * This state is set after the last of the parts is closed.
     */
    const PARSE_STATE_POST_LAST = 4;

    /**
     * Stores the state of the parser.
     *
     * @var int
     */
    private $parserState = self::PARSE_STATE_PRE_FIRST;

    /**
     * Constructs a new Multipart parser.
     *
     * @param ezcMailHeadersHolder $headers
     */
    public function __construct( ezcMailHeadersHolder $headers )
    {
        $this->headers = $headers;

        // get the boundary
        preg_match( '/\s*boundary="?([^;"]*);?/i',
                    $this->headers['Content-Type'],
                    $parameters );
        if ( count( $parameters ) > 0 )
        {
            $this->boundary = trim( $parameters[1], '"' );
        }
        else
        {
            // no boundary?!? Houston, we have a problem.
            // todo: try to detect the boundary by scanning for --lines
        }
    }

    /**
     * Parses a multipart body.
     *
     * @throws ezcBaseFileNotFoundException
     *         if a neccessary temporary file could not be opened.
     * @param string $origLine
     */
    public function parseBody( $origLine )
    {
        if ( $this->parserState == self::PARSE_STATE_POST_LAST )
        {
            return;
        }

        $line = rtrim( $origLine, "\r\n" );

        // check if we hit any of the boundaries
        $newPart = false;
        $endOfMultipart = false;
        if ( strlen( $line ) > 0 && $line[0] == "-" )
        {
            if ( strcmp( trim( $line ), '--' . $this->boundary ) === 0 )
            {
                $newPart = true;
            }
            else if ( strcmp( trim( $line ), '--' . $this->boundary . '--' ) === 0 )
            {
                $endOfMultipart = true;
            }
        }

        // actions to do when starting or finishing a part
        if ( $newPart || $endOfMultipart )
        {
            if ( $this->parserState != self::PARSE_STATE_BODY )
            {
                // something is b0rked, we got a new separator before getting a body
                // we'll skip this part and continue to the next
                $this->currentPartParser = null;
                $this->currentPartHeaders = new ezcMailHeadersHolder();
                $this->parserState = $newPart ? self::PARSE_STATE_HEADERS : self::PARSE_STATE_POST_LAST;
            }
            else
            {
                // complete the work on the current part if there was any
                if ( $this->currentPartParser !== null )
                {
                    $part = $this->currentPartParser->finish();
                    if ( $part !== null ) // parsing failed
                    {
                        $this->partDone( $part );
                    }
                }

                // prepare for a new part if any
                $this->currentPartParser = null;
                $this->parserState =self::PARSE_STATE_POST_LAST;
                if ( $newPart )
                {
                    $this->parserState = self::PARSE_STATE_HEADERS;
                    $this->currentPartHeaders = new ezcMailHeadersHolder();
                }
            }
        }
        // normal data, pass to headers or current body
        else
        {
            if ( $this->parserState == self::PARSE_STATE_HEADERS && $line == '' )
            {
                $this->currentPartParser = self::createPartParserForHeaders( $this->currentPartHeaders );
                $this->parserState = self::PARSE_STATE_BODY;
            }
            else if ( $this->parserState == self::PARSE_STATE_HEADERS )
            {
                $this->parseHeader( $line, $this->currentPartHeaders );
            }
            else if ( $this->parserState == self::PARSE_STATE_BODY )
            {
                if ( $this->currentPartParser ) // we may have none if the part type was unknown
                {
                    // send body data to the part
                    $this->currentPartParser->parseBody( $origLine );
                }
            }
            // we are done parsing the multipart, ignore anything else pushed to us.
        }
    }

    /**
     * Completes the parsing of the multipart and returns the corresponding part.
     *
     * This method should not be overriden. Use finishMultipart() instead.
     *
     * @return ezcMailMultipart
     */
    public function finish()
    {
        if ( $this->parserState != self::PARSE_STATE_POST_LAST )
        {
            // this should never happen
            // let's give the last parser a chance to clean up after himself
            if ( $this->currentPartParser !== null )
            {
                $part = $this->currentPartParser->finish();
                $this->partDone( $part );
                $this->currentPartParser = null;
            }
        }
        $multipart = $this->finishMultipart();
        ezcMailPartParser::parsePartHeaders( $this->headers, $multipart );
        $multipart->boundary = $this->boundary;
        return $multipart;
    }

    /**
     * This function will be called every time a part has been parsed.
     *
     * Implementors should put the part into the correct multitype part.
     * @param ezcMailPart $part
     */
    abstract public function partDone( ezcMailPart $part );

    /**
     * Returns the multipart part corresponding to the parsed object.
     *
     * This method is called by finish() when all parts have been parsed.
     *
     * @return ezcMailMultipart
     */
    abstract public function finishMultipart();
}
?>
