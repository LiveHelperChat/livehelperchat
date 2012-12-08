<?php
/**
 * File containing the ezcTemplateStringFunctions class
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
class ezcTemplateStringFunctions extends ezcTemplateFunctions
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
            // str_replace( $sl, $index, $len, $sr )
            // substr( $sl, 0, $index ) . $sr . substr( $sl, $index + $len );
            case "str_replace": 
                return array( ezcTemplateAstNode::TYPE_VALUE, array( "%left", "%index", "%length", "%right" ), 
                   self::concat( 
                       self::functionCall( "substr", array( "%left", self::value( 0 ), "%index" ) ),
                       self::concat( 
                           "%right", 
                           self::functionCall( 
                               "substr", 
                               array( "%left", array( "ezcTemplateAdditionOperatorAstNode", array( "%index", "%length" ) ) ) 
                           ) 
                       ) 
                    )
                 ); 


            // str_remove( $s, $index, $len ) 
            // substr( $s, 0, $index ) . substr( $s, $index + $len );
             case "str_remove": 
                 return array( ezcTemplateAstNode::TYPE_VALUE,  array( "%string", "%index", "%length" ), 
                   self::concat( 
                       self::functionCall( "substr", array( "%string", self::value( 0 ), "%index" ) ),
                       self::functionCall( "substr", array( "%string", array( "ezcTemplateAdditionOperatorAstNode", array( "%index", "%length" ) ) ) 
                           ) 
                        ) 
                     );
  
            // string str_chop( $s, $len ) ( QString::chop ):
            // substr( $s, 0, strlen( $string ) - $len );
             case "str_chop": 
                return array( ezcTemplateAstNode::TYPE_VALUE,  array( "%string", "%length" ), 
                       self::functionCall( "substr", array( 
                           "%string", 
                           self::value( 0 ), 
                           array( "ezcTemplateSubtractionOperatorAstNode", array( self::functionCall( "strlen", array( "%string" ) ), "%length" ) )
                       )
                   ) );
                       
            // string str_chop_front( $s, $len )
            // substr( $s, $len );
             case "str_chop_front": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "%length" ), self::functionCall( "substr", array( "%string", "%length" ) ) );

             case "str_append": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%left", "%right" ), self::concat( "%left", "%right" ) );

             case "str_prepend": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%left", "%right"), self::concat( "%right", "%left" ) );

            // str_compare( $sl, $sr )
            // strcmp( $sl, $sr );
            case "str_compare": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%left", "%right"), self::functionCall( "strcmp", array( "%left", "%right" ) ) );

            // str_nat_compare( $sl, $sr )
            // strnatcmp( $sl, $sr );
            case "str_nat_compare": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%left", "%right"), self::functionCall( "strnatcmp", array( "%left", "%right" ) ) );

            // str_contains( $sl, $sr ) ( QString::compare )::
            // strpos( $sl, $sr ) !== false 
            case "str_contains": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%left", "%right" ), 
                array( "ezcTemplateNotIdenticalOperatorAstNode", 
                array( self::functionCall( "strpos", array( "%left", "%right" ) ), self::value( false ) ) ) );

            // str_len( $s )
            // strlen( $s )
            case "str_len": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), self::functionCall( "strlen", array( "%string" ) ) );

            // str_left( $s, $len )
            // substr( $s, 0, $len )
            case "str_left": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "%length"), self::functionCall( "substr", array( "%string", self::value( 0 ), "%length" ) ) );

            // str_starts_with( $sl, $sr )
            // strpos( $sl, $sr ) === 0
            case "str_starts_with": return array(
                ezcTemplateAstNode::TYPE_VALUE,  
                array( "%haystack", "%needle" ), 
                array( "ezcTemplateIdenticalOperatorAstNode", array( 
                    self::functionCall( "strpos", array( "%haystack", "%needle" ) ),
                    self::value( 0 )
                ) ) ); 

            // str_right( $s, $len )
            // substr( $s, -$len )
            case "str_right": return array( ezcTemplateAstNode::TYPE_VALUE,  array( "%string", "%length" ),
                self::functionCall( "substr", array( "%string", array( "ezcTemplateArithmeticNegationOperatorAstNode",  array( "%length" ) ) ) ) );

            // str_ends_with( $sl, $sr )
            // strrpos( $sl, $sr ) === ( strlen( $sl ) - strlen( $sr) )
            case "str_ends_with": return array(
                ezcTemplateAstNode::TYPE_VALUE,
                array( "%haystack", "%needle" ),
                array( "ezcTemplateIdenticalOperatorAstNode", array( 
                    self::functionCall( "strrpos", array( "%haystack", "%needle" ) ),
                    array( "ezcTemplateSubtractionOperatorAstNode", array( 
                        self::functionCall( "strlen", array( "%haystack" ) ), 
                        self::functionCall( "strlen", array( "%needle" ) ) 
                    ) ) ) ) ); 

            // str_mid( $s, $index, $len )
            // substr( $s, $index, $len )
            case "str_mid": return array( ezcTemplateAstNode::TYPE_VALUE,  array( "%string", "%index", "%length" ), 
                self::functionCall( "substr", array( "%string", "%index", "%length" ) ) );

            // str_at( $s, $index )
            // substr( $s, $index, 1 )
            case "str_at": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "%index" ), 
                self::functionCall( "substr", array( "%string", "%index", self::value( 1 ) ) ) );

            // str_fill( $s, $len )
            // str_repeat( $s, $len )
            case "str_fill": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "%length" ), 
                self::functionCall( "str_repeat", array( "%string", "%length" ) ) );

            // str_index_of( $sl, $sr [, $index ] )
            // strpos( $sl, $sr [, $index ] )
            case "str_index_of": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%haystack", "%needle", "[%index]" ), 
                self::functionCall( "strpos", array( "%haystack", "%needle", "[%index]" ) ) );
            
            // str_last_index( $sl, $sr [, $index] )
            // strrpos( $sl, $sr [, $index ] )
            case "str_last_index": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%haystack", "%needle", "[%index]" ), 
                self::functionCall( "strrpos", array( "%haystack", "%needle", "[%index]" ) ) );
             
            // str_is_empty( $s )
            // strlen( $s ) === 0
            case "str_is_empty": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), 
                array( "ezcTemplateIdenticalOperatorAstNode", array( 
                    self::functionCall( "strlen", array( "%string" ) ),
                    self::value( 0 ) ) ) );
             
            // str_pad_left( $s, $len, $fill )
            // str_pad( $s, $len, $fill, STR_PAD_LEFT )
            case "str_pad_left": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "%length", "%fill" ), 
                    self::functionCall( "str_pad", array( "%string", "%length", "%fill", self::constant( "STR_PAD_LEFT" ) ) ) );
             
            // str_pad_right( $s, $len, $fill ) ( QString::rightJustified() )::
            // str_pad( $s, $len, $fill, STR_PAD_RIGHT )
            case "str_pad_right": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "%length", "%fill" ), 
                    self::functionCall( "str_pad", array( "%string", "%length", "%fill", self::constant( "STR_PAD_RIGHT" ) ) ) );
             
            // str_number( $num, $decimals, $point, $sep )
            // number_format( $num, $decimals, $point, $sep )
            case "str_number": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%number", "%decimals", "%point", "%separator" ), 
                    self::functionCall( "number_format", array( "%number", "%decimals", "%point", "%separator") ) );
             
            // str_trim( $s [, $chars ] )
            // trim( $s [, $chars] )
            case "str_trim": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "[%chars]" ), 
                    self::functionCall( "trim", array( "%string", "[%chars]") ) );
             
            // str_trim_left( $s [, $chars] )
            // ltrim( $s [, $chars] )
            case "str_trim_left": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "[%chars]" ), 
                    self::functionCall( "ltrim", array( "%string", "[%chars]") ) );
             
            // str_trim_right( $s [, $chars] )
            // rtrim( $s, [$chars] )
            case "str_trim_right": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "[%chars]" ), 
                    self::functionCall( "rtrim", array( "%string", "[%chars]") ) );
             
            // str_simplified( $s )
            // trim( preg_replace( "/(\n|\t|\r\n|\s)+/", " ", $s ) )
            case "str_simplify": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), 
                    self::functionCall( "trim", array(
                        self::functionCall( "preg_replace", array( self::constant( '"/(\n|\t|\r\n|\s)+/"' ), self::value( " " ), "%string" ) )
                    ) ) );
             
            // str_split( $s, $sep[, $max] )
            // explode( $s, $sep, $max )
            case "str_split": return array( ezcTemplateAstNode::TYPE_VALUE | ezcTemplateAstNode::TYPE_ARRAY, array( "%string", "%separator", "[%max]" ), 
                    self::functionCall( "explode", array( "%separator", "%string", "[%max]" ) ) );
             
            // str_join( $s_list, $sep )
            // join( $sList, $sep )
            case "str_join": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%list", "%separator" ), 
                    self::functionCall( "join", array( "%separator", "%list" ) ) );
             
            // str_printf( $format [...] )
            // sprintf( $format [...] )
            // TODO
             
            // str_chr( $ord1 [, $ord2...] )::
            // ord( $ord1 ) [ . ord( $ord2 ) ...]
            // TODO 
            
            // str_ord( $c )
            // ord( $c )
            case "str_ord": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%char" ), 
                    self::functionCall( "ord", array( "%char" ) ) );

            // chr( $c )
            case "str_chr": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%char" ), 
                    self::functionCall( "chr", array( "%char" ) ) );
            
            // str_ord_list( $s )::
            // chr( $s[0] ) [ . chr( $s[1] ) ]
            // TODO
             
            // str_upper( $s )
            // strtoupper( $s )
            case "str_upper": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), 
                    self::functionCall( "strtoupper", array( "%string") ) );
            
            // str_lower( $s )
            // strtolower( $s )
            case "str_lower": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), 
                    self::functionCall( "strtolower", array( "%string") ) );
             
            // str_capitalize( $s )::
            // ucfirst( $s )
            case "str_capitalize": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), 
                    self::functionCall( "ucfirst", array( "%string") ) );
             
            // str_find_replace( $s, $find, $replace, $count )::
            // str_replace( $s, $replace, $find, $count )
            case "str_find_replace": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "%find", "%replace", "[%count]" ), 
                    self::functionCall( "str_replace", array( "%find", "%replace", "%string", "[%count]") ) );
             
            // str_reverse( $s )::
            // strrev( $s )
            case "str_reverse": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), 
                    self::functionCall( "strrev", array( "%string" ) ) );
             
            // str_section( $s, $sep, $start, $end = -1 )
            // join( array_slice( split( $s, $sep, $end != -1 ? $end, false ), $start, $end ? $end : false ) )
            // TODO

             // str_char_count( $s )::
            // strlen( $s )
            case "str_char_count": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), 
                    self::functionCall( "strlen", array( "%string" ) ) );
             
            // str_word_count( $s [, $wordsep] )
            // str_word_count( $s, 0 [, $wordsep] )
            case "str_word_count": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "[%wordsep]" ), 
                    self::functionCall( "str_word_count", array( "%string", self::value( 0 ), "[%wordsep]" ) ) );
 
            // - *string* str_paragraph_count( $s )::
            // Code.
            case "str_paragraph_count": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), 
                    self::functionCall( "ezcTemplateString::str_paragraph_count", array( "%string" ) ) );
 
           // 
            // - *string* str_sentence_count( $s )::
            // 
            //     $pos = 0;
            //     $count = 0;
            //     while ( preg_match( "/. /", $s, $m, PREG_OFFSET_CAPTURE, $pos )
            //     {
            //         ++$count;
            //         $pos = $m[0][1];
            //     }
            // TODO
            // 
            // - *string* str_break( $s, $eol = contextaware, $lbreak = contextaware )::
            // 
            //     str_replace( context_eol_char( $eol ), context_linebreak_char( $eol ), $s )
            // 
            // TODO
            // 
            // - *string* str_break_chars( $s, $cbreak )::
            // 
            //     $sNew = '';
            //     for ( $i = 0; $i < strlen( $s ) - 1; ++$i )
            //     {
            //         $sNew .= $s[$i] . $cbreak;
            //     }
            //     $sNew .= $s[strlen( $s ) - 1];
            // 
            // TODO
            
            // str_wrap( $s, $width, $break, $cut )
            // wordwrap( $s, $width, $break, $cut )
            case "str_wrap": return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string", "%width", "%break", "[%cut]" ), 
                    self::functionCall( "wordwrap", array( "%string", "%width", "%break", "[%cut]" ) ) );

            // base64_encode( $s )
            case "str_base64_encode":
                return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), self::functionCall( "base64_encode", array( "%string" ) ) );
            
            // base64_decode( $s )
            case "str_base64_decode":
                return array( ezcTemplateAstNode::TYPE_VALUE, array( "%string" ), self::functionCall( "base64_decode", array( "%string" ) ) );
 
            // 
            // - *string* str_wrap_indent::
            // 
            //    $tmp = wordwrap( $s, $width, $break, $cut )
            //    $lines = explode( "\n", $tmp );
            //    $newLines = array();
            //    foreach ( $lines as $line )
            //    {
            //        $newLines[] = $prefix . $line . $suffix;
            //    }
            //    return join( "\n", $newLines )
            // 
            // TODO
            // - *string* str_block( $s, $prefix, $suffix )
            // 
            // 
            // - *string* str_shorten_right( $s, $max_size )
            // 
            // - *string* str_shorten_mid( $s, $max_size )
            // 
            // - *string* str_shorten_left( $s, $max_size )
            // 
            // - *string* str_crc32( $s )::
            // 
            //     crc32( $s )
            // 
            // - *string* str_md5( $s )::
            // 
            //     md5( $s )
            // 
            // - *string* str_sha1( $s )::
            // 
            //     sha1( $s )
            // 
            // - *string* str_rot13( $s )::
            // 
            //     str_rot13( $s )
            // 
            // Some of the functions are also available as case insensitive versions, they are:
            // 
            // - *string* stri_contains( $sl, $sr ) ( QString::compare )::
            // 
            //     stristr( $sl, $sr ) !== false
            // 
            // - *string* stri_starts_with( $sl, $sr ) ( QString::startsWith )::
            // 
            //     stripos( strtolower( $sl ), strtolower( $sr ) ) === 0
            // 
            // - *string* stri_ends_with( $sl, $sr ) ( QString::endsWith )::
            // 
            //     strripos( $sl, $sr ) === ( strlen( $sl ) - 1 )
            // 
            // - *string* stri_index( $sl, $sr [, $from] ) ( QString::indexOf )::
            // 
            //     stripos( $sl, $sr [, $from ] )
            // 
            // - *string* stri_last_index( $sl, $sr [, $from] ) ( QString::lastIndexOf )::
            // 
            //     strirpos( $sl, $sr [, $from ] )
            // 
            // - *string* stri_find_replace( $s, $find, $replace, $count )::
            // 
            //     str_ireplace( $s, $replace, $find, $count )
            // 
            // - *string* stri_compare( $sl, $sr ) ( QString::compare )::
            // 
            //     strcasecmp( $sl, $sr );
            // 
            // - *string* stri_nat_compare( $sl, $sr )::
            // 
            //     strnatcasecmp( $sl, $sr );
            // 

        }

        return null;
    }
}
?>
