<?php
/**
 * File containing the ezcMvcRequestCookie class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * This struct contains cookie arguments
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcRequestCookie extends ezcBaseStruct
{
    /**
     * Cookie name
     *
     * @var string
     */
    public $name;

    /**
     * Cookie value
     *
     * @var string
     */
    public $value;

    /**
     * Constructs a new ezcMvcRequestCookie.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct( $name = '', $value = '' )
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcMvcResultCache
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcRequestCookie( $array['name'], $array['value'] );
    }
}
?>
