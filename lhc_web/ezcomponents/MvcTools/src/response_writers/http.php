<?php
/**
 * File containing the ezcMvcHttpResponseWriter class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Request parser that uses HTTP headers to populate an ezcMvcRequest object.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
class ezcMvcHttpResponseWriter extends ezcMvcResponseWriter
{
    /**
     * Contains the response struct
     *
     * @var ezcMvcResponse
     */
    protected $response;

    /**
     * Contains an array of header name to value mappings
     *
     * @var array(string=>string)
     */
    public $headers;

    /**
     * Creates a new ezcMvcHttpResponseWriter class to write $response
     *
     * @param ezcMvcResponse $response
     */
    public function __construct( ezcMvcResponse $response )
    {
        $this->response = $response;
        $this->headers = array();
    }

    /**
     * Takes the raw protocol depending response body, and the protocol
     * abstract response headers and forges a response to the client. Then it sends
     * the assembled response to the client.
     */
    public function handleResponse()
    {
        // process all headers
        $this->processStandardHeaders();
        if ( $this->response->cache instanceof ezcMvcResultCache )
        {
            $this->processCacheHeaders();
        }
        if ( $this->response->content instanceof ezcMvcResultContent )
        {
            $this->processContentHeaders();
        }

        // process the status headers through objects
        if ( $this->response->status instanceof ezcMvcResultStatusObject )
        {
            $this->response->status->process( $this );
        }

        // automatically add content-length header
        $this->headers['Content-Length'] = strlen( $this->response->body );

        // write output
        foreach ( $this->headers as $header => $value )
        {
            header( "$header: $value" );
        }
        // do cookies
        foreach ( $this->response->cookies as $cookie )
        {
            $this->processCookie( $cookie );
        }
        echo $this->response->body;
    }

    /**
     * Takes a $cookie and uses PHP's setcookie() function to add cookies to the output stream.
     *
     * @param ezcMvcResultCookie $cookie
     */
    private function processCookie( ezcMvcResultCookie $cookie )
    {
        $args = array();
        $args[] = $cookie->name;
        $args[] = $cookie->value;
        if ( $cookie->expire instanceof DateTime )
        {
            $args[] = $cookie->expire->format( 'U' );
        }
        else
        {
            $args[] = null;
        }
        $args[] = $cookie->domain;
        $args[] = $cookie->path;
        $args[] = $cookie->secure;
        $args[] = $cookie->httpOnly;
        call_user_func_array( 'setcookie', $args );
    }

    /**
     * Checks whether there is a DateTime object in $obj->$prop and sets a header accordingly.
     *
     * @param Object $obj
     * @param string $prop
     * @param string $headerName
     * @param bool   $default
     */
    private function doDate( $obj, $prop, $headerName, $default = false )
    {
        if ( $obj->$prop instanceof DateTime )
        {
            $headerDate = clone $obj->$prop;
            $headerDate->setTimezone( new DateTimeZone( "UTC" ) );
            $this->headers[$headerName] = $headerDate->format( 'D, d M Y H:i:s \G\M\T' );
            return;
        }

        if ( $default )
        {
            $headerDate = new DateTime( "UTC" );
            $this->headers[$headerName] = $headerDate->format( 'D, d M Y H:i:s \G\M\T' );
        }
    }

    /**
     * Processes the standard headers that are not subdivided into other structs.
     */
    protected function processStandardHeaders()
    {
        $res = $this->response;

        // generator
        $this->headers['X-Powered-By'] = $res->generator !== ''
            ? $res->generator
            : "eZ Components MvcTools";

        $this->doDate( $res, 'date', 'Date', true );
    }

    /**
     * Processes the caching related headers.
     */
    protected function processCacheHeaders()
    {
        $cache = $this->response->cache;

        if ( $cache->vary )
        {
            $this->headers['Vary'] = $cache->vary;
        }
        $this->doDate( $cache, 'expire', 'Expires' );
        if ( count( $cache->controls ) )
        {
            $this->headers['Cache-Control'] = join( ', ', $cache->controls );
        }
        if ( $cache->pragma )
        {
            $this->headers['Pragma'] = $cache->pragma;
        }
        $this->doDate( $cache, 'lastModified', 'Last-Modified' );
    }

    /**
     * Processes the content type related headers.
     */
    protected function processContentHeaders()
    {
        $content = $this->response->content;
        $defaultContentType = 'text/html';

        if ( $content->language )
        {
            $this->headers['Content-Language'] = $content->language;
        }
        if ( $content->type || $content->charset )
        {
            $contentType = $content->type ? $content->type : $defaultContentType;
            if ( $content->charset )
            {
                $contentType .= '; charset=' . $content->charset;
            }
            $this->headers['Content-Type'] = $contentType;
        }
        if ( $content->encoding )
        {
            $this->headers['Content-Encoding'] = $content->encoding;
        }

        if ( $content->disposition instanceof ezcMvcResultContentDisposition )
        {
            $this->processContentDispositionHeaders( $content->disposition );
        }
    }

    /**
     * Processed the content disposition related headers.
     *
     * See http://tools.ietf.org/html/rfc2183#section-2, but implemented with limitations.
     *
     * @param ezcMvcResultContentDisposition $disp
     */
    protected function processContentDispositionHeaders( ezcMvcResultContentDisposition $disp )
    {
        // type
        $value = $disp->type;

        // filename
        if ( $disp->filename !== null )
        {
            $value .= "; filename";
            if ( strpbrk( $disp->filename,
                "\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff" ) === false )
            {
                // case 1: ASCII characters only
                if ( strpbrk( $disp->filename, '\(\)<>@,;:\\"/\[\]?= ' ) === false )
                {
                    // case 1a: no tspecials
                    $value .= '=' . $disp->filename;
                }
                else
                {
                    // case 1b: with tspecials
                    $value .= '="' . str_replace( '"', '\"', $disp->filename ) . '"';
                }
            }
            else
            {
                // case 2: non-ASCII characters (and thus UTF-8 encoded)
                $value .= "*=utf-8''" . urlencode( $disp->filename );
            }
        }

        // dates
        if ( $disp->creationDate !== null )
        {
            $value .= '; creation-date="' . $disp->creationDate->format( DateTime::RFC2822 ) . '"';
        }
        if ( $disp->modificationDate !== null )
        {
            $value .= '; modification-date="' . $disp->modificationDate->format( DateTime::RFC2822 ) . '"';
        }
        if ( $disp->readDate !== null )
        {
            $value .= '; read-date="' . $disp->readDate->format( DateTime::RFC2822 ) . '"';
        }

        // size
        if ( $disp->size !== null )
        {
            $value .= "; size=" . (int) $disp->size;
        }

        $this->headers['Content-Disposition'] = $value;
    }
}
?>
