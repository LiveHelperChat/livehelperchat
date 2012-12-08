<?php
/**
 * File containing the ezcMvcRequestAuthentication class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Struct which holds the request authentication parameters.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcRequestAuthentication extends ezcBaseStruct
{
    /**
     * Username or other authentication identifier.
     *
     * @var string
     */
    public $identifier;

    /**
     * Password or other identity verification.
     *
     * @var string
     */
    public $password;

    /**
     * Constructs a new ezcMvcRequestAuthentication.
     *
     * @param string $identifier
     * @param string $password
     */
    public function __construct( $identifier = '', $password = '' )
    {
        $this->identifier = $identifier;
        $this->password = $password;
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
     * @return ezcMvcRequestAuthentication
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcRequestAuthentication( $array['identifier'],
            $array['password'] );
    }
}
?>
