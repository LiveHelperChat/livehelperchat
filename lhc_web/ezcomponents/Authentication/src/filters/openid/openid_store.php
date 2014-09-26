<?php
/**
 * File containing the ezcAuthenticationOpenidStore class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Abstract class which provides a base for store (backend) implementations to
 * be used in OpenID authentication.
 *
 * @package Authentication
 * @version 1.3.1
 */
abstract class ezcAuthenticationOpenidStore
{
    /**
     * Options for OpenID stores.
     * 
     * @var ezcAuthenticationOpenidStoreOptions
     */
    protected $options;

    /**
     * Sets the options of this class to $options.
     *
     * @param ezcAuthenticationOpenidStoreOptions $options Options for this class
     */
    public function setOptions( ezcAuthenticationOpenidStoreOptions $options )
    {
        $this->options = $options;
    }

    /**
     * Returns the options of this class.
     *
     * @return ezcAuthenticationOpenidStoreOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Stores the nonce in the store.
     *
     * Returns true if the nonce was stored successfully, and false otherwise.
     *
     * @param string $nonce The nonce value to store
     * @return bool
     */
    abstract public function storeNonce( $nonce );

    /**
     * Checks if the nonce exists and afterwards deletes it.
     *
     * Returns true if the nonce can be used (exists and it is still valid), and
     * false otherwise.
     *
     * @param string $nonce The nonce value to check and delete
     * @return bool
     */
    abstract public function useNonce( $nonce );

    /**
     * Stores an association in the store linked to the OpenID provider URL.
     *
     * Returns true if the association was stored successfully, and false
     * otherwise.
     *
     * @param string $url The URL of the OpenID provider
     * @param ezcAuthenticationOpenidAssociation $association The association value to store
     * @return bool
     */
    abstract public function storeAssociation( $url, $association );

    /**
     * Returns the association linked to the OpenID provider URL.
     *
     * Returns null if the association could not be retrieved.
     *
     * @param string $url The URL of the OpenID provider
     * @return ezcAuthenticationOpenidAssociation
     */
    abstract public function getAssociation( $url );

    /**
     * Removes the association linked to the OpenID provider URL.
     *
     * Returns true if the association could be removed, and false otherwise.
     *
     * @param string $url The URL of the OpenID provider
     * @return bool
     */
    abstract public function removeAssociation( $url );
}
?>
