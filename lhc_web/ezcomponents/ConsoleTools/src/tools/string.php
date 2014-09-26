<?php
/**
 * File containing the ezcConsoleStringTool class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */
/**
 * String tool class.
 *
 * Tool class for the ConsoleTools package. Contains multi-byte encoding save
 * string methods.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 * @access private
 */
class ezcConsoleStringTool
{
    /**
     * Binary safe wordwrap() replacement.
     *
     * This method is a multi-byte encoding safe replacement for the PHP
     * function wordwrap(). It mimics exactly the behavior of wordwrap(), but
     * uses iconv_* functions with UTF-8 encoding. The parameters received by
     * this method equal the parameters of {@link http://php.net/wordwrap
     * wordwrap()}. Note: Make sure to only hand UTF-8 encoded content to this
     * method.
     * 
     * @param string $str 
     * @param int $width 
     * @param string $break 
     * @param bool $cut 
     * @return string|false
     */
    public function wordWrap( $str, $width = 75, $break = "\n", $cut = false )
    {
        $strlen   = iconv_strlen( $str, 'UTF-8' );
        $breaklen = iconv_strlen( $break, 'UTF-8' );
        $newtext  = '';

        if ( $strlen === 0 )
        {
            return '';
        }
    
        if ( $breaklen === 0 )
        {
            return false;
        }

        if ( $width === 0 && $cut )
        {
            return false;
        }

        $laststart  = $lastspace = 0;
        $breakstart = iconv_substr( $break, 0, 1, 'UTF-8' );

        for ( $current = 0; $current < $strlen; $current++ )
        {
            $char = iconv_substr( $str, $current, 1, 'UTF-8' );

            // Existing line break, copy line and  start a new one
            if ( $char === $breakstart
                 && $current + $breaklen < $strlen
                 && iconv_substr( $str, $current, $breaklen, 'UTF-8' ) === $break
               )
            {
                $newtext .= iconv_substr( $str, $laststart, $current - $laststart + $breaklen, 'UTF-8' );
                $current += $breaklen - 1;
                $laststart = $lastspace = $current + 1;
            }

            // Keep track of spaces, if line break is necessary, do it
            else if ( $char === ' ' )
            {
                if ( $current - $laststart >= $width )
                {
                    $newtext .= iconv_substr( $str, $laststart, $current - $laststart, 'UTF-8' )
                        . $break;
                    $laststart = $current + 1;
                }
                $lastspace = $current;
            }

            // Special cut case, if no space has been seen
            else if ( $current - $laststart >= $width
                      && $cut && $laststart >= $lastspace
                    )
            {
                $newtext .= iconv_substr( $str, $laststart, $current - $laststart, 'UTF-8' )
                    . $break;
                $laststart = $lastspace = $current;
            }


            // Usual case that line got longer than expected
            else if ( $current - $laststart >= $width
                      && $laststart < $lastspace
                    )
            {
                $newtext .= iconv_substr( $str, $laststart, $lastspace - $laststart, 'UTF-8' )
                    . $break;
                // $laststart = $lastspace = $lastspace + 1;
                $laststart = ++$lastspace;
            }
        }

		// Rest of the string
        if ( $laststart !== $current )
        {
            $newtext .= iconv_substr( $str, $laststart, $current - $laststart, 'UTF-8' );
        }

        return $newtext;
    }

    /**
     * Binary safe str_pad() replacement.
     *
     * This method is a multi-byte encoding safe replacement for the PHP
     * function str_pad().  It mimics exactly the behavior of str_pad(), but
     * uses iconv_* functions with UTF-8 encoding. The parameters received by
     * this method equal the parameters of {@link http://php.net/str_pad
     * str_pad()}. Note: Make sure to hand only UTF-8 encoded content to this
     * method.
     * 
     * @param string $input 
     * @param int $padLength 
     * @param string $padString 
     * @param int $padType 
     * @return string
     */
    public function strPad( $input, $padLength, $padString = ' ', $padType = STR_PAD_RIGHT )
    {
        $input     = (string) $input;

        $strLen    = iconv_strlen( $input, 'UTF-8' );
        $padStrLen = iconv_strlen( $padString, 'UTF-8' );

        if ( $strLen >= $padLength )
        {
            return $input;
        }

        if ( $padType === STR_PAD_BOTH )
        {
            return $this->strPad(
                $this->strPad(
                    $input,
                    $strLen + ceil( ( $padLength - $strLen ) / 2 ),
                    $padString
                ),
                $padLength,
                $padString,
                STR_PAD_LEFT
            );
        }

        $fullStrRepeats = (int) ( ( $padLength - $strLen ) / $padStrLen );
        $partlyPad = iconv_substr(
            $padString,
            0,
            ( ( $padLength - $strLen ) % $padStrLen )
        );

        $padding = str_repeat( $padString, $fullStrRepeats ) . $partlyPad;

        switch ( $padType )
        {
            case STR_PAD_LEFT:
                return $padding . $input;
            case STR_PAD_RIGHT:
            default:
                return $input . $padding;
        }
    }
}

?>
