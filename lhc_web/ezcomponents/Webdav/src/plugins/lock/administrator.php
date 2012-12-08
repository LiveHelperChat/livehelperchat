<?php
/**
 * File containing the ezcWebdavLockAdministrator class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Administration class for the lock plugin.
 *
 * An instance of this class might be used to administrate the Webdav lock
 * plugin. It receives the {@link ezcWebdavBackend} to work on in the
 * constructor. You should have the {@link ezcWebdavServer} already configured
 * when you use an instance of this class, since it uses settings from {@link
 * ezcWebdavLockPluginOptions}. In case the lock plugin is not configured in
 * the server, a new (default) instance of it will be added before any
 * administrative action and will be removed again afterwards. In addition, any
 * authentication/authorization mechanism configured in the server will be
 * de-activated before any administrative operation and is brought back into
 * place afterward.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavLockAdministrator
{
    /**
     * The server instance. 
     * 
     * @var ezcWebdavServer
     */
    private $server;

    /**
     * The webdav backend.
     *
     * @var ezcWebdavBackend
     */
    private $backend;

    /**
     * Backup of the server authentication. 
     * 
     * @var ezcWebdavServerAuthenticator
     */
    private $serverAuth;

    /**
     * If the server had the lock plugin defined before it was prepared. 
     * 
     * @var bool
     */
    private $serverHadLockPlugin;

    /**
     * Creates a new lock purger.
     *
     * $backend must be the back end used in the Webdav environment. Note, that
     * you should have the {@link ezcWebdavServer} configured, before you use
     * the administrator class. Some actions will check for the lock plugin and
     * use the options set for it. In case the lock plugin is not registered in
     * the server, it will be registered before an action takes place and
     * unregistered afterwards.
     * 
     * @param ezcWebdavBackend $backend
     */
    public function __construct( ezcWebdavBackend $backend )
    {
        $this->server  = ezcWebdavServer::getInstance();
        $this->backend = $backend;
    }

    /**
     * Purges all outdated locks under $path.
     *
     * This method analyses all locks under $path and purges the locks which
     * have not been accessed for the timeout configured in the lock property.
     *
     * @param string $path 
     * @return void
     *
     * @throws ezcWebdavLockPurgerException
     *         in case an error occurs during the lock purge process. Error
     *         might occur if the user identified by $adminCredentials does not
     *         have the appropriate permissions to write resources or if the
     *         backend is inconsistent.
     */
    public function purgeLocks( $path = '/' )
    {
        $this->prepareEnvironment();

        try
        {
            $purger = new ezcWebdavLockPurger( $this->backend );
            $purger->purgeLocks( $path );
        }
        catch ( ezcWebdavException $e )
        {
            $this->restoreEnvironment();
            throw $e;
        }
        
        $this->restoreEnvironment();
    }

    /**
     * Prepares the server for administrative operations.
     *
     * Removes any configured auth mechanism in the server and stores it for
     * later restoring. Registers the lock plugin, if it is not registered,
     * yet. Locks the backend.
     */
    private function prepareEnvironment()
    {
        if ( $this->server->auth !== null )
        {
            $this->serverAuth   = $this->server->auth;
            $this->server->auth = null;
        }
        
        $lockConf = null;
        if ( !$this->server->pluginRegistry->hasPlugin(
                ezcWebdavLockPlugin::PLUGIN_NAMESPACE
             )
        )
        {
            $lockConf = new ezcWebdavLockPluginConfiguration();
            $this->server->pluginRegistry->registerPlugin(
                $lockConf
            );
            $this->serverHadLockPlugin = false;
        }
        else
        {
            $lockConf = $this->server->pluginRegistry->getPluginConfig(
                ezcWebdavLockPlugin::PLUGIN_NAMESPACE
            );
            $this->serverHadLockPlugin = true;
        }

        $this->backend->lock(
            $lockConf->options->backendLockWaitTime,
            $lockConf->options->backendLockTimeout
        );
    }

    /**
     * Restores the original server settings.
     * 
     * Restores the original server auth (if it was set) and removes the lock
     * plugin again, if it was not set before. Unlocks the backend.
     */
    private function restoreEnvironment()
    {
        $this->backend->unlock();

        if ( $this->serverAuth !== null )
        {
            $this->server->auth = $this->serverAuth;
        }

        if ( !$this->serverHadLockPlugin )
        {
            $this->server->pluginRegistry->unregisterPlugin(
                new ezcWebdavLockPluginConfiguration()
            );
        }

        $this->serverAuth          = null;
        $this->serverHadLockPlugin = null;
    }
}

?>
