<?php
/**
 * File containing the ezcTemplateString class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This class contains a bundle of static functions, each implementing a specific
 * function used inside the template language. 
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateString
{
    /**
     * Returns the number of paragraphs found in the given string.
     *
     * @param string $string
     * @return int
     */
    public static function str_paragraph_count( $string )
    {
        $pos = 0;
        $count = 0;

        while ( preg_match( "/\n\n+/", $string, $m, PREG_OFFSET_CAPTURE, $pos ) )
        {
            ++$count;
            $pos = $m[0][1] + 1;
        }

        return $count;
    }
}


?>
