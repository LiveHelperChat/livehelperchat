<?php
/**
 * File containing the ezcWebdavLockPluginConfiguration class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Plugin configuration class for the LOCK plugin.
 *
 * To activate (load) the plugin, the user must instantiate this class and
 * submit the instance to {@link ezcWebdavPluginRegistry::registerPlugin()}.
 * 
 * @mainclass
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavLockPluginConfiguration extends ezcWebdavPluginConfiguration
{
    /**
     * Main object of the lock plugin. 
     * 
     * @var ezcWebdavLockPlugin
     */
    private $main;

    /**
     * Properties. 
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Creates a new lock plugin configuration.
     * 
     * @param ezcWebdavLockPluginOptions $options
     * @return void
     */
    public function __construct( ezcWebdavLockPluginOptions $options = null )
    {
        if ( $options === null )
        {
            $options = new ezcWebdavLockPluginOptions();
        }
        $this->properties['options'] = $options;
        $this->main                  = new ezcWebdavLockPlugin( $options );
    }

    /**
     * Returns the hooks this plugin wants to assign to.
     *
     * This method is called by {@link ezcWebdavPluginRegistry}, as soon as the
     * plugin is registered to be used. The method must return a structured
     * array, representing the hooks the plugin want to be notified about.
     *
     * The returned array must be of the following structure:
     * <code>
     *  array(
     *      '<className>' => array(
     *          '<hookName>' => array(
     *              <callback1>,
     *              <callback2>,
     *          ),
     *          '<anotherHookName>' => array(
     *              <callback3>,
     *          ),
     *      ),
     *      '<secondClassName>' => array(
     *          '<thirdHookName>' => array(
     *              <callback1>,
     *              <callback3>,
     *          ),
     *      ),
     *  )
     * </code>
     * 
     * @return array
     */
    public function getHooks()
    {
        return array(
            'ezcWebdavTransport' => array(
                'parseUnknownRequest'   => array(
                    array( $this->main, 'parseUnknownRequest' ),
                ),
                'processUnknownResponse' => array(
                    array( $this->main, 'processUnknownResponse' ),
                ),
            ),
            'ezcWebdavPropertyHandler' => array(
                'extractUnknownLiveProperty'   => array(
                    array( $this->main, 'extractUnknownLiveProperty' ),
                ),
                'serializeUnknownLiveProperty' => array(
                    array( $this->main, 'serializeUnknownLiveProperty' ),
                ),
            ),
            'ezcWebdavServer' => array(
                'receivedRequest'   => array(
                    array( $this->main, 'receivedRequest' ),
                ),
                'generatedResponse' => array(
                    array( $this->main, 'generatedResponse' ),
                ),
            ),
        );
    }

    /**
     * Returns the namespace of this plugin.
     *
     * The namespace of a plugin is a unique identifier string that allows it
     * to be recognized bejond other plugins. The namespace is used to provide
     * storage for the plugin in the 
     * 
     * @return string
     */
    public function getNamespace()
    {
        return ezcWebdavLockPlugin::PLUGIN_NAMESPACE;
    }

    /**
     * Initialize the plugin.
     *
     * This method is called after the server has be initialized to make the
     * plugin setup necessary objects and to retreive necessary information
     * from the server.
     * 
     * @return void
     */
    public function init()
    {
        $srv = ezcWebdavServer::getInstance();

        if ( !( $srv->backend instanceof ezcWebdavLockBackend ) )
        {
            throw new ezcWebdavPluginPreconditionFailedException(
                $this->getNamespace(),
                'Backend does not implement ezcWebdavLockBackend.'
            );
        }
        if ( !is_object( $srv->auth ) || !( $srv->auth instanceof ezcWebdavLockAuthorizer ) )
        {
            throw new ezcWebdavPluginPreconditionFailedException(
                $this->getNamespace(),
                'No authorizer available or authorizer does not implement ezcWebdavLockAuthorizer.'
            );
        }
        // @TODO: Check if more sanity checks must be tested?
    }

    /**
     * Sets a property.
     *
     * This method is called when an property is to be set.
     * 
     * @param string $propertyName The name of the property to set.
     * @param mixed $propertyValue The property value.
     * @return void
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'options':
                if ( !( $propertyValue instanceof ezcWebdavLockPluginOptions ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcWebdavLockPluginOptions'
                    );
                }
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }

        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property get access.
     *
     * Simply returns a given property.
     * 
     * @param string $propertyName The name of the property to get.
     * @return mixed The property value.
     *
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a write-only property.
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Returns if a property exists.
     *
     * Returns true if the property exists in the {@link $properties} array
     * (even if it is null) and false otherwise. 
     *
     * @param string $propertyName Option name to check for.
     * @return void
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }
}

?>
