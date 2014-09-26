<?php
/**
 * File containing the ezcMailCharsetConverter class.
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing common character set conversion methods.
 *
 * By calling the static function ezcMailCharsetConverter::setConvertMethod()
 * before doing mail parsing, another callback function can be used for
 * character conversion to UTF-8 in place of the normal iconv() conversion.
 *
 * The callback function must have this signature:
 * <code>
 * public static function function_name( $text, $originalCharset );
 * </code>
 *
 * where:
 *  - $text = string to convert to UTF-8
 *  - $originalCharset = in what charset is $text
 *
 * Example:
 * <code>
 * // specify another function for character set conversion
 * ezcMailCharsetConverter::setConvertMethod( array( 'myConverter', 'convertToUTF8IconvIgnore' ) );
 *
 * // ...code for mail parsing...
 * </code>
 *
 * where myConverter is (along with some other examples of charset conversion
 * functions which can be used):
 * <code>
 * class myConverter
 * {
 *     public static function convertToUTF8IconvIgnore( $text, $originalCharset )
 *     {
 *         if ( $originalCharset === 'unknown-8bit' || $originalCharset === 'x-user-defined' )
 *         {
 *             $originalCharset = "latin1";
 *         }
 *         return iconv( $originalCharset, 'utf-8//IGNORE', $text );
 *     }
 *
 *     public static function convertToUTF8IconvTranslit( $text, $originalCharset )
 *     {
 *         if ( $originalCharset === 'unknown-8bit' || $originalCharset === 'x-user-defined' )
 *         {
 *             $originalCharset = "latin1";
 *         }
 *         return iconv( $originalCharset, 'utf-8//TRANSLIT', $text );
 *     }
 *
 *     public static function convertToUTF8Mbstring( $text, $originalCharset )
 *     {
 *         return mb_convert_encoding( $text, "UTF-8", $originalCharset );
 *     }
 * }
 * </code>
 *
 * Developers can choose to use the error suppresion operator ('@') in front of
 * the iconv() calls in the above examples, in order to ignore the notices thrown
 * when processing broken text (issue #8369).
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailCharsetConverter
{
    /**
     * Callback function to use for character set conversion to UTF8.
     *
     * @var callback
     */
    private static $method = array( __CLASS__, 'convertToUTF8Iconv' );

    /**
     * Sets the callback function used for character set conversion to UTF-8.
     *
     * Call this method before doing mail parsing if you need a special way
     * of converting the character set to UTF-8.
     *
     * @param callback $method
     */
    public static function setConvertMethod( $method )
    {
        self::$method = $method;
    }

    /**
     * Converts the $text with the charset $originalCharset to UTF-8.
     *
     * It calls the function specified by using the static method
     * setConvertMethod(). By default it calls convertToUTF8Iconv() defined
     * in this class.
     *
     * @param string $text
     * @param string $originalCharset
     * @return string
     */
    public static function convertToUTF8( $text, $originalCharset )
    {
        return call_user_func( self::$method, $text, $originalCharset );
    }

    /**
     * Converts the $text with the charset $originalCharset to UTF-8.
     *
     * In case $originalCharset is 'unknown-8bit' or 'x-user-defined' then
     * it is assumed to be 'latin1' (ISO-8859-1).
     *
     * @param string $text
     * @param string $originalCharset
     * @return string
     */
    public static function convertToUTF8Iconv( $text, $originalCharset )
    {
        if ( $originalCharset === 'unknown-8bit' || $originalCharset === 'x-user-defined' )
        {
            $originalCharset = "latin1";
        }
        return iconv( $originalCharset, 'utf-8', $text );
    }
}
?>
