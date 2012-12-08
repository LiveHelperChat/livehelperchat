<?php
/**
 * File containing the ezcWebdavPropertyHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Handles the parsing and serailization of live and dead properties.
 *
 * An instance of this class is used by {@link ezcWebdavTransport} and {@link
 * ezcWebdavFileBackend} to parse {@link ezcWebdavLiveProperty} and {@link
 * ezcWebdavDeadProperty} instances from XML content and to re-serialized
 * instances of these classes back to XML.
 *
 * {@link ezcWebdavTransport} might be configured to use a different property
 * handler, to adjust the behavior of property handling to specific client
 * needs. {@link ezcWebdavFileBackend} always uses this default implementation.
 *
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavPropertyHandler
{
    /**
     * XML tool. 
     * 
     * @var ezcWebdavXmlTool
     */
    protected $xmlTool;

    /**
     * Regedx to parse the <getcontenttype /> XML elemens content.
     *
     * Example: 'text/html; charset=UTF-8'
     */
    const GETCONTENTTYPE_REGEX = '(^(?P<mime>\w+/\w+)\s*(?:;\s*charset\s*=\s*(?P<charset>.+)\s*)?$)i';

    /**
     * Creates a new property handler.
     *
     * An instance of this class is capable of handling live and dead WebDAV
     * properties. It can extract properties from requests and generate
     * response information for properties. If $xml is not specified, the
     * instance in {@link ezcWebdavServer} will be used, which propably
     * underlies client specific adjustments.
     *
     * The {@link ezcWebdavXmlTool} instance of {@link ezcWebdavServer} can be
     * configured using a {@link ezcWebdavServerConfiguration} in the {@link
     * ezcWebdavServerConfigurationManager} of the {@link ezcWebdavServer}
     * singleton instance. The XML instance is created as soon as the server is
     * configured for a specific client.
     * 
     * @param ezcWebdavXmlTool $xml 
     * @return void
     */
    public function __construct( ezcWebdavXmlTool $xml = null )
    {
        if ( $xml !== null )
        {
            $this->xmlTool = $xml;
        }
    }

    /**
     * Returns the XML tool to work with.
     *
     * This method either returns the internally ({@link $xmlTool}) instance of
     * {@link ezcWebdavXmlTool} or, if this one is not available, the instance
     * stored in the singleton of {@link ezcWebdavServer}. The latter instance
     * might be an extended one, which is adjusted to the special needs of a
     * certain client.
     * 
     * @return ezcWebdavXmlTool
     */
    protected function getXmlTool()
    {
        if ( $this->xmlTool === null )
        {
            return ezcWebdavServer::getInstance()->xmlTool;
        }
        return $this->xmlTool;
    }

    /**
     * Returns extracted properties in an ezcWebdavPropertyStorage.
     *
     * This method receives a DOMNodeList $domNodes which must contain a set
     * of DOMElement objects, while each of those represents a WebDAV property.
     *
     * The list may contain live properties as well as dead ones. Live
     * properties ({@link ezcWebdavLiveProperty}) as defined in RFC 2518 are
     * currently recognized, except for locking related properties. All other
     * properties in the DAV: namespace are added as dead properties ({@link
     * ezcWebdavDeadProperty}). Dead properties are parsed generally in any
     * namespace.
     *
     * The extracted properties are stored in the given {@link
     * ezcWebdavPropertyStorage} $storage. If a $flag value is provided, this
     * one is submitted as the second parameter to {@link
     * ezcWebdavFlaggedPropertyStorage->attach()}.
     *  
     * @param DOMNodeList $domNodes 
     * @param ezcWebdavBasicPropertyStorage $storage
     * @param int $flag
     * @return ezcWebdavBasicPropertyStorage
     */
    public final function extractProperties( DOMNodeList $domNodes, ezcWebdavBasicPropertyStorage $storage, $flag = null )
    {
        for ( $i = 0; $i < $domNodes->length; ++$i )
        {
            $currentNode = $domNodes->item( $i );
            if ( $currentNode->nodeType !== XML_ELEMENT_NODE )
            {
                // Skip
                continue;
            }

            // Initialize
            $property = null;

            // DAV: namespace indicates live property! If parsing live fails, a dead property is returned
            if ( $currentNode->namespaceURI === ezcWebdavXmlTool::XML_DEFAULT_NAMESPACE )
            {
                $property = $this->dispatchExtractLiveProperty( $currentNode );
            }
            // Other namespaces are always dead properties
            else
            {
                $property = $this->dispatchExtractDeadProperty( $currentNode );
            }

            $flag === null ? $storage->attach( $property ) : $storage->attach( $property, $flag );
        }
        return $storage;
    }

    /**
     * Dispatches the extraction of a live property.
     *
     * This method takes care for dispatching to the plugin registry takes
     * place before and after the actual live property is extracted.
     * Additionally the extractUnknownLiveProperty is announced, if the
     * property could not be parsed internally. If the property still cannot be
     * parsed, it is dispatched to the dead property parsing. This also
     * includes the additional hook announcements.
     * 
     * @param DOMElement $element 
     * @return ezcWebdavLiveProperty
     */
    private function dispatchExtractLiveProperty ( DOMElement $element )
    {
        $property = $this->extractLiveProperty( $element );

        // First, let a plugin try
        if ( $property === null )
        {
            // Plugin hook extractUnknownLiveProperty
            $property = ezcWebdavServer::getInstance()->pluginRegistry->announceHook(
                __CLASS__,
                'extractUnknownLiveProperty',
                new ezcWebdavPluginParameters(
                    array(
                        'domElement'  => $element,
                        'xmlTool'     => $this->getXmlTool(),
                    )
                )
            );
        }

        if ( $property === null )
        {
            // Second, parse dead property instead
            $property = $this->dispatchExtractDeadProperty( $element );
        }
        
        return $property;
    }

    /**
     * Dispatches the extraction of a dead property.
     *
     * This method takes care that the dispatching to the plugin registry takes
     * place before and after the actual dead property is extracted.
     * 
     * @param DOMElement $element 
     * @return ezcWebdavDeadProperty
     */
    private function dispatchExtractDeadProperty ( DOMElement $element )
    {
        // Plugin hook beforeExtractDeadProperty
        $property = ezcWebdavServer::getInstance()->pluginRegistry->announceHook(
            __CLASS__,
            'extractDeadProperty',
            new ezcWebdavPluginParameters(
                array(
                    'domElement' => $element,
                    'xmlTool'    => $this->getXmlTool(),
                )
            )
        );

        // No plugin wanted to parse the property, parse default
        if ( $property === null )
        {
            $property = $this->extractDeadProperty( $element );
        }

        return $property;
    }

    /**
     * Extract a dead property from a DOMElement.
     *
     * This method is responsible for parsing a {@link ezcWebdavDeadProperty}
     * (unknown) property from a $domElement.
     * 
     * @param DOMElement $domElement 
     * @return ezcWebdavDeadProperty
     */
    protected function extractDeadProperty( DOMElement $domElement )
    {
        // Create standalone XML for property
        // It may possibly occur, that shortcut clashes occur...
        $propDom    = new DOMDocument();
        $copiedNode = $propDom->importNode( $domElement, true );
        $propDom->appendChild( $copiedNode );
        
        return new ezcWebdavDeadProperty(
            (string) $domElement->namespaceURI,
            $domElement->localName,
            $propDom->saveXML()
        );
    }

    /**
     * Extracts a live property from a DOMElement.
     *
     * This method is responsible for parsing WebDAV live properties. The
     * DOMElement $domElement must be an XML element in the DAV: namepsace. If
     * the received property is not defined in RFC 2518, null is returned.
     * 
     * @param DOMElement $domElement 
     * @return ezcWebdavLiveProperty|null
     */
    protected function extractLiveProperty( DOMElement $domElement )
    {
        $property = null;
        switch ( $domElement->localName )
        {
            case 'creationdate':
                $property = new ezcWebdavCreationDateProperty();
                if ( trim( $domElement->nodeValue ) !== '' )
                {
                    $property->date = new ezcWebdavDateTime( $domElement->nodeValue );
                }
                break;
            case 'displayname':
                $property = new ezcWebdavDisplayNameProperty();
                if ( trim( $domElement->nodeValue ) !== '' )
                {
                    $property->displayName = $domElement->nodeValue;
                }
                break;
            case 'getcontentlanguage':
                $property = new ezcWebdavGetContentLanguageProperty();
                if ( trim( $domElement->nodeValue ) !== '' )
                {
                    // e.g. 'de, en'
                    $property->languages = array_map( 'trim', explode( ',', $domElement->nodeValue ) );
                }
                break;
            case 'getcontentlength':
                $property = new ezcWebdavGetContentLengthProperty();
                if ( trim( $domElement->nodeValue ) !== '' )
                {
                    $property->length = trim( $domElement->nodeValue );
                }
                break;
            case 'getcontenttype':
                $property = new ezcWebdavGetContentTypeProperty();
                // @todo: Should this throw an exception, if the match fails?
                // Currently, the property stays empty and the backend needs to handle this
                if ( trim( $domElement->nodeValue ) !== '' 
                  && preg_match( self::GETCONTENTTYPE_REGEX, $domElement->nodeValue, $matches ) > 0 )
                {
                    $property->mime    = $matches['mime'];

                    if ( isset( $matches['charset'] ) )
                    {
                        $property->charset = $matches['charset'];
                    }
                }
                break;
            case 'getetag':
                $property = new ezcWebdavGetEtagProperty();
                if ( trim( $domElement->nodeValue ) !== '' )
                {
                    $property->etag = $domElement->nodeValue;
                }
                break;
            case 'getlastmodified':
                $property = new ezcWebdavGetLastModifiedProperty();
                if ( trim( $domElement->nodeValue ) !== '' )
                {
                    $property->date = new ezcWebdavDateTime( $domElement->nodeValue );
                }
                break;
            case 'resourcetype':
                $property = new ezcWebdavResourceTypeProperty();
                if ( trim( $domElement->nodeValue ) !== '' )
                {
                    $property->type = $domElement->nodeValue;
                }
                break;
            case 'source':
                $property = new ezcWebdavSourceProperty();
                if ( $domElement->hasChildNodes() )
                {
                    $property->links = $this->extractLinkContent( $domElement );
                }
                break;
            default:
                return null;
        }
        return $property;
    }

    /**
     * Serializes an object of new ezcWebdavPropertyStorage to XML.
     *
     * Attaches all properties of the $storage to the $parentElement XML
     * element in their XML representation.
     * 
     * @param ezcWebdavPropertyStorage $storage 
     * @param DOMElement $parentElement 
     * @return void
     */
    public final function serializeProperties( ezcWebdavPropertyStorage $storage, DOMElement $parentElement )
    {
        foreach ( $storage as $property )
        {
            if ( $property instanceof ezcWebdavLiveProperty )
            {
                $propertyElement = $this->serializeLiveProperty( $property, $parentElement );

                // Attempt plugins to parse an unknown live property
                if ( $propertyElement === null )
                {
                    // Plugin hook beforeSerializeLiveProperty
                    $propertyElement = ezcWebdavServer::getInstance()->pluginRegistry->announceHook(
                        __CLASS__,
                        'serializeUnknownLiveProperty',
                        new ezcWebdavPluginParameters(
                            array(
                                'property'      => $property,
                                'xmlTool'       => $this->getXmlTool(),
                                'parentElement' => $parentElement,
                            )
                        )
                    );
                }
            }
            else
            {
                // Plugin hook serializeDeadProperty
                $propertyElement = ezcWebdavServer::getInstance()->pluginRegistry->announceHook(
                    __CLASS__,
                    'serializeDeadProperty',
                    new ezcWebdavPluginParameters(
                        array(
                            'property' => $property,
                            'xmlTool'  => $this->getXmlTool(),
                        )
                    )
                );

                // No plugin wanted to serialize the propery
                if ( $propertyElement === null )
                {
                    if ( $property === false )
                    {
                        var_dump( $storage );
                    }
                    $propertyElement = $this->serializeDeadProperty( $property, $parentElement );
                }
            }
            if ( $propertyElement instanceof DOMNode )
            {
                $parentElement->appendChild( $propertyElement );
            }
        }
    }

    // Extracting

    /**
     * Extracts the <link /> XML elements.
     *
     * This method extracts the <link /> XML elements from the <source />
     * element and returns the corresponding {@link
     * ezcWebdavSourcePropertyLink} object to be used as the content of {@link
     * ezcWebdavSourceProperty}.
     * 
     * @param DOMElement $domElement 
     * @return ezcWebdavSourcePropertyLink
     */
    protected function extractLinkContent( DOMElement $domElement )
    {
        $links = array();

        $linkElements = $domElement->getElementsByTagNameNS(
            ezcWebdavXmlTool::XML_DEFAULT_NAMESPACE, 'link'
        );
        for ( $i = 0; $i < $linkElements->length; ++$i )
        {
            $links[] = new ezcWebdavSourcePropertyLink(
                $linkElements->item( $i )->getElementsByTagNameNS( ezcWebdavXmlTool::XML_DEFAULT_NAMESPACE, 'src' )->nodeValue,
                $linkElements->item( $i )->getElementsByTagNameNS( ezcWebdavXmlTool::XML_DEFAULT_NAMESPACE, 'dst' )->nodeValue
            );
        }
        return $links;
    }

    // Serializing

    /**
     * Returns the XML representation of a dead property.
     *
     * Returns a DOMElement, representing the content of the given $property in
     * XML. The newly created element is also appended as a child to the given
     * $parentElement.
     * 
     * @param ezcWebdavDeadProperty $property 
     * @param DOMElement $parentElement 
     * @return DOMElement
     */
    protected function serializeDeadProperty( ezcWebdavDeadProperty $property, DOMElement $parentElement )
    {
        if ( $property->content === null )
        {
            return $this->getXmlTool()->createDomElement(
                $parentElement->ownerDocument,
                $property->name,
                $property->namespace
            );
        }

        $contentDom = $this->getXmlTool()->createDom( $property->content );
        return  $parentElement->ownerDocument->importNode( $contentDom->documentElement, true );
    }

    /**
     * Returns the XML representation of a live property.
     *
     * Returns a DOMElement, representing the content of the given $property.
     * The newly created element is also appended as a child to the given
     * $parentElement.
     *
     * In case the given property is not recodnized, null is returned to
     * indicate that a plugin hook must be announced to see if a plugin can
     * serialize the property.
     * 
     * @param ezcWebdavLiveProperty $property 
     * @param DOMElement $parentElement 
     * @return DOMElement|null
     */
    protected function serializeLiveProperty( ezcWebdavLiveProperty $property, DOMElement $parentElement )
    {
        switch ( get_class( $property ) )
        {
            case 'ezcWebdavCreationDateProperty':
                $elementName  = 'creationdate';
                $elementValue = ( $property->date !== null ? $property->date->format( DATE_ISO8601 ) : null );
                break;
            case 'ezcWebdavDisplayNameProperty':
                $elementName  = 'displayname';
                $elementValue = $property->displayName;
                break;
            case 'ezcWebdavGetContentLanguageProperty':
                $elementName  = 'getcontentlanguage';
                $elementValue = ( count( $property->languages ) > 0 ? implode( ', ', $property->languages ) : null );
                break;
            case 'ezcWebdavGetContentLengthProperty':
                $elementName  = 'getcontentlength';
                $elementValue = $property->length;
                break;
            case 'ezcWebdavGetContentTypeProperty':
                $elementName  = 'getcontenttype';
                $elementValue = ( $property->mime !== null ? $property->mime . ( $property->charset === null ? '' : '; charset="' . $property->charset . '"' ) : null );
                break;
            case 'ezcWebdavGetEtagProperty':
                $elementName  = 'getetag';
                $elementValue = $property->etag;
                break;
            case 'ezcWebdavGetLastModifiedProperty':
                $elementName  = 'getlastmodified';
                $elementValue = ( $property->date !== null ? $property->date->format( DATE_RFC1123 ) : null );
                break;
            case 'ezcWebdavResourceTypeProperty':
                $elementName  = 'resourcetype';
                $elementValue = ( $property->type === ezcWebdavResourceTypeProperty::TYPE_COLLECTION ? array( $this->getXmlTool()->createDomElement( $parentElement->ownerDocument, 'collection' ) ) : null );
                break;
            case 'ezcWebdavSourceProperty':
                $elementName  = 'source';
                $elementValue = ( $property->links !== null ? $this->serializeLinkContent( $property->links, $parentElement->ownerDocument ) : null );
                break;
            default:
                // Now let the plugin registry hook in
                return null;
        }

        $propertyElement = $parentElement->appendChild( 
            $this->getXmlTool()->createDomElement( $parentElement->ownerDocument, $elementName, $property->namespace )
        );

        if ( $elementValue instanceof DOMDocument )
        {
            $propertyElement->appendChild(
                $dom->importNode( $elementValue->documentElement, true )
            );
        }
        else if ( is_array( $elementValue ) )
        {
            foreach ( $elementValue as $subValue )
            {
                $propertyElement->appendChild( $subValue );
            }
        }
        else if ( is_scalar( $elementValue ) )
        {
            $propertyElement->nodeValue = $elementValue;
        }

        return $propertyElement;
    }

    /**
     * Serializes an array of ezcWebdavSourcePropertyLink elements to XML.
     *
     * This method takes an array of {@link ezcWebdavSourcePropertyLink}
     * instances, which are serialized to DOMElement objects (using the given
     * $dom) to be added to a {@link ezcWebdavSourceProperty} XML
     * representation. The DOMElement instances are returned in an array.
     * 
     * @param array(ezcWebdavSourcePropertyLink) $links 
     * @param DOMDocument $dom To create the returned DOMElements.
     * @return array(DOMElement)
     */
    protected function serializeLinkContent( array $links = null, DOMDocument $dom )
    {
        $linkContentElements = array();

        foreach ( $links as $link )
        {
            $linkElement = $this->getXmlTool()->createDomElement( $dom, 'link' );
            $linkElement->appendChild(
                $this->getXmlTool()->createDomElement( $dom, 'src' )
            )->nodeValue = $link->src;
            $linkElement->appendChild(
                $this->getXmlTool()->createDomElement( $dom, 'dst' )
            )->nodeValue = $link->dst;
            $linkContentElements[] = $linkElement;
        }

        return $linkContentElements;
    }
}

?>
