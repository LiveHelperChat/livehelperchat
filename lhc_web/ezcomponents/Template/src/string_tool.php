<?php
/**
 * File containing the ezcTemplateStringTool class.
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Contains static function for doing common string operations.
 *
 * Processing escaped sequences for single and double quoted strings
 * are possible with processSingleQuotedEscapes() and processDoubleQuotedEscapes().
 *
 * <code>
 * $text = ezcTemplateStringTool::processDoubleQuotedEscapes( 'hi\nthere' );
 * var_dump( $text );
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */

class ezcTemplateStringTool
{
    /**
     * This will process escape characters allowed for double quoted strings and return
     * the new string.
     *
     * The allowed escape characters are:
     * - \\n  => 0x0a
     * - \\r  => 0x0c
     * - \\t  => 0x09
     * - \\\\ => \\
     * - \\"  => "
     *
     * @param string $text
     * @return string
     */
    public static function processDoubleQuotedEscapes( $text )
    {

        $text = preg_replace_callback( '#(?:\\\\([nrt"\\\\]))#',
                                       array( __CLASS__, "doubleQuotedEscape" ),
                                       $text );
        return $text;
    }

    /**
     * This will process escape characters allowed for single quoted strings and return
     * the new string.
     *
     * The allowed escape characters are:
     * - \\\\ => \\
     * - \\'  => '
     *
     * @param string $text
     * @return string
     */
    public static function processSingleQuotedEscapes( $text )
    {
        $text = preg_replace_callback( '#(?:\\\\([\'\\\\]))#',
                                       array( __CLASS__, "singleQuotedEscape" ),
                                       $text );
        return $text;
    }

    /**
     * Callback function for the preg_replace_callback() call in processDoubleQuotedEscapes().
     *
     * @param array $matches Array from the preg_replace_callback method.
     * @return string
     */
    public static function doubleQuotedEscape( $matches )
    {
        if ( isset( $matches[1] ) &&
             $matches[1] != "" )
        {
            switch ( $matches[1] )
            {
                case "n":
                    return "\n";
                case "r":
                    return "\r";
                case "t":
                    return "\t";
                case "\"":
                    return "\"";
                case "\\":
                    return "\\";
            }
        }
    }

    /**
     * Callback function for the preg_replace_callback() call in processSingleQuotedEscapes().
     *
     * @param array $matches Array from the preg_replace_callback method.
     * @return string
     */
    public static function singleQuotedEscape( $matches )
    {
        if ( isset( $matches[1] ) &&
             $matches[1] != "" )
        {
            switch ( $matches[1] )
            {
                case "'":
                    return "'";
                case "\\":
                    return "\\";
            }
        }
    }
}

?>
