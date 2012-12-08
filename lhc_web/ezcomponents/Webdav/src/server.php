<?php
/**
 * File containing the ezcWebdavServer class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Base class for creating a webdav server, capable of serving webdav requests.
 *
 * <code>
 * $server = ezcWebdavServer::getInstance();
 *
 * // Optionally register aditional transport handlers
 *   
 * // This step is only required, if you want to add custom or third party extensions
 * // implementations for special clients.
 * // Create a new configuration set for the client
 * $newClientConf = new ezcWebdavServerConfiguration(
 *     // Regular expression to match client name
 *     '(My.*Webdav\s+Cliengt)i',
 *     // Class name of transport handler, extending {@link ezcWebdavTransport}
 *     'myCustomTransportTransport'
 *     // There are more settings you can provide, see {@link 
 *     // ezcWebdavServerConfiguration}.
 * );
 * // Append the configuration at front, because the last configuration is a 
 * // catch all for misc clients.
 * $server->configurations->insertBefore( $newClientConf, 0 );
 *
 * // If you want to use a different path factory globally, you need to replace 
 * // it in every configuration.
 * $myPathFactory = new ezcWebdavBasicPathFactory( 'http://webdav.server/base/path' );
 * foreach ( $server->configuration as $config )
 * {
 *     $config->pathFactory = $myPathFactory;
 * }
 *
 * // Serve data using file backend with data in the local directory "/path"
 * // Make sure this directory is read and writable for your server and that 
 * // the umask is set accordingly in the server settings, if you want to 
 * // access the files as a different user, too.
 * $backend = new ezcWebdavBackendFile( '/path' );
 *
 * // Make the server serve WebDAV requests
 * $server->handle( $backend );
 * </code>
 *
 * @property ezcWebdavServerConfigurationManager $configurations
 *           Webdav server configuration manager, which holds and dispatches
 *           configurations that fit for a certain client.
 * @property ezcWebdavAuth $auth
 *           The central authentication mechanism for the WebDAV server. This
 *           instance will be used to perform authentication and authorization
 *           on every incoming request. A valid property value is an object
 *           that at least implements {@link ezcWebdavBasicAuthenticator} or
 *           {@link ezcWebdavDigestAuthenticator} or both. In addition {@link
 *           ezcWebdavAuthorizer} may be implemented. The default is null,
 *           indicating that no authentication/authorization is provided.
 *
 * @property-read ezcWebdavBackend $backend
 *                The backend given to {@link ezcWebdavServer->handle()}. Null
 *                before handle() was called.
 * @property-read ezcWebdavPluginRegistry $pluginRegistry
 *                The internal plugin registry. Can be accessed to register and
 *                remove plugins.
 * @property-read ezcWebdavPathFactory $pathFactory
 *                The path factory object used to translate between URIs and
 *                local paths. Configured by the {@link
 *                ezcWebdavServerConfigurationManager} when the {@link
 *                ezcWebdavServer::handle()} method is run}.
 * @property-read ezcWebdavXmlTool $xmlTool
 *                The XML tool object used for XML related operations in the
 *                server and transport level. Configured by the {@link
 *                ezcWebdavServerConfigurationManager} when the {@link
 *                ezcWebdavServer::handle()} method is run}.
 * @property-read ezcWebdavPropertyHandler $propertyHandler
 *                The property handler object used to parse and serialize
 *                WebDAV properties on the transport level. Configured by the
 *                {@link ezcWebdavServerConfigurationManager} when the {@link
 *                ezcWebdavServer::handle()} method is run}.
 * @property-read ezcWebdavTransport $transport
 *                The transport layer object used to parse and serialize WebDAV
 *                requests and responses. Configured by the {@link
 *                ezcWebdavServerConfigurationManager} when the {@link
 *                ezcWebdavServer::handle()} method is run}.
 * @property ezcWebdavServerConfigurationManager $configurations
 *           Configuration manager, handling different client configurations
 *           for this server.
 *
 * @version 1.1.4
 * @package Webdav
 * @mainclass
 */
