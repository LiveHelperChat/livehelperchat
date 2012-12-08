<?php
/**
 * File containing the ezcWebdavServerConfiguration class
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class containing the configuration for a specific client.
 *
 * An instance of this class represents the configuration of {@link
 * ezcWebdavServer} for a specific client. The {@link
 * ezcWebdavServerConfigurationManager} holds a default set of such objects,
 * representing the configurations that are known by the Webdav component by
 * default.
 *
 * You can instantiate more objects of this class to add custom configurations
 * and possibly even extend it to support more advanced features.
 *
 * An object of this class can configure the {@link ezcWebdavServer} instances
 * in the way that is suitable to serve the requests send by a certain client
 * and to serialize proper responses for it, when requested by the {@link
 * ezcWebdavServerConfigurationManager} through the {@link configure()} method.
 *
 * The property $userAgentRegex determines the PCRE that is used to match
 * against the User-Agent HTTP header. If the regex matches, the configuration
 * is used to configure the {@link ezcWebdavServer} instance. The default regex
 * will match always and therefore always as the last fallback and will make
 * the server act RFC conform.
 *
 * The $transport property represents the class to be instantiated as the
 * transport layer. The default is {@link ezcWebdavTransport}, which is the RFC
 * compliant transport implementation.
 *
 * $xmlTool defaults to an instance of {@link ezcWebdavXmlTool}, but may be
 * configured to be a class implementing the same interface or even an extended
 * one.  The premission is, that the corresponding {@link ezcWebdavTransport}
 * and {@link ezcWebdavPropertyHandler} are able to use the instance of this
 * class for XML handling purposes.
 *
 * The property $propertyHandler is responsible for extraction of and
 * serialization to XML of dead and live properties. This may be replaced,
 * if a transport needs or provides non-conform  property XML.
 *
 * @property string $userAgentRegex
 *           PCRE that is used to match against the User-Agent header. If this
 *           regex matches, this configuration object is used to configure the
 *           {@link ezcWebdavServer} instance, according to the other
 *           properties.
 * @property string $transportClass
 *           Transport class to instantiate when creating an instance of the
 *           transport layer configured in this object. 
 * @property ezcWebdavPathFactory $pathFactory
 *           Object used to transform incoming request URIs into request paths,
 *           that can be handled by the {@link ezcWebdavBackend}. Default is
 *           {@link ezcWebdavAutomaticPathFactory}. This is the only place
 *           where an object is expected, since transport implementations
 *           should not rely on a specific path factory and that means 1 path
 *           factory can be used for all transport configurations.
 * @property string $xmlToolClass
 *           This property defines the {@link ezcWebdavXmlTool} instance to be
 *           used with the {@link ezcWebdavTransport} class configured in
 *           $transportClass and the {@link ezcWebdavPropertyHandler} class
 *           configured in $propertyHandlerClass.
 * @property string $propertyHandlerClass
 *           This property defines the {@link ezcWebdavPropertyHandler} class
 *           to use, when instanciating the {@link ezcWebdavTransport} in
 *           $transportClass. The class given here will receive $xmlTool as a
 *           parameter, to work with.
 *
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavServerConfiguration
{
    /**
     * Properties. 
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Creates a new instance.
     *
     * All parameters are strings, representing the specific classes to use,
     * exception for $pathFactory, which must be a valid path factory instance.
     * The classes defined in the other parameters will be set as properties
     * and instantiated when a server configuration is requested through the
     * {@link configure()} method, by the {@link
     * ezcWebdavServerConfigurationManager} instance hold in {@link
     * ezcWebdavServer}.
     * 
     * @param string $userAgentRegex 
     * @param string $transportClass
     * @param string $xmlToolClass 
     * @param string $propertyHandlerClass
     * @param string $headerHandlerClass
     * @param ezcWebdavPathFactory $pathFactory 
     * @return void
     */
    public function __construct(
        $userAgentRegex                   = '(.*)',
        $transportClass                   = 'ezcWebdavTransport',
        $xmlToolClass                     = 'ezcWebdavXmlTool',
        $propertyHandlerClass             = 'ezcWebdavPropertyHandler',
        $headerHandlerClass               = 'ezcWebdavHeaderHandler',
        ezcWebdavPathFactory $pathFactory = null
    )
    {
        $this->properties['userAgentRegex']       = null;
        $this->properties['transportClass']       = null;
        $this->properties['xmlToolClass']         = null;
        $this->properties['propertyHandlerClass'] = null;
        $this->properties['headerHandlerClass']   = null;
        $this->properties['pathFactory']          = null;

        $this->userAgentRegex       = $userAgentRegex;
        $this->transportClass       = $transportClass;
        $this->xmlToolClass         = $xmlToolClass;
        $this->propertyHandlerClass = $propertyHandlerClass;
        $this->headerHandlerClass   = $headerHandlerClass;
        $this->pathFactory          = ( $pathFactory === null ? new ezcWebdavAutomaticPathFactory() : $pathFactory );
    }

    /**
     * Configures the server for handling a request.
     *
     * This method takes the instance of {@link ezcWebdavServer} in $server and
     * configures this instance according to the configuration represented.
     * After calling this method, the {@link ezcWebdavServer} instance in
     * $server is ready to handle a request.
     *
     * This method is not intended to be called directly, but by {@link
     * ezcWebdavServerConfigurationManager}, when requested to configure the
     * server.
     * 
     * @param ezcWebdavServer $server
     * @return void
     */
    public function configure( ezcWebdavServer $server )
    {
        $this->checkClasses();

        $xmlTool         = new $this->xmlToolClass();
        $propertyHandler = new $this->propertyHandlerClass();
        $headerHandler   = new $this->headerHandlerClass();
        $transport       = new $this->transportClass();
        $pathFactory     = $this->pathFactory;

        $server->init( $pathFactory, $xmlTool, $propertyHandler, $headerHandler, $transport );
    }

    /**
     * Checks the availability of all classes to instantiate.
     *
     * This method checks all classes stored in the configuration for existance
     * and validity. If an error is found, an {@link ezcBaseValueException} is
     * issued.
     * 
     * @return void
     *
     * @throws ezcBaseValueException
     *         if a property does not contain a class valid to be used with
     *         this configuration class or if a given class does not exist.
     */
    protected function checkClasses()
    {
        foreach ( $this->properties as $propertyName => $propertyValue )
        {
            if ( $propertyName !== 'userAgentRegex' && is_string( $propertyValue ) && !class_exists( $propertyValue ) )
            {
                throw new ezcBaseValueException( $propertyName, $propertyValue, 'name of existining, loadable class' );
            }
        }
        switch ( true )
        {
            case ( $this->transportClass !== 'ezcWebdavTransport' && !is_subclass_of( $this->transportClass, 'ezcWebdavTransport' ) ):
                throw new ezcBaseValueException( 'transportClass', $this->transportClass, 'ezcWebdavTransport or derived' );

            case ( !( $this->pathFactory instanceof ezcWebdavPathFactory ) ):
                throw new ezcBaseValueException( 'pathFactory', $this->pathFactory, 'ezcWebdavPathFactory implementation' );

            case ( $this->xmlToolClass !== 'ezcWebdavXmlTool' && !is_subclass_of( $this->xmlToolClass, 'ezcWebdavXmlTool' ) ):
                throw new ezcBaseValueException( 'xmlToolClass', $this->xmlToolClass, 'ezcWebdavXmlTool or derived' );

            case ( $this->propertyHandlerClass !== 'ezcWebdavPropertyHandler' && !is_subclass_of( $this->propertyHandlerClass, 'ezcWebdavPropertyHandler' ) ):
                throw new ezcBaseValueException( 'propertyHandlerClass', $this->propertyHandlerClass, 'ezcWebdavPropertyHandler or derived' );

            case ( $this->headerHandlerClass !== 'ezcWebdavHeaderHandler' && !is_subclass_of( $this->headerHandlerClass, 'ezcWebdavHeaderHandler' ) ):
                throw new ezcBaseValueException( 'headerHandlerClass', $this->headerHandlerClass, 'ezcWebdavHeaderHandler or derived' );
        }
    }

    /**
     * Property set access.
     *
     * Sets a property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return void
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'userAgentRegex':
            case 'transportClass':
            case 'xmlToolClass':
            case 'propertyHandlerClass':
            case 'headerHandlerClass':
                if ( !is_string( $propertyValue ) || strlen( $propertyValue ) < 1 )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'string, length > 0' );
                }
                break;
            case 'pathFactory':
                if ( !is_object( $propertyValue ) || !( $propertyValue instanceof ezcWebdavPathFactory ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavPathFactory' );
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
