<?php
/**
 * File containing the ezcFeedRss1 class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class providing parsing and generating of RSS1 feeds.
 *
 * Specifications:
 * {@link http://web.resource.org/rss/1.0/spec RSS1 Specifications}
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedRss1 extends ezcFeedProcessor implements ezcFeedParser
{
    /**
     * Defines the feed type of this processor.
     */
    const FEED_TYPE = 'rss1';

    /**
     * Defines the feed content type of this processor.
     */
    const CONTENT_TYPE = 'application/rss+xml';

    /**
     * Defines the namespace for RSS1 (RDF) feeds.
     */
    const NAMESPACE_URI = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';

    /**
     * Creates a new RSS1 processor.
     *
     * @param ezcFeed $container The feed data container used when generating
     */
    public function __construct( ezcFeed $container )
    {
        $this->feedContainer = $container;
        $this->feedType = self::FEED_TYPE;
        $this->contentType = self::CONTENT_TYPE;
    }

    /**
     * Returns an XML string from the feed information contained in this
     * processor.
     *
     * @return string
     */
    public function generate()
    {
        $this->xml = new DOMDocument( '1.0', 'utf-8' );
        $this->xml->formatOutput = 1;
        $this->createRootElement( '2.0' );

        $this->generateChannel();
        $this->generateFeedModules( $this->channel );
        $this->generateItems();
        $this->generateImage();
        $this->generateTextInput();

        return $this->xml->saveXML();
    }

    /**
     * Returns true if the parser can parse the provided XML document object,
     * false otherwise.
     *
     * @param DOMDocument $xml The XML document object to check for parseability
     * @return bool
     */
    public static function canParse( DOMDocument $xml )
    {
        if ( strpos( $xml->documentElement->tagName, 'RDF' ) === false )
        {
            return false;
        }

        $namespaceUri = $xml->documentElement->lookupNamespaceURI( null );

        // RSS 0.90
        if ( $namespaceUri === "http://channel.netscape.com/rdf/simple/0.9/" )
        {
            return false;
        }

        return true;
    }

    /**
     * Parses the provided XML document object and returns an ezcFeed object
     * from it.
     *
     * @throws ezcFeedParseErrorException
     *         If an error was encountered during parsing.
     *
     * @param DOMDocument $xml The XML document object to parse
     * @return ezcFeed
     */
    public function parse( DOMDocument $xml )
    {
        $feed = new ezcFeed( self::FEED_TYPE );
        $rssChildren = $xml->documentElement->childNodes;
        $channel = null;

        $this->usedPrefixes = $this->fetchUsedPrefixes( $xml );

        foreach ( $rssChildren as $rssChild )
        {
            if ( $rssChild->nodeType === XML_ELEMENT_NODE
                 && $rssChild->tagName === 'channel' )
            {
                $channel = $rssChild;
            }
        }

        if ( $channel === null )
        {
            throw new ezcFeedParseErrorException( null, "No channel tag" );
        }

        if ( $channel->hasAttributeNS( self::NAMESPACE_URI, 'about' ) )
        {
            $feed->id = $channel->getAttributeNS( self::NAMESPACE_URI, 'about' );
        }

        foreach ( $channel->childNodes as $channelChild )
        {
            if ( $channelChild->nodeType == XML_ELEMENT_NODE )
            {
                $tagName = $channelChild->tagName;

                switch ( $tagName )
                {
                    case 'title':
                    case 'link':
                    case 'description':
                        $feed->$tagName = $channelChild->textContent;
                        break;

                    case 'items':
                        $seq = $channelChild->getElementsByTagNameNS( self::NAMESPACE_URI, 'Seq' );
                        if ( $seq->length === 0 )
                        {
                            break;
                        }

                        $lis = $seq->item( 0 )->getElementsByTagNameNS( self::NAMESPACE_URI, 'li' );

                        foreach ( $lis as $el )
                        {
                            $resource = $el->getAttribute( 'resource' );
                            if ( empty( $resource ) )
                            {
                                // some RSS1 (RDF) feeds specify the "resource" attribute as "rdf:resource"
                                // see issue #13109
                                $resource = $el->getAttributeNS( self::NAMESPACE_URI, 'resource' );
                            }

                            $item = $this->getNodeByAttributeNS( $xml->documentElement, 'item', self::NAMESPACE_URI, 'about', $resource );
                            if ( $item instanceof DOMElement )
                            {
                                $element = $feed->add( 'item' );
                                $this->parseItem( $feed, $element, $item );
                            }
                        }
                        break;

                    case 'image':
                        $resource = $channelChild->getAttributeNS( self::NAMESPACE_URI, 'resource' );

                        $image = $this->getNodeByAttributeNS( $xml->documentElement, 'image', self::NAMESPACE_URI, 'about', $resource );
                        $this->parseImage( $feed, $image );
                        break;

                    case 'textinput':
                        $resource = $channelChild->getAttributeNS( self::NAMESPACE_URI, 'resource' );

                        $textInput = $this->getNodeByAttributeNS( $xml->documentElement, 'textinput', self::NAMESPACE_URI, 'about', $resource );
                        $this->parseTextInput( $feed, $textInput );
                        break;

                    default:
                        // check if it's part of a known module/namespace
                        $this->parseModules( $feed, $channelChild, $tagName );
                        break;
                }
            }
        }

        if ( $channel->hasAttribute( 'xml:lang' ) )
        {
            $feed->language = $channel->getAttribute( 'xml:lang' );
        }

        return $feed;
    }

    /**
     * Creates a root node for the XML document being generated.
     *
     * @param string $version The RSS version for the root node
     */
    private function createRootElement( $version )
    {
        $rss = $this->xml->createElementNS( self::NAMESPACE_URI, 'rdf:RDF' );
        $this->addAttribute( $rss, 'xmlns', 'http://purl.org/rss/1.0/' );

        $this->channel = $channelTag = $this->xml->createElement( 'channel' );
        $rss->appendChild( $channelTag );
        $this->root = $this->xml->appendChild( $rss );
    }

    /**
     * Adds the required feed elements to the XML document being generated.
     */
    private function generateChannel()
    {
        $data = $this->id;

        if ( is_null( $data ) )
        {
            throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/@about" );
        }

        $aboutAttr = $this->xml->createAttribute( 'rdf:about' );
        $aboutVal = $this->xml->createTextNode( $data );
        $aboutAttr->appendChild( $aboutVal );
        $this->channel->appendChild( $aboutAttr );

        $elements = array( 'title', 'link', 'description' );
        foreach ( $elements as $element )
        {
            $data = $this->$element;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/{$element}" );
            }

            switch ( $element )
            {
                case 'link':
                    $this->generateMetaData( $this->channel, $element, $data );
                    break;

                case 'title':
                case 'description':
                    $this->generateMetaData( $this->channel, $element, $data );
                    break;
            }
        }

        if ( !is_null( $this->language ) )
        {
            $this->addAttribute( $this->channel, 'xml:lang', $this->language );
        }

        $items = $this->item;
        if ( count( $items ) === 0 )
        {
            throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/item" );
        }

        $itemsTag = $this->xml->createElement( 'items' );
        $this->channel->appendChild( $itemsTag );
        $seqTag = $this->xml->createElement( 'rdf:Seq' );
        $itemsTag->appendChild( $seqTag );

        foreach ( $items as $item )
        {
            $about = $item->id;
            $liTag = $this->xml->createElement( 'rdf:li' );
            $resourceAttr = $this->xml->createAttribute( 'resource' );
            $resourceVal = $this->xml->createTextNode( $about );
            $resourceAttr->appendChild( $resourceVal );
            $liTag->appendChild( $resourceAttr );
            $seqTag->appendChild( $liTag );
        }

        $image = $this->image;
        if ( $image !== null )
        {
            $imageTag = $this->xml->createElement( 'image' );

            $about = $image->about;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/image/@about" );
            }

            $resourceAttr = $this->xml->createAttribute( 'rdf:resource' );
            $resourceVal = $this->xml->createTextNode( $about );
            $resourceAttr->appendChild( $resourceVal );
            $imageTag->appendChild( $resourceAttr );

            $this->channel->appendChild( $imageTag );
        }

        $textInput = $this->textInput;
        if ( $textInput !== null )
        {
            $textInputTag = $this->xml->createElement( 'textinput' );

            $about = $textInput->about;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/textinput/@about" );
            }

            $resourceAttr = $this->xml->createAttribute( 'rdf:resource' );
            $resourceVal = $this->xml->createTextNode( $about );
            $resourceAttr->appendChild( $resourceVal );
            $textInputTag->appendChild( $resourceAttr );

            $this->channel->appendChild( $textInputTag );
        }
    }

    /**
     * Adds the feed items to the XML document being generated.
     */
    private function generateItems()
    {
        foreach ( $this->item as $element )
        {
            $itemTag = $this->xml->createElement( 'item' );
            $this->root->appendChild( $itemTag );

            $data = $element->id;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/item/@about" );
            }

            $aboutAttr = $this->xml->createAttribute( 'rdf:about' );
            $aboutVal = $this->xml->createTextNode( $data );
            $aboutAttr->appendChild( $aboutVal );
            $itemTag->appendChild( $aboutAttr );

            $elements = array( 'title', 'link' );
            foreach ( $elements as $attribute )
            {
                $data = $element->$attribute;

                if ( is_null( $data ) )
                {
                    throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/item/{$attribute}" );
                }

                $this->generateMetaData( $itemTag, $attribute, $data );
            }

            $elements = array( 'description', 'language' );
            foreach ( $elements as $attribute )
            {
                $data = $element->$attribute;
                if ( !is_null( $data ) )
                {
                    switch ( $attribute )
                    {
                        case 'description':
                            $this->generateMetaData( $itemTag, $attribute, $data );
                            break;

                        case 'language':
                           $this->addAttribute( $itemTag, 'xml:lang', $data );
                           break;
                    }
                }
            }

            $this->generateItemModules( $element, $itemTag );
        }
    }

    /**
     * Adds the feed image to the XML document being generated.
     */
    private function generateImage()
    {
        $image = $this->image;
        if ( $image !== null )
        {
            $imageTag = $this->xml->createElement( 'image' );
            $this->root->appendChild( $imageTag );

            $data = $image->about;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/image/@about" );
            }

            $aboutAttr = $this->xml->createAttribute( 'rdf:about' );
            $aboutVal = $this->xml->createTextNode( $data );
            $aboutAttr->appendChild( $aboutVal );
            $imageTag->appendChild( $aboutAttr );

            $elements = array( 'title', 'url', 'link' );
            foreach ( $elements as $attribute )
            {
                $data = $image->$attribute;
                if ( is_null( $data ) )
                {
                    throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/image/{$attribute}" );
                }

                $this->generateMetaData( $imageTag, $attribute, $data );
            }
        }
    }

    /**
     * Adds the feed textinput to the XML document being generated.
     */
    private function generateTextInput()
    {
        $textInput = $this->textInput;
        if ( $textInput !== null )
        {
            $textInputTag = $this->xml->createElement( 'textinput' );
            $this->root->appendChild( $textInputTag );

            $data = $textInput->about;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/textinput/@about" );
            }

            $aboutAttr = $this->xml->createAttribute( 'rdf:about' );
            $aboutVal = $this->xml->createTextNode( $data );
            $aboutAttr->appendChild( $aboutVal );
            $textInputTag->appendChild( $aboutAttr );

            $elements = array( 'title', 'description', 'name', 'link' );
            foreach ( $elements as $attribute )
            {
                $data = $textInput->$attribute;
                if ( is_null( $data ) )
                {
                    throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/textinput/{$attribute}" );
                }

                $this->generateMetaData( $textInputTag, $attribute, $data );
            }
        }
    }

    /**
     * Parses the provided XML element object and stores it as a feed item in
     * the provided ezcFeed object.
     *
     * @param ezcFeed $feed The feed object in which to store the parsed XML element as a feed item
     * @param ezcFeedElement $element The feed element object that will contain the feed item
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseItem( ezcFeed $feed, $element, DOMElement $xml )
    {
        if ( $xml->hasAttributeNS( self::NAMESPACE_URI, 'about' ) )
        {
            $element->id = $xml->getAttributeNS( self::NAMESPACE_URI, 'about' );
        }

        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType == XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;

                switch ( $tagName )
                {
                    case 'title':
                    case 'link':
                    case 'description':
                        $element->$tagName = $itemChild->textContent;
                        break;

                    default:
                        // check if it's part of a known module/namespace
                        $this->parseModules( $element, $itemChild, $tagName );
                        break;
                }
            }
        }

        if ( $xml->hasAttribute( 'xml:lang' ) )
        {
            $element->language = $xml->getAttribute( 'xml:lang' );
        }
    }

    /**
     * Parses the provided XML element object and stores it as a feed image in
     * the provided ezcFeed object.
     *
     * @param ezcFeed $feed The feed object in which to store the parsed XML element as a feed image
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseImage( ezcFeed $feed, DOMElement $xml = null )
    {
        $image = $feed->add( 'image' );
        if ( $xml !== null )
        {
            foreach ( $xml->childNodes as $itemChild )
            {
                if ( $itemChild->nodeType == XML_ELEMENT_NODE )
                {
                    $tagName = $itemChild->tagName;
                    switch ( $tagName )
                    {
                        case 'title':
                        case 'link':
                        case 'url':
                            $image->$tagName = $itemChild->textContent;
                            break;
                    }
                }
            }

            if ( $xml->hasAttributeNS( self::NAMESPACE_URI, 'about' ) )
            {
                $image->about = $xml->getAttributeNS( self::NAMESPACE_URI, 'about' );
            }
        }
    }

    /**
     * Parses the provided XML element object and stores it as a feed textinput in
     * the provided ezcFeed object.
     *
     * @param ezcFeed $feed The feed object in which to store the parsed XML element as a feed textinput
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseTextInput( ezcFeed $feed, DOMElement $xml = null )
    {
        $textInput = $feed->add( 'textInput' );
        if ( $xml !== null )
        {
            foreach ( $xml->childNodes as $itemChild )
            {
                if ( $itemChild->nodeType == XML_ELEMENT_NODE )
                {
                    $tagName = $itemChild->tagName;
                    switch ( $tagName )
                    {
                        case 'title':
                        case 'description':
                        case 'name':
                        case 'link':
                            $textInput->$tagName = $itemChild->textContent;
                            break;
                    }
                }
            }

            if ( $xml->hasAttributeNS( self::NAMESPACE_URI, 'about' ) )
            {
                $textInput->about = $xml->getAttributeNS( self::NAMESPACE_URI, 'about' );
            }
        }
    }
}
?>