class ezcWebdavServer
{
    /**
     * Singleton instance.
     *
     * @var ezcWebdavServer
     */
    protected static $instance;

    /**
     * Properties. 
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Creates a new instance.
     *
     * The constructor is protected due to singleton reasons. Use {@link
     * getInstance()} and then use the properties of the server to adjust its
     * configuration.
     * 
     * @return void
     */
    protected function __construct()
    {
        $this->reset();
    }

    /**
     * Returns singleton instance.
     *
     * The instantiation of 2 WebDAV servers at the same time does not make
     * sense and could possibly cause strange effects, like double sending of a
     * response. Therefore the server implements a singleton and its only
     * instance must be retrieved using this method. Configuration changes can
     * then be performed through the properties of this instance.
     * 
     * @return ezcWebdavServer
     */
    public static function getInstance()
    {
        if ( self::$instance === null )
        {
            self::$instance = new ezcWebdavServer();
        }
        return self::$instance;
    }

    /**
     * Handles the current request.
     *
     * This method is the absolute heart of the Webdav component. It is called
     * to make the server instance handle the current request. This means, a
     * {@link ezcWebdavTransport} is selected and instantiated through the
     * {@link ezcWebdavServerConfigurationManager} in {@link $configurations}.
     * This transport (and all other objects, created from the configuration)
     * is used to parse the incoming request into an instance of {@link
     * ezcWebdavRequest}, which is then handed to the submitted $backend for
     * handling. The resulting {@link ezcWebdavResponse} is serialized by the
     * {@link ezcWebdavTransport} and send back to the client.
     *
     * The method receives at least an instance of {@link ezcWebdavBackend},
     * which is used to server the request. Optionally, the request URI can be
     * submitted in $uri. If this is not the case, the request URI is
     * determined by the server variables
     * <ul>
     *  <li>$_SERVER['SERVER_NAME']</li>
     *  <li>$_SERVER['REQUEST_URI']</li>
     * </ul>
     *
     * @param ezcWebdavBackend $backend
     * @param string $uri
     * 
     * @return void
     */
    public final function handle( ezcWebdavBackend $backend, $uri = null )
    {
        $uri = ( $uri === null 
            ? 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']
            : $uri );

        // Perform final setup
        $this->properties['backend'] = $backend;
        if ( !isset( $_SERVER['HTTP_USER_AGENT'] ) )
        {
            throw new ezcWebdavMissingHeaderException( 'User-Agent' );
        }
        // Configure the server according to the requesting client
        $this->configurations->configure( $this, $_SERVER['HTTP_USER_AGENT'] );

        // Initialize all plugins
        $this->pluginRegistry->initPlugins();

        // Parse request into request object
        $request = $this->transport->parseRequest( $uri );

        // Perform authentication / authorization on the given request,
        // if it is known by the server.
        if ( $request instanceof ezcWebdavRequest && is_object( $this->properties['auth'] ) )
        {
            $res = $this->authenticate( $request );
            if ( $res !== null )
            {
                $request = $res;
            }
        }
        
        if ( $request instanceof ezcWebdavRequest )
        {
            // Plugin hook receivedRequest
            $pluginRes = ezcWebdavServer::getInstance()->pluginRegistry->announceHook(
                __CLASS__,
                'receivedRequest',
                new ezcWebdavPluginParameters(
                    array(
                        'request'  => $request,
                    )
                )
            );
            if ( is_object( $pluginRes ) && $pluginRes instanceof ezcWebdavResponse )
            {
                // Plugin already took care about processing the request
                $response = $pluginRes;
            }
            else
            {
                // Let backend process the request
                $response = $this->backend->performRequest( $request );
            }
        }
        else
        {
            // The transport layer or auth mechanism already issued an error.
            $response = $request;
        }

        // Plugin hook generatedResponse
        ezcWebdavServer::getInstance()->pluginRegistry->announceHook(
            __CLASS__,
            'generatedResponse',
            new ezcWebdavPluginParameters(
                array(
                    'response'  => $response,
                )
            )
        );

        $this->transport->handleResponse( $response );
    }

