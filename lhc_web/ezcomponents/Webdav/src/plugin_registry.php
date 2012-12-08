<?php
/**
 * File containing the ezcWebdavPluginRegistry class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Global plugin registry class.
 *
 * An instance of this class is request wide uniquely responsible for handling
 * plugins to the Webdav component. It has a number of different hooks
 * available of the server and transport layer of the component to allow
 * plugins to interact and integrate with these layers to add extended
 * funtionality.
 *
 * A good overview of the working of the plugin system can be found in its
 * design document {@link Webdav/design/extensibility.txt}.
 *
 * @see ezcWebdavServer
 * @see ezcWebdavTransport
 * @see ezcWebdavPropertyHandler
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavPluginRegistry
{
    /**
     * Known hooks. 
     * 
     * <code>
     *      array(
     *          '<class>' => array(
     *              '<method>' => true,
     *              // ...
     *          )
     *          // ...
     *      )
     * </code>
     *
     * @var array(string=>array(string=>bool))
     */
    private $hooks = array();

    /**
     * Registered plugins. 
     *
     * <code>
     *      array(
     *          '<namespace>' => '<config-object>',
     *          // ...
     *      )
     * </code>
     * 
     * @var array(string=>ezcWebdavPluginConfiguration)
     */
    private $plugins = array();
    

    /**
     * Assigned hooks.
     * <code>
     *      array(
     *          '<class name>' => array(
     *              '<hook name>' => array(
     *                  '<namespace>' => array(
     *                      <callback1>,
     *                      <callback2>,
     *                      // ...
     *                  ),
     *                  // ...
     *              ),
     *              // ...
     *          ),
     *          // ...
     *      )
     * </code>
     * 
     * @var array
     */
    private $assignedHooks = array();

    /**
     * Creates a new plugin registry.
     * 
     * @return void
     */
    public function __construct()
    {
        // Transport layer hooks
        $this->createHook( 'ezcWebdavTransport', 'beforeParseRequest' );
        $this->createHook( 'ezcWebdavTransport', 'afterProcessResponse' );

        $this->createHook( 'ezcWebdavTransport', 'parseUnknownRequest' );
        $this->createHook( 'ezcWebdavTransport', 'processUnknownResponse' );

        // Property related hooks
        $this->createHook( 'ezcWebdavPropertyHandler', 'extractDeadProperty' );
        $this->createHook( 'ezcWebdavPropertyHandler', 'serializeDeadProperty' );

        $this->createHook( 'ezcWebdavPropertyHandler', 'extractUnknownLiveProperty' );
        $this->createHook( 'ezcWebdavPropertyHandler', 'serializeUnknownLiveProperty' );

        // Server layer hooks
        $this->createHook( 'ezcWebdavServer', 'receivedRequest' );
        $this->createHook( 'ezcWebdavServer', 'generatedResponse' );
    }

    /**
     * Creates a new hook.
     * 
     * Helper method. Used in {@link __construct()} to create a hook. The
     * $class identifies the base class the hook is provided by, $method
     * specificies the name of the affected method of this class or a "pseudo
     * method name", if no such is available.
     * 
     * @param string $class 
     * @param string $hook 
     * @return void
     */
    private function createHook( $class, $hook )
    {
        $this->hooks[$class][$hook] = true;
    }

    /**
     * Registers a new plugin to be used.
     *
     * Receives an instance of {@link ezcWebdavPluginConfiguration}, which is
     * possible extended for internal use in the plugin. The 'namespace'
     * property of this class is used to register it internally. Multiple
     * registrations of the same namespace will lead to an exception.
     *
     * @param ezcWebdavPluginConfiguration $config
     * @return void
     *
     * @throws ezcWebdavPluginDoubleRegistrationException
     *         if the namespace of a plugin is registered twice.
     */
    public final function registerPlugin( ezcWebdavPluginConfiguration $config )
    {
        if ( !is_string( ( $namespace = $config->getNamespace() ) ) )
        {
            throw new ezcBaseValueException( 'namespace', $namespace, 'string' );
        }
        if ( isset( $this->plugins[$namespace] ) )
        {
            throw new ezcBaseValueException( 'namespace', $namespace, 'already registered' );
        }

        if ( !is_array( ( $hooks = $config->getHooks() ) ) )
        {
            throw new ezcBaseValueException( 'hooks', $hooks, 'array' );
        }
        // Validate hooks
        foreach ( $hooks as $class => $hookInfos )
        {
            if ( !isset( $this->hooks[$class] ) )
            {
                throw new ezcWebdavInvalidHookException( $class );
            }
            foreach ( $hookInfos as $hook => $callbacks )
            {
                if ( !isset( $this->hooks[$class][$hook] ) )
                {
                    throw new ezcWebdavInvalidHookException( $class, $hook );
                }
                foreach( $callbacks as $callback )
                {
                    if ( !is_callable( $callback ) )
                    {
                        throw new ezcWebdavInvalidCallbackException( $callback );
                    }
                }
            }
        }

        // Register namespace
        $this->plugins[$namespace] = $config;

        // Register Hooks
        foreach ( $hooks as $class => $hookInfos )
        {
            foreach ( $hookInfos as $hook => $callbacks )
            {
                $this->assignedHooks[$class][$hook][$namespace] = $callbacks;
            }
        }
    }

    /**
     * Can be used to deactivate a plugin.
     *
     * Receives an instance of {@link ezcWebdavPluginConfiguration}, which is
     * possible extended for internal use in the plugin. The 'namespace'
     * property of this class is used to unregister it internally.
     * Unregistration of a notregistered $config object will be silently
     * ignored.
     *
     * @param ezcWebdavPluginConfiguration $config
     * @return void
     */
    public final function unregisterPlugin( ezcWebdavPluginConfiguration $config )
    {
        if ( !is_string( ( $namespace = $config->getNamespace() ) ) )
        {
            throw new ezcBaseValueException( 'namespace', $namespace, 'string' );
        }
        if ( !isset( $this->plugins[$namespace] ) )
        {
            throw new ezcBaseValueException( 'namespace', $namespace, 'registered' );
        }

        // Unregister namespace
        unset( $this->plugins[$namespace] );

        // Unregister hooks
        foreach ( $this->assignedHooks as $class => $hookInfos )
        {
            foreach ( $hookInfos as $hook => $pluginInfos )
            {
                if ( isset( $pluginInfos[$namespace] ) )
                {
                    unset( $this->assignedHooks[$class][$hook][$namespace] );
                }
            }
        }
    }

    /**
     * Returns a plugins configuration object.
     *
     * Returns the instance of {@link ezcWebdavPluginConfiguration} used for
     * the plugin with a given $namespace. Throws an exception, if the plugin
     * was not found.
     * 
     * @param string $namespace 
     * @return ezcWebdavPluginConfiguration
     */
    public final function getPluginConfig( $namespace )
    {
        if ( !isset( $this->plugins[$namespace] ) )
        {
            throw new ezcBaseValueException( 'namespace', $namespace, 'registered' );
        }

        return $this->plugins[$namespace];
    }

    /**
     * Returns if a plugin is active in the server.
     *
     * Checks if a configuration with the given $namespace exists and returns
     * this information as a boolean value.
     * 
     * @param string $namespace 
     * @return bool
     */
    public final function hasPlugin( $namespace )
    {
        return isset( $this->plugins[$namespace] );
    }

    /**
     * Announces the given hook.
     *
     * This class may only be used by {@link ezcWebdavServer} and {@link
     * ezcWebdavTransport} to announce the reaching of a hook. Therefore, this
     * method is marked private. Receives the name of the class issuing the
     * $hook and the $params that may be used for information extraction and
     * _careful_ possible manipulation.
     *
     * This method is declared private, because the announcement of hooks is
     * only allowed by component internal classes.
     * 
     * @param string $class
     * @param string $hook 
     * @param ezcWebdavPluginParameters $params 
     * @return void
     *
     * @throws ezcWebdavPluginFailureException
     *         in case a plugin threw an exception. The original one can be
     *         accessed for processing through the public $originalException
     *         attribute.
     *
     * @access private
     */
    public final function announceHook( $class, $hook, ezcWebdavPluginParameters $params )
    {
        // Sanity check
        if ( !isset( $this->hooks[$class][$hook] ) )
        {
            throw new RuntimeException(
                "Internal error in Webdav component. Announced non-existent hook: {$class}->{$hook}."
            );
        }

        if ( !isset( $this->assignedHooks[$class][$hook] ) )
        {
            // No plugins assigned
            return;
        }
        foreach ( $this->assignedHooks[$class][$hook] as $namespace => $callbacks )
        {
            foreach ( $callbacks as $callback )
            {
                $res = call_user_func( $callback, $params );
                // If the plugin produced a result, we terminate and return the result
                if ( $res !== null )
                {
                    return $res;
                }
            }
        }
    }

    /**
     * Initializes all registered plugins.
     *
     * This method calls the {@link ezcWebdavPluginConfiguration::init()}
     * method for each registered plugin. The method is marked as private,
     * because it is not intended for external use, but may only be called from
     * {@link ezcWebdavServer}.
     * 
     * @return void
     *
     * @access private
     */
    public final function initPlugins()
    {
        foreach ( $this->plugins as $namespace => $config )
        {
            $config->init();
        }
    }
}

?>
