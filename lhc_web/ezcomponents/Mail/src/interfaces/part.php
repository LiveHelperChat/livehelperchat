<?php
/**
 * File containing the ezcMailPart class.
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base class for all mail MIME parts.
 *
 * This base class provides functionality to store headers and to generate
 * the mail part. Implementations of this class must handle the body of that
 * parts themselves. They must also implement {@link generateBody()} which is
 * called when the message part is generated.
 *
 * @property ezcMailContentDispositionHeader $contentDisposition
 *           Contains the information from the Content-Disposition field of
 *           this mail.  This useful especially when you are investigating
 *           retrieved mail to see if a part is an attachment or should be
 *           displayed inline.  However, it can also be used to set the same
 *           on outgoing mail. Note that the ezcMailFile part sets the
 *           Content-Disposition field itself based on it's own properties
 *           when sending mail.
 * @property int $size
 *           The size of the mail part in bytes. It is set when parsing a
 *           mail {@link ezcMailParser->parseMail()}.
 * @property-read ezcMailHeadersHolder $headers
 *                Contains the header holder object, taking care of the
 *                headers of this part. Can be retreived for reasons of
 *                extending this class and its derivals.
 *
 * @package Mail
 * @version 1.7.1
 */
abstract class ezcMailPart
{
    /**
     * An associative array containing all the headers set for this part.
     *
     * @var ezcMailHeadersHolder
     */
    private $headers = null;

    /**
     * An associative array containing the charsets for the headers in this
     * part.
     *
     * @var array(string=>string)
     */
    private $headerCharsets = array();

    /**
     * An array of headers to exclude when generating the headers.
     *
     * @var array(string)
     */
    private $excludeHeaders = array();

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Constructs a new mail part.
     */
    public function __construct()
    {
        $this->headers = new ezcMailHeadersHolder();
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property is read-only.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'contentDisposition':
            case 'size':
                $this->properties[$name] = $value;
                break;

            case 'headers':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist.
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'contentDisposition':
            case 'size':
                return isset( $this->properties[$name] ) ? $this->properties[$name] : null;

            case "headers":
                return $this->headers;

            default:
                throw new ezcBasePropertyNotFoundException( $name );

        }
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
            case 'contentDisposition':
            case 'size':
                return isset( $this->properties[$name] );

            case "headers":
                return isset( $this->headers );

