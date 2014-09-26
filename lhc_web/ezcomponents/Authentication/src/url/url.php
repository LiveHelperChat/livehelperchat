<?php
/**
 * File containing the ezcAuthenticationUrl class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Class which provides a methods for handling URLs.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationUrl
{
    /**
     * Normalizes the provided URL.
     *
     * The operations performed on the provided URL:
     *  - trim
     *  - add 'http://' in front if it is missing
     *
     * @param string $url The URL to normalize
     * @return string
     */
    public static function normalize( $url )
    {
        $url = trim( $url );
        if ( strpos( $url, '://' ) === false )
        {
            $url = 'http://' . $url;
        }

        return $url;
    }

    /**
     * Appends a query value to the provided URL and returns the complete URL.
     *
     * @param string $url The URL to append a query value to
     * @param string $key The query key to append to the URL
     * @param string $value The query value to append to the URL
     * @return string
     */
    public static function appendQuery( $url, $key, $value )
    {
        $parts = parse_url( $url );
        if ( isset( $parts['query'] ) )
        {
            $parts['query'] = self::parseQueryString( $parts['query'] );
        }

        $parts['query'][$key] = $value;
        return self::buildUrl( $parts );
    }

    /**
     * Fetches the value of key $key from the query of the provided URL.
     *
     * @param string $url The URL from which to fetch the query value
     * @param string $key The query key for which to get the value
     * @return true
     */
    public static function fetchQuery( $url, $key )
    {
        $parts = parse_url( $url );
        if ( isset( $parts['query'] ) )
        {
            $parts['query'] = self::parseQueryString( $parts['query'] );
            return ( isset( $parts['query'][$key] ) ) ? $parts['query'][$key] : null;
        }
        return null;
    }

    /**
     * Creates a string URL from the provided $parts array.
     *
     * The format of the $parts array is similar to the one returned by
     * parse_url(), with the 'query' part as an array(key=>value) (obtained with
     * the function parse_str()).
     *
     * @param array(string=>mixed) $parts The parts of the URL
     * @return string
     */
    public static function buildUrl( array $parts )
    {
        $path = ( isset( $parts['path'] ) ) ? $parts['path'] : '/';
        $query = ( isset( $parts['query'] ) ) ? '?' . http_build_query( $parts['query'] ) : '';
        $fragment = ( isset( $parts['fragment'] ) ) ? '#' . $parts['fragment'] : '';

        if ( isset( $parts['host'] ) )
        {
            $host = $parts['host'];
            $scheme = ( isset( $parts['scheme'] ) ) ? $parts['scheme'] . '://' : 'http://';
            $port = ( isset( $parts['port'] ) ) ? ':' . $parts['port'] : '';
            $result = "{$scheme}{$host}{$port}{$path}{$query}{$fragment}";
        }
        else
        {
            $result = "{$path}{$query}{$fragment}";
        }

        return $result;
    }

    /**
     * Parses the provided string and returns an associative array structure.
     *
     * It implements the functionality of the PHP function parse_str(), but
     * without converting dots to underscores in parameter names.
     *
     * Example:
     * <code>
     * $str = 'foo[]=bar&openid.nonce=123456';
     *
     * parse_str( $str, $params );
     * $params = ezcUrlTools::parseQuery( $str );
     * </code>
     *
     * In the first case (parse_str()), $params will be:
     * <code>
     * array( 'foo' => array( 'bar' ), 'openid_nonce' => '123456' );
     * </code>
     *
     * In the second case (ezcUrlTools::parseQueryString()), $params will be:
     * <code>
     * array( 'foo' => array( 'bar' ), 'openid.nonce' => '123456' );
     * </code>
     *
     * The same function is defined in {@link ezcUrlTools} in the Url component.
     *
     * @param array(string=>mixed) $str The string to parse
     * @return array(string=>mixed)
     */
    public static function parseQueryString( $str )
    {
        $result = array();

        // $params will be returned, but first we have to ensure that the dots
        // are not converted to underscores
        parse_str( $str, $params );

        $separator = ini_get( 'arg_separator.input' );
        if ( empty( $separator ) )
        {
            $separator = '&';
        }

        // go through $params and ensure that the dots are not converted to underscores
        $args = explode( $separator, $str );
        foreach ( $args as $arg )
        {
            $parts = explode( '=', $arg, 2 );
            if ( !isset( $parts[1] ) )
            {
                $parts[1] = null;
            }

            if ( substr_count( $parts[0], '[' ) === 0 )
            {
                $key = $parts[0];
            }
            else
            {
                $key = substr( $parts[0], 0, strpos( $parts[0], '[' ) );
            }

            $paramKey = str_replace( '.', '_', $key );
            if ( isset( $params[$paramKey] ) && strpos( $paramKey, '_' ) !== false )
            {
                $newKey = '';
                for ( $i = 0; $i < strlen( $paramKey ); $i++ )
                {
                    $newKey .= ( $paramKey{$i} === '_' && $key{$i} === '.' ) ? '.' : $paramKey{$i};
                }

                $keys = array_keys( $params );
                if ( ( $pos = array_search( $paramKey, $keys ) ) !== false )
                {
                    $keys[$pos] = $newKey;
                }
                $values = array_values( $params );
                $params = array_combine( $keys, $values );
            }
        }

        return $params;
    }

    /**
     * Retrieves the headers and contents of $url using the HTTP method
     * $method (GET, HEAD, POST), with an optional Accept $type
     * (default 'text/html').
     *
     * @param string $url Then URL to retrieve
     * @param string $method HTTP method to use, default GET
     * @param string $type Accept type to use, eg. 'application/xrds+xml'
     *
     */
    public static function getUrl( $url, $method = 'GET', $type = 'text/html' )
    {
        $opts = array( 'http' =>
            array(
                'method'  => $method,
                'header'  => "Accept: {$type}"
            )
        );

        $context  = stream_context_create( $opts );

        if ( !$file = @fopen( $url, 'r', false, $context ) )
        {
            throw new ezcAuthenticationOpenidConnectionException( $url, $type );
        }

        // get the HTTP headers
        $metadata = stream_get_meta_data( $file );
        if ( array_key_exists( 'headers', $metadata['wrapper_data'] ) )
        {
            // for php compiled with --with-curlwrappers
            $headers = implode( "\n", $metadata['wrapper_data']['headers'] );
        }
        else
        {
            // for php compiled without --with-curlwrappers
            $headers = implode( "\n", $metadata['wrapper_data'] );
        }

        // get the contents of the $url
        $contents = file_get_contents( $url, false, $context );

        // append the contents to the headers
        return $headers . "\n" . $contents;
    }
}
?>
