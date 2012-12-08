<?php
/**
 * File containing the ezcAuthenticationDatabaseInfo structure.
 *
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package AuthenticationDatabaseTiein
 * @version 1.1
 */

/**
 * Structure for defining the database and table to authenticate against.
 *
 * @package AuthenticationDatabaseTiein
 * @version 1.1
 */
class ezcAuthenticationDatabaseInfo extends ezcBaseStruct
{
    /**
     * Database instance.
     *
     * @var ezcDbHandler
     */
    public $instance;

    /**
     * Table which stores the user credentials.
     *
     * @var string
     */
    public $table;

    /**
     * Fields which hold the user credentials.
     *
     * @var array(string)
     */
    public $fields;

    /**
     * Constructs a new ezcAuthenticationDatabaseInfo object.
     *
     * @param ezcDbHandler $instance Database instance to use
     * @param string $table Table which stores usernames and passwords
     * @param array(string) $fields The fields which hold usernames and passwords
     */
    public function __construct( ezcDbHandler $instance, $table, array $fields )
    {
        $this->instance = $instance;
        $this->table = $table;
        $this->fields = $fields;
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
     * @return ezcAuthenticationDatabaseInfo
     */
    static public function __set_state( array $array )
    {
        return new ezcAuthenticationDatabaseInfo( $array['instance'], $array['table'], $array['fields'] );
    }
}
?>