    /**
     * Initializes the server with the given objects.
     * 
     * This method is marked proteced, because it is intended to be used by by
     * {@link ezcWebdavServerConfiguration} instances and instances of derived
     * classes, but not directly.
     *
     * @param ezcWebdavPathFactory $pathFactory
     * @param ezcWebdavXmlTool $xmlTool
     * @param ezcWebdavPropertyHandler $propertyHandler
     * @param ezcWebdavHeaderHandler $headerHandler
     * @param ezcWebdavTransport $transport
     * @access protected
     * @return void
     */
    public function init(
        ezcWebdavPathFactory $pathFactory,
        ezcWebdavXmlTool $xmlTool,
        ezcWebdavPropertyHandler $propertyHandler,
        ezcWebdavHeaderHandler $headerHandler,
        ezcWebdavTransport $transport
    )
    {
        $this->properties['pathFactory']     = $pathFactory;
        $this->properties['xmlTool']         = $xmlTool;
        $this->properties['propertyHandler'] = $propertyHandler;
        $this->properties['headerHandler']   = $headerHandler;
        $this->properties['transport']       = $transport;
    }

    /**
     * Reset the server to its initial state.
     *
     * Resets the internal server state as if a new instance has just been
     * constructed.
     * 
     * @return void
     */
    public function reset()
    {
        unset( $this->properties['configurations'] );
        unset( $this->properties['pluginRegistry'] );
        $this->properties['configurations'] = new ezcWebdavServerConfigurationManager();
        $this->properties['pluginRegistry'] = new ezcWebdavPluginRegistry();
        $this->properties['auth']           = null;
        $this->properties['options']        = new ezcWebdavServerOptions();

        $this->properties['transport']       = null;
        $this->properties['backend']         = null;
        $this->properties['pathFactory']     = null;
        $this->properties['xmlTool']         = null;
        $this->properties['propertyHandler'] = null;
        $this->properties['headerHandler']   = null;
    }

    /**
     * Performs authentication and authorization. 
     * 
     * @param ezcWebdavRequest $req 
     * @return ezcWebdavErrorResponse|null
     */
    private function authenticate( ezcWebdavRequest $req )
    {
        if ( $this->properties['auth'] === null )
        {
            // No authentication
            return null;
        }

        $creds = $req->getHeader( 'Authorization' );

        $res = null;
        // Authenticate user
        
        switch ( get_class( $creds ) )
        {
            case 'ezcWebdavAnonymousAuth':
                if ( $this->properties['auth'] instanceof ezcWebdavAnonymousAuthenticator )
                {
                    $res = $this->properties['auth']->authenticateAnonymous( $creds );
                }
                break;
            case 'ezcWebdavBasicAuth':
                if ( $this->properties['auth'] instanceof ezcWebdavBasicAuthenticator )
                {
                    $res = $this->properties['auth']->authenticateBasic( $creds );
                }
                break;
            case 'ezcWebdavDigestAuth':
                if ( $this->properties['auth'] instanceof ezcWebdavDigestAuthenticator )
                {
                    $res = $this->properties['auth']->authenticateDigest( $creds );
                }
                break;
        }

        // $res is now null or bool, if not evaluates to true, authentication failed
        if ( !$res )
        {
            return $this->createUnauthenticatedResponse(
                $req->requestUri, 'Authentication failed.'
            );
        }

        return null;
    }

