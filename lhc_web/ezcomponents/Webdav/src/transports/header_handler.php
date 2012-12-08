<?php
/**
 * File containing the ezcWebdavHeaderHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * An instance of this class manages header parsing and handling.
 *
 * An object of this class takes care about headers in {@link
 * ezcWebdavTransport}. It is responsible for parsing incoming headers and
 * serialize outgoing ones. Like for the {@link ezcWebdavPropertyHandler}, the
 * instance of this class that is used in the current transport layer must be
 * accessable for plugins.
 *
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavHeaderHandler
{
    /**
     * Map of regular header names to $_SERVER keys.
     *
     * @var array(string=>string)
     */
    protected $headerMap = array(
        'Authorization' => array(
            'HTTP_AUTHORIZATION',
            'PHP_AUTH_DIGEST',
            'PHP_AUTH_USER',
        ),
        'Content-Length' => array( 
            'HTTP_CONTENT_LENGTH',
            'CONTENT_LENGTH',
        ),
        'Content-Type'   => array( 
            'CONTENT_TYPE',
        ),
        'Depth'          => array( 
            'HTTP_DEPTH',
        ),
        'Destination'    => array( 
            'HTTP_DESTINATION',
        ),
        'If-Match'        => array(
            'HTTP_IF_MATCH'
        ),
        'If-None-Match'        => array(
            'HTTP_IF_NONE_MATCH'
        ),
        'Overwrite'      => array(
            'HTTP_OVERWRITE',
        ),
        'Server'         => array(
            'SERVER_SOFTWARE',
        ),
    );

    /**
     * List of headers that should be attempted to parse for every request.
     * 
     * @var array(string)
     */
    protected $defaultHeaders = array(
        'Authorization',
        'If-Match',
        'If-None-Match',
    );

    /**
     * Returns an array with the given headers.
     *
     * Checks for the availability of headers in $headerNamess, given as an
     * array of header names, and parses them according to their format.
     *
     * By default, this method parses an additional set of default headers
     * (e.g. If-Match and If-None-Match). This can be avoided by setting the
     * optional $defaultHeaders parameter to false.
     *
     * The returned array can be used with {@link ezcWebdavRequest->setHeaders()}.
     * 
     * @param array(string) $headerNames 
     * @return array(string=>mixed)
     * @param bool $defaultHeaders
     *
     * @throws ezcWebdavUnknownHeaderException
     *         if a header requested in $headerNames is not known in {@link
     *         $headerNames}.
     */
    public function parseHeaders( array $headerNames = array(), $defaultHeaders = true )
    {
        if ( $defaultHeaders )
        {
            $headerNames = array_unique(
                array_merge( $headerNames, $this->defaultHeaders )
            );
        }

        $resultHeaders = array();
        foreach ( $headerNames as $headerName )
        {
            if ( ( $value = $this->parseHeader( $headerName ) ) !== null )
            {
                $resultHeaders[$headerName] = $value;
            }
        }

        // Ignore If-Match and If-None-Match if both are set
        // @todo: RFC conform, also clients conform?
        if ( isset( $resultHeaders['If-Match'] ) && isset( $resultHeaders['If-None-Match'] ) )
        {
            unset( $resultHeaders['If-Match'] );
            unset( $resultHeaders['If-None-Match'] );
        }

        return $resultHeaders;
    }

    /**
     * Parses a single header.
     *
     * Retrieves a $headerName and returns the processed value for it, if it
     * does exist. If the requested header is unknown, a {@link
     * ezcWebdavUnknownHeaderException} is thrown. If the requested header is
     * not present in {@link $_SERVER} null is returned.
     * 
     * @param string $headerName 
     * @return mixed
     */
    public function parseHeader( $headerName )
    {
        if ( !isset( $this->headerMap[$headerName] ) )
        {
            throw new ezcWebdavUnknownHeaderException( $headerName );
        }

        foreach ( $this->headerMap[$headerName] as $serverHeaderName )
        {
            if ( isset( $_SERVER[$serverHeaderName] ) )
            {
                return $this->processHeader( $headerName, $_SERVER[$serverHeaderName], $serverHeaderName );
            }
        }

        // Default to null, if header is not available
        return null;
    }

    /**
     * Processes a single header value.
     *
     * Takes the $headerName and $value of a header and parses the value
     * accordingly, * if necessary. Returns the parsed or unmanipuled result. The
     * $serverHeaderName parameter contains the key that was used to extract the
     * header from the $_SERVER array.
     * 
     * @param string $headerName 
     * @param string $value 
     * @param string $serverHeaderName
     * @return mixed
     */
    protected function processHeader( $headerName, $value, $serverHeaderName )
    {
        switch ( $headerName )
        {
            case 'Authorization':
                $value = $this->parseAuthorizationHeader( $value, $serverHeaderName );
                break;
            case 'Depth':
                $value = $this->parseDepthHeader( $value );
                break;
            case 'Destination':
                $value = ezcWebdavServer::getInstance()
                    ->pathFactory
                    ->parseUriToPath( $value );
                break;
            case 'If-Match':
            case 'If-None-Match':
                $value = $this->parseIfMatchHeader( $value );
                break;
            default:
                // @todo Add plugin hook
        }
        return $value;
    }

    /**
     * Parses the Authorization header.
     *
     * Takes the string value of the Authorization header and parses it
     * according to the Basic authentication scheme. The return value is an
     * struct of either of the following classes:
     * 
     * <ul>
     *   <li>{@link ezcWebdavDigestAuth}</li>
     *   <li>{@link ezcWebdavBasicAuth}</li>
     * </ul>    
     *
     * In case the header is provided but does not contain a parseable value,
     * the user and pass fields are null. The $serverHeaderName parameter
     * indicates, which key from the $_SERVER array was used to extract the
     * header.
     *
     * @param string $value 
     * @param string $serverHeaderName
     * @return array(string)
     */
    protected function parseAuthorizationHeader( $value, $serverHeaderName )
    {
        switch ( $serverHeaderName )
        {
            case 'PHP_AUTH_DIGEST':
                return $this->parseDigestAuthorizationHeader( $value );
            
            case 'PHP_AUTH_USER':
                return new ezcWebdavBasicAuth(
                    $value, $_SERVER['PHP_AUTH_PW']
                );

            case 'HTTP_AUTHORIZATION':
            default:
                if ( substr( $value, 0, 5 ) === 'Basic' )
                {
                    return $this->parseBasicAuthorizationHeader(
                        trim( substr( $value, 6 ) )
                    );
                }
                elseif ( substr( $value, 0, 6 ) === 'Digest' )
                {
                    return $this->parseDigestAuthorizationHeader(
                        trim( substr( $value, 7 ) )
                    );
                }
                
                // In case of an unknown auth method.
                return new ezcWebdavAnonymousAuth();
        }
    }

    /**
     * Parses the basic authorization header.
     *
     * Returns a struct of type {@link ezcWebdavBasicAuth}, containing the
     * parsed values, or with empty username and password, if the header could
     * not be parsed.
     * 
     * @param string $value 
     * @return ezcWebdavBasicAuth
     */
    protected function parseBasicAuthorizationHeader( $value )
    {
        $res = new ezcWebdavAnonymousAuth();
        $credentials = explode(
            ':',
            base64_decode( $value ),
            2
        );
        if ( count( $credentials ) > 1 )
        {
            // Credentials parsed successfully.
            $res = new ezcWebdavBasicAuth( $credentials[0], $credentials[1] );
        }
        return $res;
    }

    /**
     * Parses the digest authorization header.
     *
     * Returns an authorization credential struct of type {@link
     * ezcWebdavDigestAuth}, containing the parsed data, or an instance of {@link
     * ezcWebdavBasicAuth} with empty username and password, if parsing failed.
     * 
     * @param string $value 
     * @return ezcWebdavDigestAuth|ezcWebdavBasicAuth
     */
    protected function parseDigestAuthorizationHeader( $value )
    {
        // Default, if header cannot be parsed
        $res = new ezcWebdavAnonymousAuth();

        // Minimum 6 values, otherwise incorrect
        if ( preg_match_all( '((\w+)=(?:"([^"]*)"|([A-Za-z0-9]+)))', $value, $matches, PREG_SET_ORDER ) > 5 )
        {
            $oldRes = $res;
            $res    = new ezcWebdavDigestAuth( $_SERVER['REQUEST_METHOD'] );
            foreach ( $matches as $matchSet )
            {
                switch ( $matchSet[1] )
                {
                    case 'username':
                    case 'realm':
                    case 'nonce':
                    case 'uri':
                    case 'response':
                    case 'algorithm':
                    case 'opaque':
                        $res->$matchSet[1] = $matchSet[2];
                        break;
                    // Ususally clients should not quote qop and nc, however, we check is some do
                    case 'qop':
                        $res->qualityOfProtection = !empty( $matchSet[2] ) ? $matchSet[2] : $matchSet[3];
                        break;
                    case 'nc':
                        $res->nonceCount = !empty( $matchSet[2] ) ? $matchSet[2] : $matchSet[3];
                        break;
                    case 'cnonce':
                        $res->clientNonce = $matchSet[2];
                        break;
                    // default:
                    // ignore
                }
            }
            // Check for anonymous auth
            if ( $res->username === '' )
            {
                $res = $oldRes;
            }
        }
        return $res;
    }

    /**
     * Parses the Depth header.
     *
     * Parses the values '0', '1' and 'infinity' into the corresponding
     * constants:
     *
     * <ul>
     *  <li>{@linkezcWebdavRequest::DEPTH_ZERO}</li>
     *  <li>{@linkezcWebdavRequest::DEPTH_ONE}</li>
     *  <li>{@linkezcWebdavRequest::DEPTH_INFINITY}</li>
     * </ul>
     *
     * If the header contains a different value it is left as is.
     * 
     * @param string $value 
     * @return int|string
     */
    protected function parseDepthHeader( $value )
    {
        switch ( trim( $value ) )
        {
            case '0':
                $value = ezcWebdavRequest::DEPTH_ZERO;
                break;
            case '1':
                $value = ezcWebdavRequest::DEPTH_ONE;
                break;
            case 'infinity':
                $value = ezcWebdavRequest::DEPTH_INFINITY;
                break;
            // No default. Header stays as is, if not matched
        }
        return $value;
    }

    /**
     * Parses the If-Match and If-None-Match headers.
     *
     *
     * We do not pay attention to weak entity tags (prefixed by W\), since our
     * backends don't make use of such tags. If backends want to provide weak
     * entity tags, they still might do so.
     * 
     * @param string $value 
     * @return array
     *
     * @todo Do we need to provide support for weak entity tags?
     */
    protected function parseIfMatchHeader( $value )
    {
        // Special case
        if ( trim( $value ) === '*' )
        {
            return true;
        }

        $etags  = array();

        $index      = 0;
        $length     = strlen( $value );
        $inTag      = false;
        $currentTag = '';

        while ( $index < $length )
        {
            if ( !$inTag )
            {
                if ( $value[$index] === '"' )
                {
                    $inTag = true;
                }
            }
            else
            {
                if ( $value[$index] === '"' )
                {
                    $etags[]    = $currentTag;
                    $currentTag = '';
                    $inTag      = false;
                }
                else
                {
                    $currentTag .= $value[$index];
                }
            }
            ++$index;
        }

        return $etags;
    }
}

?>
