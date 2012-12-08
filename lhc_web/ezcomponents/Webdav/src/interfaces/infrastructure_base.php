<?php
/**
 * File containing the ezcWebdavInfrastructureBase class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Base class for all infrastructural classes of the Webdav component.
 *
 * The Webdav component provides a nifty plugin system that allows extension
 * developers to hook into the flow of the Webdav component. Since this system
 * makes it hard to extend infrastructural classes, like request or response
 * classes, to add storage for custom plugin data.
 *
 * To solve this need for plugins to attach data to the instance of an
 * infrastructural class, this abstract base class has been invented which is
 * extended by all infrastructural classes.
 *
 * You can attach data to objects that inherit this class by using the {@see
 * $this->setPluginData()} method, receive data back using {@see
 * $this->getPluginData()} and detach once attached data using {@link
 * $this->removePluginData()}. A check if data is available for a given plugin
 * namespace and key can be checked using {@link $this->hasPluginData()}.
 *
 * NOTE: The plugin API is not public, yet, and will be released in a later
 * version of this component.
 *
 * @see ezcWebdavRequest
 * @see ezcWebdavResponse
 * @see ezcWebdavProperty
 *
 * @package Webdav
 * @version 1.1.4
 */
abstract class ezcWebdavInfrastructureBase
{

    /**
     * Storage for the plugin data. 
     * 
     * @var array
     */
    protected $pluginData = array();

    /**
     * Sets plugin data in the storage.
     *
     * This method is used to set plugin data in the internal data storage. The
     * $namespace parameter must be the valid namespace of a plugin registered
     * with the {@link ezcWebdavPluginRegistry}. If this is not the case, an
     * {@link ezcBaseValueException} will be thrown.
     *
     * The $key parameter is a string used to identify the data added uniquely
     * inside the private storage area of the client. The $key is needed to
     * retrieve the data back using {@link $this->getPluginData()}.
     *
     * The $data to store can be of any arbitrary PHP type. If there is already
     * $data stored in the position of the data store it will be overwritten.
     *
     * @param string $namespace 
     * @param string $key 
     * @param mixed $data 
     * @return void
     *
     * @throws ezcBaseValueException
     *         if the $namespace is unknown by the {@link
     *         ezcWebdavPluginRegistry} or if $key is not a string.
     */
    public function setPluginData( $namespace, $key, $data )
    {
        if ( !ezcWebdavServer::getInstance()->pluginRegistry->hasPlugin( $namespace ) )
        {
            throw new ezcBaseValueException( 'namespace', $namespace, 'known by ezcWebdavPluginRegistry' );
        }
        if ( !is_string( $key ) )
        {
            throw new ezcBaseValueException( 'key', $key, 'string' );
        }
        $this->pluginData[$namespace][$key] = $data;
    }

    /**
     * Removes plugin data from the storage.
     *
     * Completly removes the data identified by the given plugin $namespace and
     * the data $key. If the $namespace is not a known by the global {@link
     * ezcWebdavPluginRegistry} an {@link ezcBaseValueException} will
     * be thrown. If the given $key has no data assigned inside the plugins
     * private data store, this call is silently ignored.
     * 
     * @param string $namespace 
     * @param string $key 
     * @return void
     *
     * @throws ezcBaseValueException
     *         if the $namespace is unknown by the {@link
     *         ezcWebdavPluginRegistry} or if $key is not a string.
     */
    public function removePluginData( $namespace, $key )
    {
        if ( !ezcWebdavServer::getInstance()->pluginRegistry->hasPlugin( $namespace ) )
        {
            throw new ezcBaseValueException( 'namespace', $namespace, 'known by ezcWebdavPluginRegistry' );
        }
        if ( !is_string( $key ) )
        {
            throw new ezcBaseValueException( 'key', $key, 'string' );
        }
        
        if ( isset( $this->pluginData[$namespace][$key] ) )
        {
            unset( $this->pluginData[$namespace][$key] );
        }
    }

    /**
     * Retrieves plugin data from the storage.
     *
     * This method returns the data that was stored under the given plugin
     * $namespace and data $key. If the given $namespace is unknown by the
     * global {@link ezcWebdavPluginRegistry} an {@link
     * ezcBaseValueException} will be thrown. If no data exists with
     * the given $key, null will be returned.
     * 
     * @param string $namespace 
     * @param string $key 
     * @return mixed
     *
     * @throws ezcBaseValueException
     *         if the $namespace is unknown by the {@link
     *         ezcWebdavPluginRegistry} or if $key is not a string.
     */
    public function getPluginData( $namespace, $key )
    {
        if ( !ezcWebdavServer::getInstance()->pluginRegistry->hasPlugin( $namespace ) )
        {
            throw new ezcBaseValueException( 'namespace', $namespace, 'known by ezcWebdavPluginRegistry' );
        }
        if ( !is_string( $key ) )
        {
            throw new ezcBaseValueException( 'key', $key, 'string' );
        }
        
        if ( isset( $this->pluginData[$namespace][$key] ) )
        {
            return $this->pluginData[$namespace][$key];
        }
        return null;
    }
    
    /**
     * Returns if plugin data is available in the storage.
     *
     * This method checks if there is data available for the given plugin
     * $namespace and data $key. If the given $namespace is unknown by the
     * global {@link ezcWebdavPluginRegistry} an {@link
     * ezcBaseValueException} will be thrown. If data (not null) is
     * assigned to the given key this method returns true, otherwise false.
     * 
     * @param string $namespace 
     * @param string $key 
     * @return bool
     *
     * @throws ezcBaseValueException
     *         if the $namespace is unknown by the {@link
     *         ezcWebdavPluginRegistry} or if $key is not a string.
     */
    public function hasPluginData( $namespace, $key )
    {
        if ( !ezcWebdavServer::getInstance()->pluginRegistry->hasPlugin( $namespace ) )
        {
            throw new ezcBaseValueException( 'namespace', $namespace, 'known by ezcWebdavPluginRegistry' );
        }
        if ( !is_string( $key ) )
        {
            throw new ezcBaseValueException( 'key', $key, 'string' );
        }
        
        return ( isset( $this->pluginData[$namespace][$key] ) );
    }
}

?>
