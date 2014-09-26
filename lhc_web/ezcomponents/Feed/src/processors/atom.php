<?php
/**
 * File containing the ezcFeedAtom class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class providing parsing and generating of ATOM feeds.
 *
 * Specifications:
 * {@link http://atompub.org/rfc4287.html ATOM RFC4287}.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedAtom extends ezcFeedProcessor implements ezcFeedParser
{
    /**
     * Defines the feed type of this processor.
     */
    const FEED_TYPE = 'atom';

    /**
     * Defines the feed content type of this processor.
     */
    const CONTENT_TYPE = 'application/atom+xml';

    /**
     * Defines the namespace for ATOM feeds.
     */
    const NAMESPACE_URI = 'http://www.w3.org/2005/Atom';

    /**
     * Creates a new ATOM processor.
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

        $rss = $this->xml->createElementNS( self::NAMESPACE_URI, 'feed' );
        $this->channel = $rss;
        $this->root = $this->xml->appendChild( $rss );

        $this->generateRequired();
        $this->generateAtLeastOne();
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
        if ( $xml->documentElement->tagName !== 'feed' )
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
        $channel = $xml->documentElement;

        $this->usedPrefixes = $this->fetchUsedPrefixes( $xml );

        foreach ( $channel->childNodes as $channelChild )
        {
            if ( $channelChild->nodeType == XML_ELEMENT_NODE )
            {
                $tagName = $channelChild->tagName;

                switch ( $tagName )
                {
                    case 'title':
                        $this->parseTextNode( $feed, $channelChild, 'title' );
                        break;

                    case 'rights':
                        $this->parseTextNode( $feed, $channelChild, 'copyright' );
                        break;

                    case 'subtitle':
                        $this->parseTextNode( $feed, $channelChild, 'description' );
                        break;

                    case 'id':
                        $feed->$tagName = $channelChild->textContent;
                        break;

                    case 'icon':
                        $feed->$tagName = $channelChild->textContent;
                        break;

                    case 'logo':
                        $feed->image = $channelChild->textContent;
                        break;

                    case 'generator':
                        $element = $feed->add( $tagName );

                        $attributes = array( 'uri' => 'url', 'version' => 'version' );
                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $channelChild->hasAttribute( $name ) )
                            {
                                $element->$alias = $channelChild->getAttribute( $name );
                            }
                        }
                        $element->name = $channelChild->textContent;
                        break;

                    case 'updated':
                        $feed->$tagName = $channelChild->textContent;
                        break;

                    case 'category':
                        $element = $feed->add( $tagName );

                        $attributes = array( 'term' => 'term', 'scheme' => 'scheme', 'label' => 'label' );
                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $channelChild->hasAttribute( $name ) )
                            {
                                $element->$alias = $channelChild->getAttribute( $name );
                            }
                        }
                        break;

                    case 'link':
                        $element = $feed->add( $tagName );

                        $attributes = array( 'href' => 'href', 'rel' => 'rel', 'hreflang' => 'hreflang',
                                             'type' => 'type', 'title' => 'title', 'length' => 'length' );

                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $channelChild->hasAttribute( $name ) )
                            {
                                $element->$alias = $channelChild->getAttribute( $name );
                            }
                        }
                        break;

                    case 'contributor':
                    case 'author':
                        $element = $feed->add( $tagName );
                        $this->parsePerson( $element, $channelChild, $tagName );
                        break;

                    case 'entry':
                        $element = $feed->add( 'item' );
                        $this->parseItem( $element, $channelChild );
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
     * Adds the required feed elements to the XML document being generated.
     */
    private function generateRequired()
    {
        $elements = array( 'id', 'title', 'updated' );
        foreach ( $elements as $element )
        {
            $data = $this->$element;
            if ( is_null( $data ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "/feed/{$element}" );
            }

            switch ( $element )
            {
                case 'id':
                    $this->generateMetaData( $this->channel, $element, $data );
                    break;

                case 'title':
                    $this->generateTextNode( $this->channel, $element, $data );
                    break;

                case 'updated':
                    // Sample date: 2003-12-13T18:30:02-05:00
                    $this->generateMetaData( $this->channel, $element, $data->date->format( 'c' ) );
                    break;

            }
        }
    }

    /**
     * Adds the at-least-one feed elements to the XML document being generated.
     */
    private function generateAtLeastOne()
    {
        $needToThrowException = false;
        $element = 'author';

        $data = $this->$element;

        if ( is_null( $data ) )
        {
            $entries = $this->item;
            if ( $entries === null )
            {
                throw new ezcFeedAtLeastOneItemDataRequiredException( array( '/feed/author' ) );
            }

            foreach ( $entries as $entry )
            {
                $authors = $entry->author;
                if ( $authors === null )
                {
                    throw new ezcFeedAtLeastOneItemDataRequiredException( array( '/feed/entry/author' ) );
                }
            }

            throw new ezcFeedAtLeastOneItemDataRequiredException( array( '/feed/author' ) );
        }
    }

    /**
     * Adds the optional feed elements to the XML document being generated.
     */
    private function generateOptional()
    {
        $elements = array( 'author', 'link', 'category',
                           'contributor', 'generator', 'icon',
                           'image', 'copyright', 'description', 'language' );

        if ( $this->link !== null )
        {
            $this->checkLinks( $this->channel, $this->link );
        }

        foreach ( $elements as $element )
        {
            $data = $this->$element;

            if ( !is_null( $data ) )
            {
                switch ( $element )
                {
                    case 'contributor':
                        foreach ( $this->contributor as $person )
                        {
                            $this->generatePerson( $this->channel, 'contributor', $person );
                        }
                        break;

                    case 'author':
                        foreach ( $this->author as $person )
                        {
                            $this->generatePerson( $this->channel, 'author', $person );
                        }
                        break;

                    case 'generator':
                        $this->generateGenerator( $this->channel, $this->generator );
                        break;

                    case 'link':
                        foreach ( $data as $dataNode )
                        {
                            $this->generateLink( $this->channel, $dataNode );
                        }
                        break;

                    case 'category':
                        foreach ( $data as $dataNode )
                        {
                            $this->generateCategory( $this->channel, $dataNode );
                        }
                        break;

                    case 'description':
                        $this->generateTextNode( $this->channel, 'subtitle', $data );
                        break;

                    case 'copyright':
                        $this->generateTextNode( $this->channel, 'rights', $data );
                        break;

                    case 'image':
                        $this->generateMetaData( $this->channel, 'logo', $data );
                        break;

                    case 'icon':
                        $this->generateMetaData( $this->channel, 'icon', $data );
                        break;
                        
                    case 'language':
                        $this->generateLanguage( $this->channel, $data );
                        break;
                }
            }
        }
    }

    /**
     * Checks if the links are defined correctly.
     *
     * @throws ezcFeedOnlyOneValueAllowedException
     *         if there was more than one @rel="alternate" element in the $links array
     *
     * @param DOMNode $root The root in which to check the link elements
     * @param array(ezcFeedLinkElement) $links The link elements to check
     * @return bool
     */
    private function checkLinks( DOMNode $root, array $links )
    {
        $unique = array();
        foreach ( $links as $dataNode )
        {
            if ( ( isset( $dataNode->rel ) && $dataNode->rel === 'alternate' )
                 && isset( $dataNode->type )
                 && isset( $dataNode->hreflang ) )
            {
                foreach ( $unique as $obj )
                {
                    if ( $obj['type'] === $dataNode->type
                         && $obj['hreflang'] === $dataNode->hreflang )
                    {
                        $parentNode = ( $root->nodeName === 'entry' ) ? '/feed' : '';
                        $parentNode = ( $root->nodeName === 'source' ) ? '/feed/entry' : $parentNode;

                        throw new ezcFeedOnlyOneValueAllowedException( "{$parentNode}/{$root->nodeName}/link@rel=\"alternate\"" );
                    }
                }

                $unique[] = array( 'type' => $dataNode->type,
                                   'hreflang' => $dataNode->hreflang );

            }
        }

        return true;
    }

    /**
     * Creates a link node in the XML document being generated.
     *
     * @param DOMNode $root The root in which to create the link
     * @param ezcFeedLinkElement $dataNode The data for the link node to create
     */
    private function generateLink( DOMNode $root, ezcFeedLinkElement $dataNode )
    {
        $elementTag = $this->xml->createElement( 'link' );
        $root->appendChild( $elementTag );

        $elements = array( 'href', 'rel', 'type', 'hreflang', 'title', 'length' );
        if ( !isset( $dataNode->href ) )
        {
            $parentNode = ( $root->nodeName === 'entry' ) ? '/feed' : '';
            $parentNode = ( $root->nodeName === 'source' ) ? '/feed/entry' : $parentNode;

            throw new ezcFeedRequiredMetaDataMissingException( "{$parentNode}/{$root->nodeName}/link/@href" );
        }

        foreach ( $elements as $attribute )
        {
            if ( isset( $dataNode->$attribute ) )
            {
                $this->addAttribute( $elementTag, $attribute, $dataNode->$attribute );
            }
        }
    }

    /**
     * Creates a generator node in the XML document being generated.
     *
     * @param DOMNode $root The root in which to create the generator node
     * @param ezcFeedGeneratorElement $generator The data for the generator node to create
     */
    private function generateGenerator( DOMNode $root, ezcFeedGeneratorElement $generator )
    {
        $name = $generator->name;
        $version = $generator->version;
        $url = $generator->url;

        $elementTag = $this->xml->createElement( 'generator', $name );
        $root->appendChild( $elementTag );

        if ( $version !== null )
        {
            $this->addAttribute( $elementTag, 'version', $version );
        }

        if ( $url !== null )
        {
            $this->addAttribute( $elementTag, 'uri', $url );
        }
    }

    /**
     * Creates a category node in the XML document being generated.
     *
     * @throws ezcFeedRequiredMetaDataMissingException
     *         if the required attributes are missing
     *
     * @param DOMNode $root The root in which to create the category node
     * @param ezcFeedCategoryElement $dataNode The data for the category node to create
     */
    private function generateCategory( DOMNode $root, ezcFeedCategoryElement $dataNode )
    {
        $elementTag = $this->xml->createElement( 'category' );
        $root->appendChild( $elementTag );

        $parentNode = ( $root->nodeName === 'entry' ) ? '/feed' : '';
        $parentNode = ( $root->nodeName === 'source' ) ? '/feed/entry' : $parentNode;

        $attributes = array( 'term' => 'term', 'scheme' => 'scheme', 'label' => 'label' );
        $required = array( 'term' );
        $optional = array( 'scheme', 'label' );

        foreach ( $attributes as $attribute => $alias )
        {
            if ( isset( $dataNode->$alias ) )
            {
                $val = $dataNode->$alias;
                $this->addAttribute( $elementTag, $attribute, $val );
            }
            else if ( in_array( $attribute, $required ) )
            {
                throw new ezcFeedRequiredMetaDataMissingException( "{$parentNode}/{$root->nodeName}/category/@{$attribute}" );
            }
        }
    }

    /**
     * Creates an XML node in the XML document being generated.
     *
     * @param DOMNode $root The root in which to create the node $element
     * @param string $element The name of the node to create
     * @param ezcFeedTextElement $dataNode The data for the node to create
     */
    private function generateTextNode( DOMNode $root, $element, ezcFeedTextElement $dataNode )
    {
        $elementTag = $this->xml->createElement( $element );
        $root->appendChild( $elementTag );

        $attributes = array();

        if ( isset( $dataNode->type ) )
        {
            $val = $dataNode->type;
            switch ( $val )
            {
                case 'html':
                    $dataNode->text = htmlspecialchars( $dataNode->__toString() );
                    $this->addAttribute( $elementTag, 'type', $val );
                    break;

                case 'xhtml':
                    $this->addAttribute( $elementTag, 'type', $val );
                    $this->addAttribute( $elementTag, 'xmlns:xhtml', 'http://www.w3.org/1999/xhtml' );
                    $xhtmlTag = $this->xml->createElement( 'xhtml:div', $dataNode->__toString() );
                    $elementTag->appendChild( $xhtmlTag );
                    $elementTag = $xhtmlTag;
                    break;

                case 'text':
                    // same as the default case

                default:
                    $val = 'text';
                    $this->addAttribute( $elementTag, 'type', $val );
                    break;

            }
        }

        if ( isset( $dataNode->language ) )
        {
            if ( $dataNode->type === 'xhtml' )
            {
                $this->addAttribute( $elementTag->parentNode, 'xml:lang', $dataNode->language );
            }
            else
            {
                $this->addAttribute( $elementTag, 'xml:lang', $dataNode->language );
            }
        }

        $elementTag->nodeValue = $dataNode;
    }
    
    /**
     * Creates an attribute on an XML node containing the language in it.
     *
     * @param DOMNode $root The root in which to create the attribute
     * @param ezcFeedTextElement $dataNode The data for the node to create
     */
    private function generateLanguage( DOMNode $root, ezcFeedTextElement $dataNode  )
    {
        $this->addAttribute( $root, 'xml:lang', $dataNode->text );
    }

    /**
     * Creates an XML node in the XML document being generated.
     *
     * @param DOMNode $root The root in which to create the node $element
     * @param string $element The name of the node to create
     * @param array(string=>mixed) $dataNode The data for the node to create
     */
    private function generateContentNode( DOMNode $root, $element, $dataNode )
    {
        $elementTag = $this->xml->createElement( $element );
        $root->appendChild( $elementTag );

        $attributes = array();

        if ( isset( $dataNode->type ) )
        {
            $val = $dataNode->type;
            switch ( $val )
            {
                case 'html':
                    $dataNode->text = htmlspecialchars( $dataNode->__toString() );
                    $this->addAttribute( $elementTag, 'type', $val );
                    break;

                case 'xhtml':
                    $this->addAttribute( $elementTag, 'type', $val );
                    $this->addAttribute( $elementTag, 'xmlns:xhtml', 'http://www.w3.org/1999/xhtml' );
                    $xhtmlTag = $this->xml->createElement( 'xhtml:div', $dataNode->__toString() );
                    $elementTag->appendChild( $xhtmlTag );
                    $elementTag = $xhtmlTag;
                    break;

                case 'text':
                    $this->addAttribute( $elementTag, 'type', $val );
                    break;

                default:
                    if ( preg_match( '@[+/]xml$@', $val ) !== 0 )
                    {
                        // @todo: implement to assign the text in $dataNode as an XML node into $elementTag
                        $this->addAttribute( $elementTag, 'type', $val );
                    }
                    else if ( substr_compare( $val, 'text/', 0, 5, true ) === 0 )
                    {
                        $dataNode->text = htmlspecialchars( $dataNode->__toString() );
                        $this->addAttribute( $elementTag, 'type', $val );
                        break;
                    }
                    else if ( $val !== null )
                    {
                        // @todo: make 76 and "\n" options?
                        $dataNode->text = chunk_split( base64_encode( $dataNode->__toString() ), 76, "\n" );
                        $this->addAttribute( $elementTag, 'type', $val );
                    }
                    else
                    {
                        $val = 'text';
                        $this->addAttribute( $elementTag, 'type', $val );
                    }
                    break;

            }
        }

        if ( $dataNode->src !== null )
        {
            $this->addAttribute( $elementTag, 'src', $dataNode->src );
        }

        if ( $dataNode->language !== null )
        {
            if ( $dataNode->type === 'xhtml' )
            {
                $this->addAttribute( $elementTag->parentNode, 'xml:lang', $dataNode->language );
            }
            else
            {
                $this->addAttribute( $elementTag, 'xml:lang', $dataNode->language );
            }
        }

        $elementTag->nodeValue = $dataNode;
    }

    /**
     * Creates an XML person node in the XML document being generated.
     *
     * @param DOMNode $root The root in which to create the node $element
     * @param string $element The name of the node to create
     * @param ezcFeedPersonElement $feedElement The person feed element (author, contributor)
     */
    private function generatePerson( DOMNode $root, $element, ezcFeedPersonElement $feedElement )
    {
        $elementTag = $this->xml->createElement( $element );
        $root->appendChild( $elementTag );

        $parentNode = ( $root->nodeName === 'entry' ) ? '/feed' : '';
        $parentNode = ( $root->nodeName === 'source' ) ? '/feed/entry' : $parentNode;

        $name = $feedElement->name;
        $email = $feedElement->email;
        $uri = $feedElement->uri;

        if ( !is_null( $name ) )
        {
            $this->generateMetaData( $elementTag, 'name', $name );
        }
        else
        {
            throw new ezcFeedRequiredMetaDataMissingException( "{$parentNode}/{$root->nodeName}/{$element}/name" );
        }

        if ( !is_null( $email ) )
        {
            $this->generateMetaData( $elementTag, 'email', $email );
        }

        if ( !is_null( $uri ) )
        {
            $this->generateMetaData( $elementTag, 'uri', $uri );
        }
    }

    /**
     * Creates an XML source node in the XML document being generated.
     *
     * @param DOMNode $root The root in which to create the source node
     * @param ezcFeedSourceElement $feedElement The feed source
     */
    private function generateSource( DOMNode $root, ezcFeedSourceElement $feedElement )
    {
        $elementTag = $this->xml->createElement( 'source' );
        $root->appendChild( $elementTag );

        $elements = array( 'id', 'title', 'updated',
                           'author', 'link', 'category',
                           'contributor', 'generator', 'icon',
                           'image', 'copyright', 'description' );

        foreach ( $elements as $child )
        {
            $data = $feedElement->$child;

            if ( !is_null( $data ) )
            {
                switch ( $child )
                {
                    case 'title':
                        $this->generateTextNode( $elementTag, 'title', $data );
                        break;

                    case 'description':
                        $this->generateTextNode( $elementTag, 'subtitle', $data );
                        break;

                    case 'copyright':
                        $this->generateTextNode( $elementTag, 'rights', $data );
                        break;

                    case 'contributor':
                        foreach ( $data as $dataNode )
                        {
                            $this->generatePerson( $elementTag, 'contributor', $dataNode );
                        }
                        break;

                    case 'author':
                        foreach ( $data as $dataNode )
                        {
                            $this->generatePerson( $elementTag, 'author', $dataNode );
                        }
                        break;

                    case 'generator':
                        $this->generateGenerator( $elementTag, $data );
                        break;

                    case 'link':
                        foreach ( $data as $dataNode )
                        {
                            $this->generateLink( $elementTag, $dataNode );
                        }
                        break;

                    case 'category':
                        foreach ( $data as $dataNode )
                        {
                            $this->generateCategory( $elementTag, $dataNode );
                        }
                        break;

                    case 'image':
                        $this->generateMetaData( $elementTag, 'logo', $data );
                        break;

                    case 'icon':
                        $this->generateMetaData( $elementTag, 'icon', $data );
                        break;

                    case 'updated':
                        // Sample date: 2003-12-13T18:30:02-05:00
                        $this->generateMetaData( $elementTag, 'updated', $data->date->format( 'c' ) );
                        break;

                    case 'id':
                        $this->generateMetaData( $elementTag, 'id', $data );
                        break;
                }
            }
        }
    }

    /**
     * Adds the feed entry elements to the XML document being generated.
     *
     * @throws ezcFeedRequiredMetaDataException
     *         if the required elements or attributes are not present
     */
    private function generateItems()
    {
        $entries = $this->item;
        if ( $entries === null )
        {
            return;
        }

        foreach ( $entries as $entry )
        {
            $entryTag = $this->xml->createElement( 'entry' );
            $this->channel->appendChild( $entryTag );
            
            if ( !is_null( $entry->language ) )
            {
                $this->addAttribute( $entryTag, 'xml:lang', $entry->language );
            }

            $elements = array( 'id', 'title', 'updated' );

            foreach ( $elements as $element )
            {
                $data = $entry->$element;

                if ( is_null( $data ) )
                {
                    throw new ezcFeedRequiredMetaDataMissingException( "/feed/entry/{$element}" );
                }

                switch ( $element )
                {
                    case 'id':
                        $this->generateMetaData( $entryTag, $element, $data );
                        break;

                    case 'title':
                        $this->generateTextNode( $entryTag, $element, $data );
                        break;

                    case 'updated':
                        // Sample date: 2003-12-13T18:30:02-05:00
                        $this->generateMetaData( $entryTag, $element, $data->date->format( 'c' ) );
                        break;
                }
            }

            // ensure the ATOM rules are applied
            $content = $entry->content;
            $summary = $entry->description;
            $links = $entry->link;
            $contentPresent = !is_null( $content );
            $contentSrcPresent = $contentPresent && is_object( $content ) && !is_null( $content->src );
            $contentBase64 = true;
            if ( $contentPresent && is_object( $content )
                 && ( in_array( $content->type, array( 'text', 'html', 'xhtml', null ) )
                      || preg_match( '@[+/]xml$@i', $content->type ) !== 0
                      || substr_compare( $content->type, 'text/', 0, 5, true ) === 0 ) )
            {
                $contentBase64 = false;
            }

            $summaryPresent = !is_null( $summary );
            $linkAlternatePresent = false;
            if ( $links !== null )
            {
                foreach ( $links as $link )
                {
                    // if the rel attribute is not set or if it is "alternate"
                    // then there is at least one rel="alternate" link in the feed entry
                    if ( !isset( $link->rel )
                         || $link->rel === 'alternate' )
                    {
                        $linkAlternatePresent = true;
                        break;
                    }
                }
            }

            if ( !$contentPresent )
            {
                if ( !$linkAlternatePresent && !$summaryPresent )
                {
                    throw new ezcFeedRequiredMetaDataMissingException( '/feed/entry/content' );
                }

                if ( !$linkAlternatePresent )
                {
                    throw new ezcFeedRequiredMetaDataMissingException( '/feed/entry/link@rel="alternate"' );
                }

                if ( !$summaryPresent )
                {
                    throw new ezcFeedRequiredMetaDataMissingException( '/feed/entry/summary' );
                }
            }

            if ( $contentPresent )
            {
                if ( ( $contentSrcPresent || $contentBase64 ) && !$summaryPresent )
                {
                    throw new ezcFeedRequiredMetaDataMissingException( '/feed/entry/summary' );
                }
            }

            $elements = array( 'author', 'content', 'link', 'description',
                               'category', 'contributor', 'published', 'copyright',
                               'source', 'enclosure' );

            if ( $entry->link !== null )
            {
                $this->checkLinks( $entryTag, $entry->link );
            }

            foreach ( $elements as $element )
            {
                $data = $entry->$element;

                if ( is_null( $data ) )
                {
                    continue;
                }

                switch ( $element )
                {
                    case 'description':
                        $this->generateTextNode( $entryTag, 'summary', $data );
                        break;

                    case 'copyright':
                        $this->generateTextNode( $entryTag, 'rights', $data );
                        break;

                    case 'content':
                        $this->generateContentNode( $entryTag, $element, $data );
                        break;

                    case 'author':
                    case 'contributor':
                        foreach ( $data as $dataNode )
                        {
                            $this->generatePerson( $entryTag, $element, $dataNode );
                        }
                        break;

                    case 'link':
                        foreach ( $data as $dataNode )
                        {
                            $this->generateLink( $entryTag, $dataNode );
                        }
                        break;

                    case 'published':
                        // Sample date: 2003-12-13T18:30:02-05:00
                        $this->generateMetaData( $entryTag, $element, $data->date->format( 'c' ) );
                        break;

                    case 'category':
                        foreach ( $data as $dataNode )
                        {
                            $this->generateCategory( $entryTag, $dataNode );
                        }
                        break;

                    case 'source':
                        $this->generateSource( $entryTag, $data );
                        break;

                    case 'enclosure':
                        // convert RSS2 enclosure elements in ATOM link elements
                        foreach ( $data as $dataNode )
                        {
                            $link = new ezcFeedLinkElement();
                            $link->href = $dataNode->url;
                            $link->length = $dataNode->length;
                            $link->type = $dataNode->type;
                            $link->rel = 'enclosure';

                            $this->generateLink( $entryTag, $link );
                        }
                        break;
                }
            }

            $this->generateItemModules( $entry, $entryTag );
        }
    }

    /**
     * Parses the provided XML element object and stores it as a feed item in
     * the provided ezcFeed object.
     *
     * @param ezcFeedEntryElement $element The feed element object that will contain the feed item
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseItem( ezcFeedEntryElement $element, DOMElement $xml )
    {
        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;

                switch ( $tagName )
                {
                    case 'id':
                        $element->$tagName = $itemChild->textContent;
                        break;

                    case 'title':
                        $this->parseTextNode( $element, $itemChild, 'title' );
                        break;

                    case 'rights':
                        $this->parseTextNode( $element, $itemChild, 'copyright' );
                        break;

                    case 'summary':
                        $this->parseTextNode( $element, $itemChild, 'description' );
                        break;

                    case 'updated':
                    case 'published':
                        $element->$tagName = $itemChild->textContent;
                        break;

                    case 'author':
                    case 'contributor':
                        $subElement = $element->add( $tagName );
                        foreach ( $itemChild->childNodes as $subChild )
                        {
                            if ( $subChild->nodeType === XML_ELEMENT_NODE )
                            {
                                $subTagName = $subChild->tagName;
                                if ( in_array( $subTagName, array( 'name', 'email', 'uri' ) ) )
                                {
                                    $subElement->$subTagName = $subChild->textContent;
                                }
                            }
                        }
                        break;

                    case 'content':
                        $type = $itemChild->getAttribute( 'type' );
                        $src = $itemChild->getAttribute( 'src' );
                        $subElement = $element->add( $tagName );

                        switch ( $type )
                        {
                            case 'xhtml':
                                $nodes = $itemChild->childNodes;
                                if ( $nodes instanceof DOMNodeList )
                                {
                                    for ( $i = 0; $i < $nodes->length; $i++ )
                                    {
                                        if ( $nodes->item( $i ) instanceof DOMElement )
                                        {
                                            break;
                                        }
                                    }

                                    $contentNode = $nodes->item( $i );
                                    $subElement->text = $contentNode->nodeValue;
                                }
                                $subElement->type = $type;
                                break;

                            case 'html':
                                $subElement->text = $itemChild->textContent;
                                $subElement->type = $type;
                                break;

                            case 'text':
                                $subElement->text = $itemChild->textContent;
                                $subElement->type = $type;
                                break;

                            case null:
                                $subElement->text = $itemChild->textContent;
                                break;

                            default:
                                if ( preg_match( '@[+/]xml$@i', $type ) !== 0 )
                                {
                                    foreach ( $itemChild->childNodes as $node )
                                    {
                                        if ( $node->nodeType === XML_ELEMENT_NODE )
                                        {
                                            $doc = new DOMDocument( '1.0', 'UTF-8' );
                                            $copyNode = $doc->importNode( $node, true );
                                            $doc->appendChild( $copyNode );
                                            $subElement->text = $doc->saveXML();
                                            $subElement->type = $type;
                                            break;
                                        }
                                    }
                                }
                                else if ( substr_compare( $type, 'text/', 0, 5, true ) === 0 )
                                {
                                    $subElement->text = $itemChild->textContent;
                                    $subElement->type = $type;
                                    break;
                                }
                                else // base64
                                {
                                    $subElement->text = base64_decode( $itemChild->textContent );
                                    $subElement->type = $type;
                                }
                                break;
                        }

                        if ( !empty( $src ) )
                        {
                            $subElement->src = $src;
                        }

                        $language = $itemChild->getAttribute( 'xml:lang' );
                        if ( !empty( $language ) )
                        {
                            $subElement->language = $language;
                        }

                        break;

                    case 'link':
                        $subElement = $element->add( $tagName );

                        $attributes = array( 'href' => 'href', 'rel' => 'rel', 'hreflang' => 'hreflang',
                                             'type' => 'type', 'title' => 'title', 'length' => 'length' );

                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $itemChild->hasAttribute( $name ) )
                            {
                                $subElement->$alias = $itemChild->getAttribute( $name );
                            }
                        }
                        break;

                    case 'category':
                        $subElement = $element->add( $tagName );

                        $attributes = array( 'term' => 'term', 'scheme' => 'scheme', 'label' => 'label' );
                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $itemChild->hasAttribute( $name ) )
                            {
                                $subElement->$alias = $itemChild->getAttribute( $name );
                            }
                        }
                        break;

                    case 'source':
                        $subElement = $element->add( $tagName );
                        $this->parseSource( $subElement, $itemChild );
                        break;

                    default:
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
     * Parses the provided XML element object $xml and stores it as a text element
     *
     * @param ezcFeed|ezcFeedEntryElement $feed The feed object in which to store the parsed XML element as a text element
     * @param DOMElement $xml The XML element object to parse
     * @param string $element The name of the feed element object that will contain the text
     */
    private function parseTextNode( $feed, DOMElement $xml, $element )
    {
        $type = $xml->getAttribute( 'type' );

        switch ( $type )
        {
            case 'xhtml':
                $nodes = $xml->childNodes;
                if ( $nodes instanceof DOMNodeList )
                {
                    $contentNode = $nodes->item( 1 );
                    $feed->$element = $contentNode->nodeValue;
                }
                break;

            case 'html':
                $feed->$element = $xml->nodeValue;
                break;

            case 'text':
                // same case as 'default'

            default:
                $feed->$element = $xml->nodeValue;
                break;
        }

        if ( $type !== '' )
        {
            $feed->$element->type = $type;
        }

        if ( $xml->hasAttribute( 'xml:lang' ) )
        {
            $feed->$element->language = $xml->getAttribute( 'xml:lang' );
        }
    }

    /**
     * Parses the provided XML element object and stores it as a feed person (author
     * or contributor - based on $type) in the provided ezcFeedPersonElement object.
     *
     * @param ezcFeedPersonElement $element The feed element object that will contain the feed person
     * @param DOMElement $xml The XML element object to parse
     * @param string $type The type of the person (author, contributor)
     */
    private function parsePerson( $element, DOMElement $xml, $type )
    {
        foreach ( $xml->childNodes as $itemChild )
        {
            if ( $itemChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $itemChild->tagName;

                switch ( $tagName )
                {
                    case 'name':
                    case 'email':
                    case 'uri':
                        $element->$tagName = $itemChild->textContent;
                        break;
                }
            }
        }
    }

    /**
     * Parses the provided XML element object and stores it as a feed source
     * element in the provided ezcFeedSourceElement object.
     *
     * @param ezcFeedSourceElement $element The feed element object that will contain the feed source
     * @param DOMElement $xml The XML element object to parse
     */
    private function parseSource( ezcFeedSourceElement $element, DOMElement $xml )
    {
        foreach ( $xml->childNodes as $sourceChild )
        {
            if ( $sourceChild->nodeType === XML_ELEMENT_NODE )
            {
                $tagName = $sourceChild->tagName;

                switch ( $tagName )
                {
                    case 'title':
                        $this->parseTextNode( $element, $sourceChild, 'title' );
                        break;

                    case 'rights':
                        $this->parseTextNode( $element, $sourceChild, 'copyright' );
                        break;

                    case 'subtitle':
                        $this->parseTextNode( $element, $sourceChild, 'description' );
                        break;

                    case 'id':
                        $subElement = $element->add( 'id' );
                        $subElement->id = $sourceChild->textContent;
                        break;

                    case 'generator':
                        $subElement = $element->add( 'generator' );
                        $subElement->name = $sourceChild->textContent;

                        $attributes = array( 'uri' => 'url', 'version' => 'version' );
                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $sourceChild->hasAttribute( $name ) )
                            {
                                $subElement->$alias = $sourceChild->getAttribute( $name );
                            }
                        }
                        break;

                    case 'logo':
                        $subElement = $element->add( 'image' );
                        $subElement->link = $sourceChild->textContent;
                        break;

                    case 'icon':
                        $subElement = $element->add( 'icon' );
                        $subElement->link = $sourceChild->textContent;
                        break;

                    case 'updated':
                        $element->$tagName = $sourceChild->textContent;
                        break;

                    case 'category':
                        $subElement = $element->add( $tagName );

                        $attributes = array( 'term' => 'term', 'scheme' => 'scheme', 'label' => 'label' );
                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $sourceChild->hasAttribute( $name ) )
                            {
                                $subElement->$alias = $sourceChild->getAttribute( $name );
                            }
                        }
                        break;

                    case 'link':
                        $subElement = $element->add( $tagName );

                        $attributes = array( 'href' => 'href', 'rel' => 'rel', 'hreflang' => 'hreflang',
                                             'type' => 'type', 'title' => 'title', 'length' => 'length' );

                        foreach ( $attributes as $name => $alias )
                        {
                            if ( $sourceChild->hasAttribute( $name ) )
                            {
                                $subElement->$alias = $sourceChild->getAttribute( $name );
                            }
                        }
                        break;

                    case 'contributor':
                    case 'author':
                        $subElement = $element->add( $tagName );
                        $this->parsePerson( $subElement, $sourceChild, $tagName );
                        break;
                }
            }
        }
    }
}
?>