            default:
                return false;
        }
    }

    /**
     * Returns the RAW value of the header $name.
     *
     * Returns an empty string if the header is not found.
     * Getting headers is case insensitive. Getting the header
     * 'Message-Id' will match both 'Message-ID' and 'MESSAGE-ID'
     * as well as 'Message-Id'.
     *
     * The raw value is MIME-encoded, so if you want to decode it,
     * use {@link ezcMailTools::mimeDecode()} or implement your own
     * MIME-decoding function.
     *
     * If $returnAllValues is true, the function will return all
     * the values of the header $name from the mail in an array. If
     * it is false it will return only the first value as a string
     * if there are multiple values present in the mail.
     *
     * @param string $name
     * @param bool $returnAllValues
     * @return mixed
     */
    public function getHeader( $name, $returnAllValues = false )
    {
        if ( isset( $this->headers[$name] ) )
        {
            if ( $returnAllValues === true )
            {
                return $this->headers[$name];
            }
            else if ( is_array( $this->headers[$name] ) )
            {
                // return only the first value in order to not break compatibility
                // see issue #14257
                return $this->headers[$name][0];
            }
            else
            {
                return $this->headers[$name];
            }
        }
        return '';
    }

    /**
     * Sets the header $name to the value $value and its charset to $charset.
     *
     * If the header is already set it will override the old value.
     *
     * Headers set should be folded at 76 or 998 characters according to
     * the folding rules described in RFC 2822.
     *
     * If $charset is specified, it is associated with the header $name. It
     * defaults to 'us-ascii' if not specified. The text in $value is encoded
     * with $charset after calling generateHeaders().
     *
     * Note: The header Content-Disposition will be overwritten by the
     * contents of the contentsDisposition property if set.
     *
     * @see generateHeaders()
     *
     * @param string $name
     * @param string $value
     * @param string $charset
     */
    public function setHeader( $name, $value, $charset = 'us-ascii' )
    {
        $this->headers[$name] = $value;
        $this->setHeaderCharset( $name, $charset );
    }

    /**
     * Adds the headers $headers.
     *
     * The headers specified in the associative array $headers will overwrite
     * any existing header values.
     *
     * The array $headers can have one of these 2 forms:
     *  - array( header_name => header_value ) - by default the 'us-ascii' charset
     *    will be associated with all headers
     *  - array( header_name => array( header_value, header_charset ) ) - if
     *    header_charset is missing it will default to 'us-ascii'
     *
     * Headers set should be folded at 76 or 998 characters according to
     * the folding rules described in RFC 2822.
     *
     * @param array(string=>mixed) $headers
     */
    public function setHeaders( array $headers )
    {
        foreach ( $headers as $key => $value )
        {
            if ( is_array( $value ) )
            {
                $this->headers[$key] = $value[0];
                $charset = isset( $value[1] ) ? $value[1] : 'us-ascii';
                $this->setHeaderCharset( $key, $charset );
            }
            else
            {
                $this->headers[$key] = $value;
                $this->setHeaderCharset( $key );
            }
        }
    }

    /**
     * Returns the headers set for this part as a RFC 822 string.
     *
     * Each header is separated by a line break.
     * This method does not add the required two lines of space
     * to separate the headers from the body of the part.
     *
     * It also encodes the headers (with the 'Q' encoding) if the charset
     * associated with the header is different than 'us-ascii' or if it
     * contains characters not allowed in mail headers.
     *
     * This function is called automatically by generate() and
     * subclasses can override this method if they wish to set additional
     * headers when the mail is generated.
     *
     * @see setHeader()
     *
     * @return string
     */
    public function generateHeaders()
    {
        // set content disposition header
        if ( $this->contentDisposition !== null &&
            ( $this->contentDisposition instanceof ezcMailContentDispositionHeader ) )
        {
            $cdHeader = $this->contentDisposition;
            $cd = "{$cdHeader->disposition}";
            if ( $cdHeader->fileName !== null )
            {
                $fileInfo = null;
                if ( $cdHeader->fileNameCharSet !== null )
                {
                    $fileInfo .= "*0*=\"{$cdHeader->fileNameCharSet}";
                    if ( $cdHeader->fileNameLanguage !== null )
                    {
                        $fileInfo .= "'{$cdHeader->fileNameLanguage}'";
                    }
                    else
                    {
                        // RFC 2184: the single quote delimiters MUST be present
                        // even when one of the field values is omitted
                        $fileInfo .= "''";
                    }
                }
                if ( $fileInfo !== null )
                {
                    $cd .= "; filename{$fileInfo}{$cdHeader->fileName}\"";
                }
                else
                {
                    $cd .= "; filename=\"{$cdHeader->fileName}\"";
                }
            }

            if ( $cdHeader->creationDate !== null )
            {
                $cd .= "; creation-date=\"{$cdHeader->creationDate}\"";
            }

            if ( $cdHeader->modificationDate !== null )
            {
                $cd .= "; modification-date=\"{$cdHeader->modificationDate}\"";
            }

            if ( $cdHeader->readDate !== null )
            {
                $cd .= "; read-date=\"{$cdHeader->readDate}\"";
            }

            if ( $cdHeader->size !== null )
            {
                $cd .= "; size={$cdHeader->size}";
            }

            foreach ( $cdHeader->additionalParameters as $addKey => $addValue )
            {
                $cd .="; {$addKey}=\"{$addValue}\"";
            }

            $this->setHeader( 'Content-Disposition', $cd );
        }

        // generate headers
        $text = "";
        foreach ( $this->headers->getCaseSensitiveArray() as $header => $value )
        {
            if ( is_array( $value ) )
            {
                $value = $value[0];
            }

            // here we encode every header, even the ones that we don't add to
            // the header set directly. We do that so that transports sill see
            // all the encoded headers which they then can use accordingly.
            $charset = $this->getHeaderCharset( $header );
            switch ( strtolower( $charset ) )
            {
                case 'us-ascii':
                    $value = ezcMailHeaderFolder::foldAny( $value );
                    break;

                case 'iso-8859-1': case 'iso-8859-2': case 'iso-8859-3': case 'iso-8859-4':
                case 'iso-8859-5': case 'iso-8859-6': case 'iso-8859-7': case 'iso-8859-8':
                case 'iso-8859-9': case 'iso-8859-10': case 'iso-8859-11': case 'iso-8859-12':
                case 'iso-8859-13': case 'iso-8859-14': case 'iso-8859-15' :case 'iso-8859-16':
                case 'windows-1250': case 'windows-1251': case 'windows-1252':
                case 'utf-8':
                    if ( strpbrk( $value, "\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff" ) === false )
                    {
                        $value = ezcMailHeaderFolder::foldAny( $value );
                        break;
                    }
                    // break intentionally missing

                default:
                    $preferences = array(
                        'input-charset' => $charset,
                        'output-charset' => $charset,
                        'line-length' => ezcMailHeaderFolder::getLimit(),
                        'scheme' => 'Q',
                        'line-break-chars' => ezcMailTools::lineBreak()
                    );
                    $value = iconv_mime_encode( 'dummy', $value, $preferences );
                    $value = substr( $value, 7 ); // "dummy: " + 1

                    // just to keep compatibility with code which might read
                    // the headers after generateHeaders() has been called
                    $this->setHeader( $header, $value, $charset );
                    break;
            }

            if ( in_array( strtolower( $header ), $this->excludeHeaders ) === false )
            {
                 $text .= "$header: $value" . ezcMailTools::lineBreak();
            }
        }

        return $text;
    }

    /**
     * The array $headers will be excluded when the headers are generated.
     *
     * @see generateHeaders()
     *
     * @param array(string) $headers
     */
    public function appendExcludeHeaders( array $headers )
    {
        $lowerCaseHeaders = array();
        foreach ( $headers as $header )
        {
            $lowerCaseHeaders[] = strtolower( $header );
        }
        $this->excludeHeaders = array_merge( $this->excludeHeaders, $lowerCaseHeaders );
    }

    /**
     * Returns the body of this part as a string.
     *
     * This method is called automatically by generate() and subclasses must
     * implement it.
     *
     * @return string
     */
    abstract public function generateBody();

    /**
     * Returns the complete mail part including both the header and the body
     * as a string.
     *
     * @return string
     */
    public function generate()
    {
        return $this->generateHeaders() . ezcMailTools::lineBreak() . $this->generateBody();
    }

    /**
     * Returns the charset registered for the header $name.
     *
     * @param string $name
     * @return string
     */
    protected function getHeaderCharset( $name )
    {
        if ( isset( $this->headerCharsets[$name] ) )
        {
            return $this->headerCharsets[$name];
        }

        // if no charset is set then return 'us-ascii'
        return 'us-ascii';
    }

    /**
     * Sets the charset of the header $name to $value.
     *
     * If $value is not specified it defaults to 'us-ascii'.
     *
     * @param string $name
     * @param string $value
     */
    protected function setHeaderCharset( $name, $value = 'us-ascii' )
    {
        $this->headerCharsets[$name] = $value;
    }
}
?>