    /**
     * Performs authorization.
     *
     * This method does several things:
     *
     * - Check if authorization is enabled by ezcWebdavServer->$auth
     * - If it is, extract username from Authenticate header or choose ''
     * - Check authorization
     *
     * It returns true, if authorization is not enabled or succeeded. False is
     * returned otherwise.
     * 
     * @param string $path 
     * @param ezcWebdavAuth $credentials 
     * @param int $access
     * @return bool
     *
     * @todo Mark protected as soon as API is final.
     * @access private
     */
    public function isAuthorized( $path, ezcWebdavAuth $credentials, $access = ezcWebdavAuthorizer::ACCESS_READ )
    {
        $auth = $this->auth;

        if ( $auth === null || !( $auth instanceof ezcWebdavAuthorizer ) )
        {
            // No auth mechanism
            return true;
        }

        return $auth->authorize( $credentials->username, $path, $access );
    }

    /**
     * Creates an ezcWebdavErrorResponse to indicate the need for authentication.
     *
     * Creates a {@link ezcWebdavErrorResponse} object with status code {@link
     * ezcWebdavResponse::STATUS_401} and a corresponding WWW-Authenticate
     * header using the $realm define in {@link ezcWebdavServerOptions}. The
     * $uri and $desc parameters are used to create the error response.
     *
     * @param string $uri
     * @param string $desc 
     * 
     * @return ezcWebdavErrorResponse
     *
     * @access private
     */
    public function createUnauthenticatedResponse( $uri, $desc )
    {
        $res = new ezcWebdavErrorResponse( ezcWebdavResponse::STATUS_401, $uri, $desc );
        $wwwAuthHeader = array(
            'basic' => 'Basic realm="' . $this->options->realm . '"',
        );
        if ( $this->properties['auth'] instanceof ezcWebdavDigestAuthenticator )
        {
            $wwwAuthHeader['digest'] = 'Digest realm="' .$this->options->realm . '"'
                . ', nonce="' . $this->getNonce() . '"'
                . ', algorithm="MD5"';
            // @todo Do we want an opaque value here, too?
        }
        $res->setHeader( 'WWW-Authenticate', $wwwAuthHeader );

        return $res;
    }

    /**
     * Creates an ezcWebdavErrorResponse to indicate unauthorized access.
     *
     * Creates a {@link ezcWebdavErrorResponse} object with status code {@link
     * ezcWebdavResponse::STATUS_403}. The $uri and $desc parameters are used
     * to create the error response.
     *
     * @param string $uri
     * @param string $desc 
     * 
     * @return ezcWebdavErrorResponse
     *
     * @access private
     */
    public function createUnauthorizedResponse( $uri, $desc )
    {
        return new ezcWebdavErrorResponse( ezcWebdavResponse::STATUS_403, $uri, $desc );
    }

    /**
     * Creates a unique, hard to guess nounce value.
     * 
     * @return string
     */
    private function getNonce()
    {
        // This should be random enough that it cannot be guessed easily
        return md5(
            $this->options->realm 
                . ':' . microtime()
                . ':' . $_SERVER['SERVER_NAME'] 
                . ':' . uniqid( mt_rand(), true )
        );
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
            case 'configurations':
                if ( !( $propertyValue instanceof ezcWebdavServerConfigurationManager ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavServerConfigurationManager' );
                }
                break;
            case 'auth':
                if ( $propertyValue !== null
                     && ( !is_object( $propertyValue ) || !( $propertyValue instanceof ezcWebdavBasicAuthenticator ) )
                   )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavBasicAuthenticator and/or ezcWebdavDigestAuthenticator' );
                }
                break;
            case 'options':
                if ( !( $propertyValue instanceof ezcWebdavServerOptions ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavServerOptions' );
                }
                break;
            case 'backend':
            case 'pluginRegistry':
            case 'pathFactory':
            case 'xmlTool':
            case 'propertyHandler':
            case 'headerHandler':
            case 'transport':
                throw new ezcBasePropertyPermissionException( $propertyName, ezcBasePropertyPermissionException::READ );

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
