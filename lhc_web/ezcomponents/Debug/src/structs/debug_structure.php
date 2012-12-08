<?php
/**
 * File containing the ezcDebugStructure class.
 *
 * @package Debug
 * @version 1.2.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcDebugStructure is used internally by the debug system to store
 * debug messages.
 *
 * @package Debug
 * @version 1.2.1
 * @access private
 */
class ezcDebugStructure
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Holds the sub-elements of this structure.
     *
     * These elements cannot be a part of the property system because it is an
     * array.
     */
    public $elements = array();

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        $this->properties[$name] = $value;
    }

   /**
     * Returns the property $name.
     *
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        $value = $this->properties[$name];
        if ( is_array( $value ) )
        {
            return (array) $this->properties[$name];
        }
        return $this->properties[$name];
    }

    /**
     * Returns if the given property isset.
     * 
     * @param string $name 
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        return array_key_exists( $name, $this->properties );
    }

    /**
     * Generates string output of the debug messages.
     *
     * The output generated is each value listed in the form "'key' => 'value'".
     *
     * @return string
     */
    public function toString()
    {
        $str = "";
        foreach ( $this->properties as $key => $value )
        {
            $str .= "$key => $value\n";
        }

        return $str;
    }
}
?>
