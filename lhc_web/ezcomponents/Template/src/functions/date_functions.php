<?php
/**
 * File containing the ezcTemplateDateFunctions class
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
class ezcTemplateDateFunctions extends ezcTemplateFunctions
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
            case "date_format":
                return array( array( "%format", "%time" ), self::functionCall( "date_format", array( "%time", "%format" ) ) );
            // date( $format, $timestamp )
            case "date_format_timestamp":
                return array( array( "%format", "[%timestamp]" ), self::functionCall( "date", array( "%format", "[%timestamp]" ) ) );
            // time()
            case "date_current_timestamp":
                return array( array(), self::functionCall( "time", array() ) );

        }

        return null;
    }
}
?>
