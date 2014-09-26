<?php
/**
 * File containing the ezcUrlTools class.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.2.2
 * @filesource
 * @package Url
 */

/**
 * Class providing methods for URL parsing.
 *
 * Static methods contained in this class:
 *  - parseQueryString() - It implements the functionality of the PHP function
 *    parse_str(), but without converting dots to underscores in parameter names.
 *  - getCurrentUrl() - Returns the current URL as a string from the provided
 *    array (by default $_SERVER).
 *
 * @package Url
 * @version 1.2.2
 */
class ezcUrlTools
{
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
     * $params = ezcUrlTools::parseQueryString( $str );
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
     * Returns the current URL as a string from the array $source
     * (by default $_SERVER).
     *
     * The following fields are used in building the URL:
     *  - HTTPS - determines the scheme ('http' or 'https'). 'https' only if
     *    the 'HTTPS' field is set or if it is 'on' or '1'
     *  - SERVER_NAME
     *  - SERVER_PORT - determines if port is default (80 = do not include port)
     *    or not default (other than 80 = include port)
     *  - REQUEST_URI
     *
     * For example, if $_SERVER has these fields:
     * <code>
     * $_SERVER = array(
     *     'HTTPS' => '1',
     *     'SERVER_NAME' => 'www.example.com',
     *     'SERVER_PORT' => 80,
     *     'REQUEST_URI' => '/index.php'
     * );
     * </code>
     *
     * Then by calling this function (with no parameters), this URL will be
     * returned: 'http://www.example.com/index.php'.
     *
     * The source of the URL parts can be changed to be other than $_SERVER by
     * specifying an array parameter when calling this function.
     *
     * @todo check if REQUEST_URI works in Windows + IIS.
     *        - Use PHP_SELF instead?
     *        - Or use SCRIPT_NAME + QUERY_STRING?
     *        - Or even use an ISAPI filter?
     * @todo check for proxy servers
     *        - Use $_SERVER['HTTP_X_FORWARDED_SERVER']?
     *
     * @param array(string=>mixed) $source The default array source, default $_SERVER
     * @return string
     */
    public static function getCurrentUrl( array $source = null )
    {
        if ( $source === null )
        {
            $source = $_SERVER;
        }

        $url = '';
        if ( isset( $source['HTTPS'] ) && 
             ( strtolower( $source['HTTPS'] ) === 'on' || $source['HTTPS'] === '1' ) )
        {
            $url .= 'https://';
        }
        else
        {
            $url .= 'http://';
        }

        $url .= isset( $source['SERVER_NAME'] ) ? $source['SERVER_NAME'] : null;
        if ( isset( $source['SERVER_PORT'] ) && $source['SERVER_PORT'] != 80 )
        {
            $url .= ":{$source['SERVER_PORT']}";
        }
        $url .= isset( $source['REQUEST_URI'] ) ? $source['REQUEST_URI'] : null;
        return $url;
    }
}
?>
