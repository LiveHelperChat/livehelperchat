<?php
/**
 * File containing the ezcAuthenticationOpenidDbStore class.
 *
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package AuthenticationDatabaseTiein
 * @version 1.1
 */

/**
 * Class providing database storage for OpenID authentication.
 *
 * This class requires that the database used contains two special tables. See
 * the tutorial for information on how to create those tables.
 *
 * Example of use:
 * <code>
 * // create an OpenID options object
 * $options = new ezcAuthenticationOpenidOptions();
 * $options->mode = ezcAuthenticationOpenidFilter::MODE_SMART;
 *
 * // define a database store
 * $options->store = new ezcAuthenticationOpenidDbStore( ezcDbInstance::get() );
 *
 * // create an OpenID filter based on the options object
 * $filter = new ezcAuthenticationOpenidFilter( $options );
 * </code>
 *
 * @property ezcDbHandler $instance
 *           The database instance to use for database storage.
 *
 * @package AuthenticationDatabaseTiein
 * @version 1.1
 */
class ezcAuthenticationOpenidDbStore extends ezcAuthenticationOpenidStore
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Creates a new object of this class.
     *
     * @param ezcDbHandler $instance The database instance used for this store
     * @param ezcAuthenticationOpenidDbStoreOptions $options Options for this class
     */
    public function __construct( ezcDbHandler $instance, ezcAuthenticationOpenidDbStoreOptions $options = null )
    {
        $this->instance = $instance;
        $this->options = ( $options === null ) ? new ezcAuthenticationOpenidDbStoreOptions() : $options;
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
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
            case 'instance':
                if ( !( $value instanceof ezcDbHandler ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcDbHandler' );
                }

                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @param string $name The name of the property for which to return the value
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'instance':
                return $this->properties[$name];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name The name of the property to test if it is set
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'instance':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Stores the nonce in the store.
     *
     * Returns true if the nonce was stored successfully, and false otherwise.
     *
     * @throws ezcBaseFilePermissionException
     *         if the nonce cannot be written in the store
     * @param string $nonce The nonce value to store
     * @return bool
     */
    public function storeNonce( $nonce )
    {
        $table = $this->options->tableNonces;

        $query = new ezcQueryInsert( $this->instance );

        $query->insertInto( $this->instance->quoteIdentifier( $table['name'] ) )
              ->set( $this->instance->quoteIdentifier( $table['fields']['nonce'] ), $query->bindValue( $nonce ) )
              ->set( $this->instance->quoteIdentifier( $table['fields']['timestamp'] ), $query->bindValue( time() ) );

        $stmt = $query->prepare();
        $stmt->execute();

        return true;
    }

    /**
     * Checks if the nonce exists and afterwards deletes it.
     *
     * Returns the timestamp of the nonce if it exists, and false otherwise.
     *
     * @param string $nonce The nonce value to check and delete
     * @return bool|int
     */
    public function useNonce( $nonce )
    {
        $table = $this->options->tableNonces;

        $query = new ezcQuerySelect( $this->instance );
        $e = $query->expr;
        $query->select( '*' )
              ->from( $this->instance->quoteIdentifier( $table['name'] ) )
              ->where(
                  $e->eq( $this->instance->quoteIdentifier( $table['fields']['nonce'] ), $query->bindValue( $nonce ) )
                     );
        $query = $query->prepare();
        $query->execute();
        $rows = $query->fetchAll();
        if ( count( $rows ) > 0 )
        {
            $rows = $rows[0];
            $lastModified = (int) $rows[$table['fields']['timestamp']];

            $this->removeNonce( $nonce );

            return $lastModified;
        }

        // $nonce was not found in the database
        return false;
    }

    /**
     * Removes the nonce from the nonces table.
     *
     * @param string $nonce
     */
    protected function removeNonce( $nonce )
    {
        $table = $this->options->tableNonces;

        $query = new ezcQueryDelete( $this->instance );
        $e = $query->expr;
        $query->deleteFrom( $this->instance->quoteIdentifier( $table['name'] ) )
              ->where(
                  $e->eq( $this->instance->quoteIdentifier( $table['fields']['nonce'] ), $query->bindValue( $nonce ) )
                     );
        $query = $query->prepare();
        $query->execute();
    }

    /**
     * Stores an association in the store linked to the OpenID provider URL.
     *
     * Returns true always.
     *
     * @param string $url The URL of the OpenID provider
     * @param ezcAuthenticationOpenidAssociation $association The association value to store
     * @return bool
     */
    public function storeAssociation( $url, $association )
    {
        $table = $this->options->tableAssociations;
        $data = serialize( $association );

        $query = new ezcQueryInsert( $this->instance );

        $query->insertInto( $this->instance->quoteIdentifier( $table['name'] ) )
              ->set( $this->instance->quoteIdentifier( $table['fields']['url'] ), $query->bindValue( $url ) )
              ->set( $this->instance->quoteIdentifier( $table['fields']['association'] ), $query->bindValue( $data ) );

        $stmt = $query->prepare();
        $stmt->execute();

        return true;
    }

    /**
     * Returns the unserialized association linked to the OpenID provider URL.
     *
     * Returns false if the association could not be retrieved or if it expired.
     *
     * @param string $url The URL of the OpenID provider
     * @return ezcAuthenticationOpenidAssociation
     */
    public function getAssociation( $url )
    {
        $table = $this->options->tableAssociations;

        $query = new ezcQuerySelect( $this->instance );
        $e = $query->expr;
        $query->select( '*' )
              ->from( $this->instance->quoteIdentifier( $table['name'] ) )
              ->where(
                  $e->eq( $this->instance->quoteIdentifier( $table['fields']['url'] ), $query->bindValue( $url ) )
                     );

        $query = $query->prepare();
        $query->execute();
        $rows = $query->fetchAll();

        if ( count( $rows ) > 0 )
        {
            $rows = $rows[0];
            $data = unserialize( $rows[$table['fields']['association']] );

            return $data;
        }

        // no association was found for $url
        return false;
    }

    /**
     * Removes the association linked to the OpenID provider URL.
     *
     * Returns true always.
     *
     * @param string $url The URL of the OpenID provider
     * @return bool
     */
    public function removeAssociation( $url )
    {
        $table = $this->options->tableAssociations;

        $query = new ezcQueryDelete( $this->instance );
        $e = $query->expr;
        $query->deleteFrom( $this->instance->quoteIdentifier( $table['name'] ) )
              ->where(
                  $e->eq( $this->instance->quoteIdentifier( $table['fields']['url'] ), $query->bindValue( $url ) )
                     );
        $query = $query->prepare();
        $query->execute();

        return true;
    }
}
?>
