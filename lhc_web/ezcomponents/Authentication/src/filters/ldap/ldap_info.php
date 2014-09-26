<?php
/**
 * File containing the ezcAuthenticationLdapInfo structure.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Structure for defining the LDAP directory to authenticate against.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationLdapInfo extends ezcBaseStruct
{
    /**
     * The hostname of the LDAP server, for example 'localhost'.
     *
     * @var string
     */
    public $host;

    /**
     * The format of the directory entry, for example 'uid=%id'. %id% is a
     * placeholder name which will be replaced by the actual value.
     *
     * @var string
     */
    public $format;

    /**
     * The base of the directory entry, for example 'dc=example,dc=com'.
     *
     * @var string
     */
    public $base;

    /**
     * Port to connect to $host.
     *
     * Default is 389 for plain connection.
     * The port is 636 if using SSL (not implemented yet).
     *
     * The port is not usable if $host is specified as an uri, for example
     * 'ldap://localhost'.
     *
     * @var int
     */
    public $port;

    /**
     * Protocol to use to connect to LDAP.
     *
     * One of these values:
     *  - ezcAuthenticationLdapFilter::PROTOCOL_PLAIN (default)
     *  - ezcAuthenticationLdapFilter::PROTOCOL_TLS
     *
     * @var int
     * @apichange Remove this as it is used already as an option
     */
    public $protocol;

    /**
     * Constructs a new ezcAuthenticationLdapInfo object.
     *
     * @param string $host Hostname of the LDAP server
     * @param string $format Format of an entry, for example 'uid=%id%'
     * @param string $base Base of an entry, for example 'dc=example,dc=com'
     * @param int $port The port to connect to $host
     * @param int $protocol The protocol to use to connect to $host
     */
    public function __construct( $host, $format, $base, $port = 389, $protocol = ezcAuthenticationLdapFilter::PROTOCOL_PLAIN )
    {
        $this->host = $host;
        $this->format = $format;
        $this->base = $base;
        $this->port = $port;
        $this->protocol = $protocol;
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
     * @return ezcAuthenticationLdapInfo
     */
    static public function __set_state( array $array )
    {
        return new ezcAuthenticationLdapInfo( $array['host'], $array['format'], $array['base'], $array['port'], $array['protocol'] );
    }
}
?>
