<?php
/**
 * File containing the ezcTemplateType class
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
 
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateType
{
    /**
     * Returns true if the given value is empty, otherwise false.
     *
     * This method couldn't be translated directly because the parameter
     * of empty should always be a variable. 
     *
     * This wrapper function makes it possible to call: is_empty("");
     *
     * @param mixed $var
     * @return bool
     */
    public static function is_empty( $var )
    {
        return empty( $var );
    }

    /**
     * Returns true if the given variable $var is an instance of the class $class. 
     *
     * @param mixed $var
     * @param string $class
     * @return bool
     */
    public static function is_instance( $var, $class )
    {
        return ($var instanceof $class);
    }
}


?>
