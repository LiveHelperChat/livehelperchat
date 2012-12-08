<?php
/**
 * File containing the ezcTemplateMathFunctions class
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
class ezcTemplateMathFunctions extends ezcTemplateFunctions
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
            // max( $v1 , $v2 [, ...] )::
            // max( $v1 , $v2 [, ...] )
            case "math_max": return array( array( "%val", "%..." ), 
                self::functionCall( "max", array( "%val", "%..." ) ) );

            // min( $v1 , $v2 [, ...] )::
            // min( $v1 , $v2 [, ...] )
            case "math_min": return array( array( "%val", "%..." ), 
                self::functionCall( "min", array( "%val", "%..." ) ) );

            // abs( $v )::
            // abs( $v )
            case "math_abs": return array( array( "%val" ), 
                self::functionCall( "abs", array( "%val" ) ) );

            // ceil( $v )::
            // ceil( $v )
            case "math_ceil": return array( array( "%val" ), 
                self::functionCall( "ceil", array( "%val" ) ) );

            // floor( $v )::
            // floor( $v )
            case "math_floor": return array( array( "%val" ), 
                self::functionCall( "floor", array( "%val" ) ) );

            // round( $v )::
            // round( $v )
            case "math_round": return array( array( "%val" ), 
                self::functionCall( "round", array( "%val" ) ) );

            // sqrt( $v )::
            // sqrt( $v )
            case "math_sqrt": return array( array( "%val" ), 
                self::functionCall( "sqrt", array( "%val" ) ) );

            // exp( $arg )::
            // exp( $arg )
            case "math_exp": return array( array( "%arg" ), 
                self::functionCall( "exp", array( "%arg" ) ) );

            // pow( $base, $exp )::
            // pow( $base, $exp )
            case "math_pow": return array( array( "%base", "%exp" ), 
                self::functionCall( "pow", array( "%base", "%exp" ) ) );

            // log( $arg, $base )::
            // log( $arg, $base )
            case "math_log": return array( array( "%arg", "%base" ), 
                self::functionCall( "log", array( "%arg", "%base" ) ) );

            // log10( $arg )::
            // log10( $arg )
            case "math_log10": return array( array( "%arg" ), 
                self::functionCall( "log10", array( "%arg" ) ) );

            // float_mod( $v )::
            // fmod( $v )
            case "math_float_mod": return array( array( "%x", "%y" ), 
                self::functionCall( "fmod", array( "%x", "%y" ) ) );

            // rand( $min, $max )::
            // mt_rand( $min, $max )
            case "math_rand": return array( array( "%min", "%max" ), 
                self::functionCall( "mt_rand", array( "%min", "%max" ) ) );

            // pi()::
            // pi()
            case "math_pi": return array( array( ), 
                self::functionCall( "pi", array() ) );

            // is_finite( $v )::
            // is_finite( $v )
            case "math_is_finite": return array( array( "%val" ), 
                self::functionCall( "is_finite", array( "%val") ) );

            // is_infinite( $v )::
            // is_infinite( $v )
            case "math_is_infinite": return array( array( "%val" ), 
                self::functionCall( "is_infinite", array( "%val") ) );

            // is_nan( $v )::
            // is_nan( $v )
            case "math_is_nan": return array( array( "%val" ), 
                self::functionCall( "is_nan", array( "%val") ) );

            // bin_to_dec( $s )::
            // bindec( $s )
            case "math_bin_to_dec": return array( array( "%string" ), 
                self::functionCall( "bindec", array( "%string") ) );

            // hex_to_dec( $s )::
            // hexdec( $s )
            case "math_hex_to_dec": return array( array( "%string" ), 
                self::functionCall( "hexdec", array( "%string") ) );

            // oct_to_dec( $s )::
            // octdec( $s )
            case "math_oct_to_dec": return array( array( "%string" ), 
                self::functionCall( "octdec", array( "%string") ) );

            // dec_to_bin( $v )::
            // decbin( $v )
            case "math_dec_to_bin": return array( array( "%val" ), 
                self::functionCall( "decbin", array( "%val") ) );

            // dec_to_hex( $v )::
            // dechex( $v )
            case "math_dec_to_hex": return array( array( "%val" ), 
                self::functionCall( "dechex", array( "%val") ) );

            // dec_to_oct( $v )::
            // decoct( $v )
            case "math_dec_to_oct": return array( array( "%val" ), 
                self::functionCall( "decoct", array( "%val") ) );
        }

        return null;
    }
}
?>
