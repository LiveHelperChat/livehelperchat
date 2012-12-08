<?php
/**
 * File containing the ezcWebdavXmlTool class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Tool class to work with XML.
 *
 * An instance of this tool class is used to perform XML operations while
 * parsing incoming requests and serializing outgoing responses.
 *
 * If a client expects different behavior regarding fundamental XML handling,
 * this class can be extended. To make it being used for a certain client, the
 * new class name needs to be specified in an instance of {@link
 * ezcWebdacServerConfiguration}, which then needs to be registered in the
 * {@link ezcWebdacServerConfigurationManager} instance, located in the {@link
 * ezcWebdacServer} singleton instance.
 *
 * @property ezcWebdavNamespaceRegistry $namespaceRegistry
 *           Registry class that keeps track of used namespace URIs and their
 *           abbreviations.
 *
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavXmlTool
{
    /**
     * The default namespace, where WebDAV XML elements reside in. 
     */
    const XML_DEFAULT_NAMESPACE = 'DAV:';

    /**
     * The XML version to create DOM documents in. 
     */
    const XML_VERSION = '1.0';

    /**
     * Encoding to use to create DOM documents. 
     */
    const XML_ENCODING = 'utf-8';

    /**
     * Properties.
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Creates a new XML tool.
     *
     * Creates an new XML tool instance. If not $namespaceRegistry is provided,
     * the default {@link ezcWebdavNamespaceRegistry} will be instantiated and
     * used. The registry can be accessed through the $namespaceRegistry
     * property.
     * 
     * @param ezcWebdavNamespaceRegistry $namespaceRegistry 
     * @return void
     */
    public function __construct( ezcWebdavNamespaceRegistry $namespaceRegistry = null )
    {
        // Initialize properties
        $this->properties['namespaceRegistry'] = null;

        $this->namespaceRegistry = ( $namespaceRegistry === null ? new ezcWebdavNamespaceRegistry() : $namespaceRegistry );
    }

    /**
     * Returns a DOMDocument from the given XML.
     *
     * Creates a new DOMDocument with the options set in the class constants
     * and loads the optionally given $xml string with settings appropriate to
     * work with it. Returns false if the loading fails.
     *
     * @param sting $content 
     * @return DOMDocument|false
     * @see LIBXML_NSCLEAN
     * @see LIBXML_NOBLANKS
     *
     * @apichange The behavior of this method will be changed to the behavior
     *            of {@link createDom()} and createDom() will be dropped in the
     *            next major release (2.0).
     */
    public function createDomDocument( $content = null )
    {
        $dom = null;
        try
        {
            $dom = $this->createDom( $content );
        }
        catch ( ezcWebdavInvalidXmlException $e )
        {
            $dom = false;
        }
        
        return $dom;
    }

    /**
     * Returns a DOMDocument from the given XML.
     *
     * Creates a new DOMDocument with the options set in the class constants
     * and loads the optionally given $xml string with settings appropriate to
     * work with it. Throws an exception if the loading fails.
     *
     * @param sting $content 
     * @return DOMDocument
     * @see LIBXML_NOWARNING
     * @see LIBXML_NSCLEAN
     * @see LIBXML_NOBLANKS
     * 
     * @throws ezcWebdavInvalidRequestBodyException
     *         in case libxml produces an error with code other than 100 while
     *         loading $content.
     *
     * @apichange This method will replace {@link createDomDocument()} in the
     *            next major version (2.0) and will be renamed to
     *            createDomDocument().
     */
    public function createDom( $content = null )
    {
        // Make libxml not throw any warnings / notices.
        $oldErrorLevel = libxml_use_internal_errors( true );

        libxml_clear_errors();

        $dom = new DOMDocument( self::XML_VERSION, self::XML_ENCODING );

        if ( $content !== null && trim( $content ) !== '' )
        {
            $res = $dom->loadXML( $content, LIBXML_NSCLEAN | LIBXML_NOBLANKS );

            if ( $res === false )
            {
                throw new ezcWebdavInvalidXmlException(
                    "Libxml error.'"
                );
            }

            // Check libxml errors
            /* Libxml error checks deactivated to to incorect errors in older libxml versions.
            foreach ( libxml_get_errors() as $error )
            {
                // Code 100 = relative URI, DAV: is relative, do not bail out.
                if ( $error->code === 100 )
                {
                    continue;
                }
                // Older libxml versions don't recognize DAV: as a valid relative URI
                if ( $error->code === 99 && strpos( $error->message, 'DAV:' ) !== false )
                {
                    continue;
                }

                throw new ezcWebdavInvalidXmlException(
                    "Libxml error: {$error->code} '{$error->message}.'"
                );
            }
            */
        }
        
        // Reset old libxml error state
        $oldErrorLevel = libxml_use_internal_errors( $oldErrorLevel );
        
        return $dom;
    }

    /**
     * Returns a new DOMElement in the given namespace.
     *
     * Retrieves the shortcut for the $namespace and creates a new DOMElement
     * object with the correct global name for the given $localName.
     * 
     * @param DOMDocument $dom 
     * @param string $localName 
     * @param string $namespace 
     * @return DOMElement
     */
    public function createDomElement( DOMDocument $dom, $localName, $namespace = self::XML_DEFAULT_NAMESPACE )
    {
        return $dom->createElementNS(
            $namespace,
            "{$this->namespaceRegistry[$namespace]}:{$localName}"
        );
    }

    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $propertyValue  The value for the property.
     * @return void
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property options is not an instance of
     * @throws ezcBaseValueException
     *         If a the value for a property is out of range.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'namespaceRegistry':
                if ( !( $propertyValue instanceof ezcWebdavNamespaceRegistry ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavNamespaceRegistry' );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property read access.
     * 
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( !$this->__isset( $propertyName ) )
        {
            throw new ezcBasePropertyNotFoundException( $propertyName );
        }
            
        return $this->properties[$propertyName];
    }

    /**
     * Property isset access.
     *
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }
}

?>
