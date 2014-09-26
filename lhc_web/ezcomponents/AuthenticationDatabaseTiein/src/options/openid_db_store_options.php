<?php
/**
 * File containing the ezcAuthenticationOpenidDbStoreOptions class.
 *
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package AuthenticationDatabaseTiein
 * @version 1.1
 */

/**
 * Class containing the options for the OpenID database store.
 *
 * Example of use:
 * <code>
 * // create an options object
 * $options = new ezcAuthenticationOpenidDbStoreOptions();
 * $options->tableNonces = array( 'name' => 'openid_nonces', 'fields' => array( 'nonce' => 'id', 'timestamp' => 'time' ) );
 * $options->tableAssociations = array( 'name' => 'openid_associations', 'fields' => array( 'url' => 'id', 'association' => 'assoc' ) );
 *
 * // use the options object
 * $store = new ezcAuthenticationOpenidDbStore( ezcDbInstance::get(), $options );
 *
 * // alternatively, you can set the options to an existing object
 * $store = new ezcAuthenticationOpenidDbStore( ezcDbInstance::get() );
 * $store->setOptions( $options );
 * </code>
 *
 * @property array(string=>mixed) $tableNonces
 *           A structure defining how the table which holds the nonces looks like.
 *           The default is array( 'name' => 'openid_nonces', 'fields' => array(
 *           'nonce' => 'nonce', 'timestamp' => 'timestamp' ) ). The column
 *           nonce is a key in the table. The names of the columns and of the
 *           table name can be changed ('nonce', 'timestamp', 'openid_nonces').
 *
 * @property array(string=>mixed) $tableAssociations
 *           A structure defining how the table which holds the nonces looks like.
 *           The default is array( 'name' => 'openid_associations', 'fields' => array(
 *           'url' => 'url', 'association' => 'association' ) ). The column
 *           nonce is a key in the table. The names of the columns and of the
 *           table name can be changed ('nonce', 'timestamp', 'openid_associations').
 *
 * @package AuthenticationDatabaseTiein
 * @version 1.1
 */
class ezcAuthenticationOpenidDbStoreOptions extends ezcAuthenticationOpenidStoreOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options Options for this class
     */
    public function __construct( array $options = array() )
    {
        $this->tableNonces = array( 'name' => 'openid_nonces', 'fields' => array( 'nonce' => 'nonce', 'timestamp' => 'timestamp' ) );
        $this->tableAssociations = array( 'name' => 'openid_associations', 'fields' => array( 'url' => 'url', 'association' => 'association' ) );

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'tableNonces':
            case 'tableAssociations':
                if ( !is_array( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'array' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}
?>
