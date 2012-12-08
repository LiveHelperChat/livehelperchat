<?php
/**
 * File containing the ezcTemplateRegExp class
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
class ezcTemplateRegExp
{
    /**
     * Returns an array with the matching values of the performed match between the regular expression 
     * $reg and the $string. 
     *
     * @param string $reg
     * @param string $string
     * @return array(string)
     */
    public static function preg_match( $reg, $string )
    {
        preg_match( $reg, $string, $matches );
        return $matches;
    }
}


?>
