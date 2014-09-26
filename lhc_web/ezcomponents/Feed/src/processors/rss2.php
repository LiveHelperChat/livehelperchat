<?php
/**
 * File containing the ezcFeedRss2 class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class providing parsing and generating of RSS2 feeds.
 *
 * Specifications:
 * {@link http://www.rssboard.org/rss-specification RSS2 Specifications}.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedRss2 extends ezcFeedProcessor implements ezcFeedParser
{
    /**
     * Defines the feed type of this processor.
     */
    const FEED_TYPE = 'rss2';

    /**
     * Defines the feed content type of this processor.
     */
    const CONTENT_TYPE = 'application/rss+xml';

    /**
     * Creates a new RSS2 processor.
     *
     * @param ezcFeed $container The feed data container used when generating
     */
    public function __construct( ezcFeed $container )
    {
        $this->feedContainer = $container;
        $this->feedType = self::FEED_TYPE;
        $this->contentType = self::CONTENT_TYPE;

        // initialize docs and pubDate with default values
        if ( !isset( $this->docs ) )
        {
            $this->docs = 'http://www.rssboard.org/rss-specification';
        }

        if ( !isset( $this->published ) )
        {
            $this->published = time();
        }
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

        $rss = $this->xml->createElement( 'rss' );

        $rssVersionTag = $this->xml->createAttribute( 'version' );
        $rssVersionContent = $this->xml->createTextNode( '2.0' );
        $rssVersionTag->appendChild( $rssVersionContent );
        $rss->appendChild( $rssVersionTag );

        $this->channel = $this->xml->createElement( 'channel' );
        $rss->appendChild( $this->channel );
        $this->root = $this->xml->appendChild( $rss );

        $this->generateRequired();
        $this->generateOptional();
        $this->generateFeedModules( $this->channel );
        $this->generateItems();

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
        if ( $xml->documentElement->tagName !== 'rss' )
        {
            return false;
        }
        if ( !$xml->documentElement->hasAttribute( 'version' ) )
        {
            return false;
        }
        if ( !in_array( $xml->documentElement->getAttribute( 'version' ), array( '0.91', '0.92', '0.93', '0.94', '2.0' ) ) )
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

        foreach ( $channel->childNodes as $channelChild )
        {
            if ( $channelChild->nodeType == XML_ELEMENT_NODE )
            {
                $tagName = $channelChild->tagName;

                switch ( $tagName )
                {
                    case 'title':
                    case 'description':
                    case 'copyright':
                        $element = $feed->add( $tagName );
                        $element->text = $channelChild->textContent;
                        break;

                    case 'language':
                    case 'ttl':
                    case 'docs':
                    case 'rating':
                        $element = $feed->add( $tagName );
                        $element->text = $channelChild->textContent;
                        break;

                    case 'generator':
                        $element = $feed->add( $tagName );
                        $element->name = $channelChild->textContent;
                        break;

                    case 'managingEditor':
                        $element = $feed->add( 'author' );
                        $element->name = $channelChild->textContent;
                        // @todo parse $name to see if it has the structure
                        // email@address (name) to fill the ->email field from it
                        break;

                    case 'webMaster':
                        $element = $feed->add( 'webMaster' );
                        $element->name = $channelChild->textContent;
                        // @todo parse $name to see if it has the structure
                        // email@address (name) to fill the ->email field from it
                        break;

                    case 'category':
                        $element = $feed->add( $tagName );
                        $element->term = $channelChild->textContent;
                        if ( $channelChild->hasAttribute( 'domain' ) )
                        {
                            $element->scheme = $channelChild->getAttribute( 'domain' );
                        }
                        break;

                    case 'link':
                        $element = $feed->add( $tagName );
                        $element->href = $channelChild->textContent;
                        break;

                    case 'cloud':
                        $element = $feed->add( $tagName );

                        $attributes = array( 'domain' => 'domain', 'port' => 'port', 'path' => 'path',
                                             'registerProcedure' => 'registerProcedure', 'protocol' => 'protocol' );
                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $channelChild->hasAttribute( $name ) )
                            {
                                $element->$alias = $channelChild->getAttribute( $name );
                            }
                        }
                        break;

                    case 'pubDate':
                        $feed->published = $channelChild->textContent;
                        break;

                    case 'lastBuildDate':
                        $feed->updated = $channelChild->textContent;
                        break;

                    case 'item':
                        $element = $feed->add( $tagName );
                        $this->parseItem( $element, $channelChild );
                        break;

                    case 'image':
                        $image = $feed->add( 'image' );
                        $this->parseImage( $image, $channelChild );
                        break;

                    case 'skipHours':
                        $element = $feed->add( $tagName );
                        $this->parseSkipHours( $element, $channelChild );
                        break;

                    case 'skipDays':
                        $element = $feed->add( $tagName );
                        $this->parseSkipDays( $element, $channelChild );
                        break;

                    case 'textInput':
                        $element = $feed->add( $tagName );
                        $this->parseTextInput( $element, $channelChild );
                        break;

                    default:
                        // check if it's part of a known module/namespace
                        $this->parseModules( $feed, $channelChild, $tagName );
                }
            }
        }
        return $feed;
    }

    /**
     * Adds the required feed elements to the XML document being generated.
     */
    private function generateRequired()
    {
        $elements = array( 'title', 'link', 'description' );
        foreach ( $elements as $element )
        {
            $data = $this->$element;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/{$element}" );
            }

            if ( !is_array( $data ) )
            {
                $data = array( $data );
            }

            switch ( $element )
            {
                case 'link':
                    foreach ( $data as $dataNode )
                    {
                        $this->generateMetaData( $this->channel, $element, $dataNode->href );
                    }
                    break;

                default:
                    foreach ( $data as $dataNode )
                    {
                        $this->generateMetaData( $this->channel, $element, $dataNode );
                    }
                    break;
            }
        }
    }

    /**
     * Adds the optional feed elements to the XML document being generated.
     */
    private function generateOptional()
    {
        $elements = array( 'language', 'copyright', 'author',
                           'webMaster', 'published', 'updated',
                           'category', 'generator', 'docs',
                           'ttl', 'image', 'rating',
                           'textInput', 'skipHours', 'skipDays',
                           'cloud', 'id' );

        foreach ( $elements as $element )
        {
            $data = $this->$element;

            if ( !is_null( $data ) )
            {
                switch ( $element )
                {
                    case 'updated':
                        $this->generateMetaData( $this->channel, 'lastBuildDate', $data->date->format( 'D, d M Y H:i:s O' ) );
                        break;

                    case 'published':
                        $this->generateMetaData( $this->channel, 'pubDate', $data->date->format( 'D, d M Y H:i:s O' ) );
                        break;

                    case 'image':
                        $this->generateImage( $this->image );
                        break;

                    case 'skipHours':
                        $this->generateSkipHours( $this->skipHours );
                        break;

                    case 'skipDays':
                        $this->generateSkipDays( $this->skipDays );
                        break;

                    case 'textInput':
                        $this->generateTextInput( $this->textInput );
                        break;

                    case 'cloud':
                        $this->generateCloud( $this->cloud );
                        break;

                    case 'category':
                        foreach ( $this->category as $category )
                        {
                            $this->generateCategory( $category, $this->channel );
                        }
                        break;

                    case 'generator':
                        $this->generateGenerator( $data, $this->channel );
                        break;

                    case 'author':
                        foreach ( $this->author as $person )
                        {
                            $this->generatePerson( $person, $this->channel, 'managingEditor' );
                        }
                        break;

                    case 'webMaster':
                        foreach ( $this->webMaster as $person )
                        {
                            $this->generatePerson( $person, $this->channel, 'webMaster' );
                        }
                        break;

                    case 'id':
                        $this->generateAtomSelfLink( $this->id );
                        break;

                    default:
                        if ( !is_array( $data ) )
                        {
                            $data = array( $data );
                        }

                        foreach ( $data as $dataNode )
                        {
                            $this->generateMetaData( $this->channel, $element, $dataNode );
                        }
                        break;
                }
            }
        }
    }

    /**
     * Adds a category node to the XML document being generated.
     *
     * @param ezcFeedCategoryElement $category The category feed element
     * @param DOMElement $root The XML element where to store the category element
     */
    private function generateCategory( ezcFeedCategoryElement $category, DOMElement $root )
    {
        $data = $category->term;

        $element = $this->xml->createElement( 'category', $data );
        $root->appendChild( $element );

        $data = $category->scheme;
        if ( !is_null( $data ) )
        {
            $this->addAttribute( $element, 'domain', $data );
        }
    }

    /**
     * Adds a person node to the XML document being generated.
     *
     * @param ezcFeedPersonElement $person The person feed element
     * @param DOMElement $root The XML element where to store the person element
     * @param string $elementName The feed element name (eg 'author')
     */
    private function generatePerson( ezcFeedPersonElement $person, DOMElement $root, $elementName )
    {
        $name = $person->name;
        $email = $person->email;

        $data = $name;
        if ( $email !== null )
        {
            $data = "{$email} ({$name})";
        }

        $element = $this->xml->createElement( $elementName, $data );
        $root->appendChild( $element );
    }

    /**
     * Adds a generator node to the XML document being generated.
     *
     * @param ezcFeedGeneratorElement $generator The generator feed element
     * @param DOMElement $root The XML element where to store the generator element
     */
    private function generateGenerator( ezcFeedGeneratorElement $generator, DOMElement $root )
    {
        $name = $generator->name;
        $version = $generator->version;
        $url = $generator->url;

        $data = $name;
        if ( $version !== null )
        {
            $data = $data . " {$version}";
        }

        if ( $url !== null )
        {
            $data = $data . " ({$url})";
        }

        $element = $this->xml->createElement( 'generator', $data );
        $root->appendChild( $element );
    }

    /**
     * Adds an image node to the XML document being generated.
     *
     * @param ezcFeedImageElement $feedElement The image feed element
     */
    private function generateImage( ezcFeedImageElement $feedElement )
    {
        $image = $this->xml->createElement( 'image' );
        $this->channel->appendChild( $image );

        $elements = array( 'url', 'title', 'link' );
        foreach ( $elements as $element )
        {
            $data = $feedElement->$element;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/image/{$element}" );
            }

            $this->generateMetaData( $image, $element, $data );
        }

        $elements = array( 'description', 'width', 'height' );
        foreach ( $elements as $element )
        {
            $data = $feedElement->$element;
            if ( !is_null( $data ) )
            {
                $this->generateMetaData( $image, $element, $data );
            }
        }
    }

    /**
     * Adds a skipHours node to the XML document being generated.
     *
     * @param ezcFeedSkipHoursElement $feedElement The skipHours feed element
     */
    private function generateSkipHours( ezcFeedSkipHoursElement $feedElement )
    {
        $tag = $this->xml->createElement( 'skipHours' );
        $this->channel->appendChild( $tag );

        $data = $feedElement->hours;
        if ( !is_null( $data ) )
        {
            foreach ( $data as $dataNode )
            {
                $this->generateMetaData( $tag, 'hour', $dataNode );
            }
        }
    }

    /**
     * Adds a skipDays node to the XML document being generated.
     *
     * @param ezcFeedSkipDaysElement $feedElement The skipDays feed element
     */
    private function generateSkipDays( ezcFeedSkipDaysElement $feedElement )
    {
        $tag = $this->xml->createElement( 'skipDays' );
        $this->channel->appendChild( $tag );

        $data = $feedElement->days;
        if ( !is_null( $data ) )
        {
            foreach ( $data as $dataNode )
            {
                $this->generateMetaData( $tag, 'day', $dataNode );
            }
        }
    }

    /**
     * Adds an textInput node to the XML document being generated.
     *
     * @param ezcFeedTextInputElement $feedElement The textInput feed element
     */
    private function generateTextInput( ezcFeedTextInputElement $feedElement )
    {
        $image = $this->xml->createElement( 'textInput' );
        $this->channel->appendChild( $image );

        $elements = array( 'title', 'description', 'name', 'link' );
        foreach ( $elements as $element )
        {
            $data = $feedElement->$element;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/textInput/{$element}" );
            }

            $this->generateMetaData( $image, $element, $data );
        }
    }

    /**
     * Adds a cloud node to the XML document being generated.
     *
     * @param ezcFeedCloudElement $feedElement The cloud feed element
     */
    private function generateCloud( ezcFeedCloudElement $feedElement )
    {
        $attributes = array();
        $elements = array( 'domain', 'port', 'path', 'registerProcedure', 'protocol' );
        foreach ( $elements as $element )
        {
            $data = $feedElement->$element;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/{$this->root->nodeName}/cloud/{$element}" );
            }
            $attributes[$element] = $data;
        }

        $this->generateMetaDataWithAttributes( $this->channel, 'cloud', false, $attributes );
    }

    /**
     * Adds an atom:link node to the XML document being generated, plus the namespace.
     *
     * @param ezcFeedIdElement $feedElement The link feed element
     */
    private function generateAtomSelfLink( ezcFeedIdElement $feedElement )
    {
        $this->addAttribute( $this->root, 'xmlns:atom', ezcFeedAtom::NAMESPACE_URI );

        $link = $this->xml->createElement( 'atom:link' );
        $this->channel->appendChild( $link );

        $this->addAttribute( $link, 'href', $feedElement->id );
        $this->addAttribute( $link, 'rel', 'self' );
        $this->addAttribute( $link, 'type', self::CONTENT_TYPE );
    }

    /**
     * Adds the feed items to the XML document being generated.
     */
    private function generateItems()
    {
        $items = $this->item;
        if ( $items === null )
        {
            return;
        }

        foreach ( $items as $item )
        {
            $itemTag = $this->xml->createElement( 'item' );
            $this->channel->appendChild( $itemTag );

            $atLeastOneRequiredFeedItemPresent = false;
            $elements = array( 'title', 'description' );
            foreach ( $elements as $attribute )
            {
                $data = $item->$attribute;
                if ( !is_null( $data ) )
                {
                    $atLeastOneRequiredFeedItemPresent = true;
                    break;
                }
            }

            if ( $atLeastOneRequiredFeedItemPresent === false )
            {
                $requiredElements = $elements;
                for ( $i = 0; $i < count( $requiredElements ); $i++ )
                {
                    $requiredElements[$i] = "/{$this->root->nodeName}/item/{$requiredElements[$i]}";
                }
                throw new ezcFeedAtLeastOneItemDataRequiredException( $requiredElements );
            }

            $optional = array( 'title', 'link', 'description',
                               'author', 'category', 'comments',
                               'enclosure', 'id', 'published',
                               'source', 'language' );

            foreach ( $optional as $attribute )
            {
                $metaData = $item->$attribute;

                if ( !is_null( $metaData ) )
                {
                    switch ( $attribute )
                    {
                        case 'comments':
                            $this->generateMetaData( $itemTag, 'comments', $metaData );
                            break;

                        case 'id':
                            $attributes = array();
                            if ( isset( $metaData->isPermaLink ) )
                            {
                                $permaLink = ( $metaData->isPermaLink === true ) ? 'true' : 'false';
                                $attributes = array( 'isPermaLink' => $permaLink );
                            }

                            $this->generateMetaDataWithAttributes( $itemTag, 'guid', $metaData->id, $attributes );
                            break;

                        case 'category':
                            foreach ( $metaData as $dataNode )
                            {
                                $this->generateCategory( $dataNode, $itemTag );
                            }
                            break;

                        case 'author':
                            foreach ( $metaData as $person )
                            {
                                $this->generatePerson( $person, $itemTag, 'author' );
                            }
                            break;

                        case 'published':
                            $this->generateMetaData( $itemTag, 'pubDate', $metaData->date->format( 'D, d M Y H:i:s O' ) );
                            break;

                        case 'enclosure':
                            foreach ( $metaData as $dataNode )
                            {
                                $attributes = array();
                                $elements = array( 'url', 'length', 'type' );
                                foreach ( $elements as $key )
                                {
                                    if ( isset( $dataNode->$key ) )
                                    {
                                        $attributes[$key] = $dataNode->$key;
                                    }
                                }

                                $this->generateMetaDataWithAttributes( $itemTag, 'enclosure', false, $attributes );
                            }

                            break;

                        case 'source':
                            if ( !isset( $metaData->url ) )
                            {
                                throw new ezcFeedRequiredMetaDataMissingException( '/rss/item/source/@url' );
                            }

                            $attributes = array( 'url' => $metaData->url );
                            $this->generateMetaDataWithAttributes( $itemTag, 'source', $metaData, $attributes );
                            break;

                        case 'language':
                           $this->addAttribute( $itemTag, 'xml:lang', $metaData );
                           break;

                        default:
                            $this->generateMetaData( $itemTag, $attribute, $metaData );
                    }
                }
            }

            $this->generateItemModules( $item, $itemTag );
        }
    }

    /**
     * Parses the provided XML element object and stores it as a feed item in
     * the provided ezcFeed object.
     *
     * @param ezcFeedElement $element The feed element object that will contain the feed item
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseItem( ezcFeedElement $element, DOMElement $xml )
    {
        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;

                switch ( $tagName )
                {
                    case 'title':
                    case 'description':
                    case 'comments':
                        $subElement = $element->add( $tagName );
                        $subElement->text = $itemChild->textContent;
                        break;

                    case 'author':
                        $subElement = $element->add( $tagName );
                        $subElement->name = $itemChild->textContent;
                        // @todo parse textContent if it has the format: email@host (name)
                        break;

                    case 'pubDate':
                        $element->published = $itemChild->textContent;
                        break;

                    case 'enclosure':
                        $subElement = $element->add( $tagName );

                        $attributes = array( 'url' => 'url', 'type' => 'type', 'length' => 'length' );
                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $itemChild->hasAttribute( $name ) )
                            {
                                $subElement->$alias = $itemChild->getAttribute( $name );
                            }
                        }
                        break;

                    case 'link':
                        $subElement = $element->add( $tagName );
                        $subElement->href = $itemChild->textContent;
                        break;

                    case 'source':
                        $subElement = $element->add( $tagName );
                        $subElement->source = $itemChild->textContent;
                        if ( $itemChild->hasAttribute( 'url' ) )
                        {
                            $subElement->url = $itemChild->getAttribute( 'url' );
                        }
                        break;

                    case 'category':
                        $subElement = $element->add( $tagName );
                        $subElement->term = $itemChild->textContent;
                        if ( $itemChild->hasAttribute( 'domain' ) )
                        {
                            $subElement->scheme = $itemChild->getAttribute( 'domain' );
                        }
                        break;

                    case 'guid':
                        $subElement = $element->add( 'id' );
                        $subElement->id = $itemChild->textContent;
                        if ( $itemChild->hasAttribute( 'isPermaLink' ) )
                        {
                            $value = $itemChild->getAttribute( 'isPermaLink' );
                            $subElement->isPermaLink = ( strtolower( $value ) === 'true' ) ? true : false;
                        }
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
     * the provided ezcFeedImageElement object.
     *
     * @param ezcFeedImageElement $element The feed element object that will contain the feed image
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseImage( ezcFeedImageElement $element, DOMElement $xml )
    {
        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;

                switch ( $tagName )
                {
                    case 'title':
                    case 'link':
                    case 'url':
                    case 'description':
                    case 'width':
                    case 'height':
                        $element->$tagName = $itemChild->textContent;
                        break;
                }
            }
        }
    }

    /**
     * Parses the provided XML element object and stores it as a feed element
     * of type skipHours in the provided ezcFeedSkipHoursElement object.
     *
     * @param ezcFeedSkipHoursElement $element The feed element object that will contain skipHours
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseSkipHours( ezcFeedSkipHoursElement $element, DOMElement $xml )
    {
        $values = array();
        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;

                if ( $tagName === 'hour' )
                {
                    $values[] = $itemChild->textContent;
                }
            }
        }

        $element->hours = $values;
    }

    /**
     * Parses the provided XML element object and stores it as a feed element
     * of type skipDays in the provided ezcFeedSkipDaysElement object.
     *
     * @param ezcFeedSkipDaysElement $element The feed element object that will contain skipDays
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseSkipDays( ezcFeedSkipDaysElement $element, DOMElement $xml )
    {
        $values = array();
        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;

                if ( $tagName === 'day' )
                {
                    $values[] = $itemChild->textContent;
                }
            }
        }

        $element->days = $values;
    }

    /**
     * Parses the provided XML element object and stores it as a textInput in
     * the provided ezcFeedTextInputElement object.
     *
     * @param ezcFeedTextInputElement $element The feed element object that will contain the textInput
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseTextInput( ezcFeedTextInputElement $element, DOMElement $xml )
    {
        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;

                switch ( $tagName )
                {
                    case 'title':
                    case 'description':
                    case 'name':
                    case 'link':
                        $element->$tagName = $itemChild->textContent;
                        break;
                }
            }
        }
    }
}
?>
