<?php
/**
 * File containing the ezcTemplateWebFunctions class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateWebFunctions extends ezcTemplateFunctions
{
    /**
     * Translates a function used in the Template language to a PHP function call.  
     * The function call is represented by an array with three elements:
     *
     * 1. The return typehint. Is it an array, a non-array, or both.
     * 2. The parameter input definition.
     * 3. The AST nodes.
     *
     * @param string $functionName
     * @param array(ezcTemplateAstNode) $parameters
     * @return array(mixed)
     */
    public static function getFunctionSubstitution( $functionName, $parameters )
    {
        switch ( $functionName )
        {
            // url_encode( $s )
            case "url_encode":
                return array( array( "%string" ), self::functionCall( "urlencode", array( "%string" ) ) );
            
            // url_decode( $s )
            case "url_decode":
                return array( array( "%string" ), self::functionCall( "urldecode", array( "%string" ) ) );
            
            // url_parameters_build( $params, [$prefix] )
            case "url_parameters_build":
                return array( array( "%params", "[%prefix]" ), self::functionCall( "http_build_query", array( "%params", "[%prefix]" ) ) );

            // url_build( $data )
            case "url_build":
                return array( array( "%data" ), self::functionCall( "ezcTemplateWeb::url_build", array( "%data" ) ) );
            
            // url_parse( $s )
            case "url_parse":
                return array( array( "%string" ), self::functionCall( "parse_url", array( "%string" ) ) );

        }

        return null;
    }
}
?>
