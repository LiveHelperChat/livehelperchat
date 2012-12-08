<?php
/**
 * File containing the ezcAuthenticationPasswordCredentials structure.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Structure containing an id and password, used as authentication credentials.
 *
 * @package Authentication
 * @version 1.3.1
 * @mainclass
 */
class ezcAuthenticationPasswordCredentials extends ezcAuthenticationCredentials
{
    /**
     * Username or userID or url.
     *
     * @var string
     */
    public $id;

    /**
     * Password for the id.
     *
     * @var string
     */
    public $password;

    /**
     * Constructs a new ezcAuthenticationPasswordCredentials object.
     *
     * @param string $id
     * @param string $password
     */
    public function __construct( $id, $password )
    {
        $this->id = $id;
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
     * @param array(string=>mixed) $array Associative array of data members for this class
     * @return ezcAuthenticationPasswordCredentials
     */
    public static function __set_state( array $array )
    {
        return new ezcAuthenticationPasswordCredentials( $array['id'], $array['password'] );
    }

    /**
     * Returns string representation of the credentials.
     *
     * Use it to save the credentials in the session.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }
}
?>
