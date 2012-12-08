<?php
/**
 * File containing the ezcFeedITunesModule class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Support for the iTunes module: data container, generator, parser.
 *
 * Specifications: {@link http://www.apple.com/itunes/store/podcaststechspecs.html}.
 *
 * Create example:
 *
 * <code>
 * <?php
 * // $feed is an ezcFeed object
 * $item = $feed->add( 'item' );
 * $module = $item->addModule( 'iTunes' );
 * $category = $module->add( 'category' );
 * $category->term = 'Category name';
 * // add a sub-category
 * $subCategory = $category->add( 'category' );
 * $subCategory->term = 'Sub-category name';
 * ?>
 * </code>
 *
 * Parse example:
 *
 * <code>
 * <?php
 * // $feed is an ezcFeed object
 * if ( isset( $feed->iTunes ) )
 * {
 *     $iTunes = $feed->iTunes;
 *     if ( isset( $iTunes->category ) )
 *     {
 *         foreach ( $iTunes->category as $category )
 *         {
 *             echo $category->term;
 *             if ( isset( $category->category ) )
 *             {
 *                 foreach ( $category->category as $subCategory )
 *                 {
 *                     echo $subCategory->term;
 *                 }
 *             }
 *         }
 *     }
 * }
 * ?>
 * </code>
 *
 * @property ezcFeedPersonElement $author
 *                                The author of a resource. Can appear at both
 *                                feed-level and item-level.
 * @property ezcFeedTextElement $block
 *                              Prevents a feed or a feed item to appear. Can appear
 *                              at both feed-level and item-level. Valid values are 'yes'
 *                              and 'no', default 'no'.
 * @property array(ezcFeedCategoryElement) $category
 *                                         Categories for a feed. Can appear at feed-level only.
 *                                         Multiple categories can be specified, and categories
 *                                         can have sub-categories. The ampersands (&) in categories
 *                                         must be escaped to &amp;.
 *                                         {@link http://www.apple.com/itunes/store/podcaststechspecs.html#categories Valid iTunes categories}
 * @property ezcFeedTextElement $duration
 *                              The duration of a feed item. Can appear at item-level
 *                              only. Can be specified as HH:MM:SS, H:MM:SS, MM:SS,
 *                              M:SS or S (H = hours, M = minutes, S = seconds).
 * @property ezcFeedTextElement $explicit
 *                              Specifies if a feed or feed-item contains explicit
 *                              content. Can appear at both feed-level and item-level.
 *                              Valid values are 'clean', 'no' and 'yes', default 'no'.
 * @property ezcFeedImageElement $image
 *                               A link to an image for the feed. Can appear at both
 *                               feed-level and item-level only. The
 *                               {@link http://www.apple.com/itunes/store/podcaststechspecs.html iTunes specifications}
 *                               says that image is supported at feed-level only, but there
 *                               are many podcasts using image at item-level also, and there
 *                               are software applications supporting image at item-level too.
 *                               Use image at item-level at your own risk, as some software
 *                               applications might not support it. The Feed component supports
 *                               parsing and generating feeds with image at both feed-level and item-level.
 * @property ezcFeedTextElement $keywords
 *                              A list of keywords for a feed or feed item. Can appear
 *                              at both feed-level and item-level. The keywords should
 *                              be separated by commas.
 * @property ezcFeedLinkElement $newfeedurl
 *                              A new URL for the feed. Can appear at feed-level only. In
 *                              XML it will appear as 'new-feed-url'.
 * @property ezcFeedPersonElement $owner
 *                                The owner of the feed. Can appear at feed-level only.
 * @property ezcFeedTextElement $subtitle
 *                              Short description of a feed or feed item. Can appear
 *                              at both feed-level and item-level.
 * @property ezcFeedTextElement $summary
 *                              Longer description of a feed or feed item. Can appear
 *                              at both feed-level and item-level.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedITunesModule extends ezcFeedModule
{
    /**
     * Constructs a new ezcFeedITunesModule object.
     *
     * @param string $level The level of the data container ('feed' or 'item')
     */
    public function __construct( $level = 'feed' )
    {
        parent::__construct( $level );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     *
     * @param string $name The property name
     * @param mixed $value The property value
     * @ignore
     */
    public function __set( $name, $value )
    {
        if ( $this->isElementAllowed( $name ) )
        {
            switch ( $name )
            {
                case 'category':
                    $node = $this->add( $name );
                    $node->term = $value;
                    break;

                case 'date':
                    $node = $this->add( $name );
                    $node->date = $value;
                    break;

                case 'newfeedurl':
                    $node = $this->add( $name );
                    $node->href = $value;
                    break;

                case 'author':
                case 'owner':
                    $node = $this->add( $name );
                    $node->name = $value;
                    break;

                default:
                    $node = $this->add( $name );
                    $node->text = $value;
                    break;
            }
        }
        else
        {
            parent::__set( $name, $value );
        }
    }

    /**
     * Returns the value of property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     *
     * @param string $name The property name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        if ( $this->isElementAllowed( $name ) )
        {
            return $this->properties[$name];
        }
        else
        {
            return parent::__get( $name );
        }
    }

    /**
     * Returns if the property $name is set.
     *
     * @param string $name The property name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        if ( $this->isElementAllowed( $name ) )
        {
            return isset( $this->properties[$name] );
        }
        else
        {
            return parent::__isset( $name );
        }
    }

    /**
     * Adds the module elements to the $xml XML document, in the container $root.
     *
     * @param DOMDocument $xml The XML document in which to add the module elements
     * @param DOMNode $root The parent node which will contain the module elements
     */
    public function generate( DOMDocument $xml, DOMNode $root )
    {
        switch ( $this->level )
        {
            case 'feed':
                $elements = array( 'author', 'block', 'category',
                                   'explicit', 'image', 'keywords',
                                   'newfeedurl', 'owner', 'subtitle',
                                   'summary' );
                break;

            case 'item':
                $elements = array( 'author', 'block', 'duration',
                                   'explicit', 'image', 'keywords',
                                   'subtitle', 'summary' );
                break;
        }

        foreach ( $elements as $element )
        {
            if ( isset( $this->$element ) )
            {
                $values = $this->$element;

                switch ( $element )
                {
                    case 'category':
                        foreach ( $this->category as $values )
                        {
                            $elementTag = $xml->createElement( $this->getNamespacePrefix() . ':' . $element );
                            $root->appendChild( $elementTag );

                            // generate sub-categories
                            if ( isset( $values->category ) )
                            {
                                $subCategory = $values->category;
                                $subTag = $xml->createElement( $this->getNamespacePrefix() . ':' . 'category' );
                                $this->addAttribute( $xml, $subTag, 'text', $subCategory->term );
                                $elementTag->appendChild( $subTag );
                            }

                            if ( isset( $values->term ) )
                            {
                                $this->addAttribute( $xml, $elementTag, 'text', $values->term );
                            }
                        }
                        break;

                    case 'image':
                        $elementTag = $xml->createElement( $this->getNamespacePrefix() . ':' . $element );
                        $root->appendChild( $elementTag );

                        if ( isset( $values->link ) )
                        {
                            $this->addAttribute( $xml, $elementTag, 'href', $values->link );
                        }
                        break;

                    case 'owner':
                        $elementTag = $xml->createElement( $this->getNamespacePrefix() . ':' . $element );
                        $root->appendChild( $elementTag );

                        foreach ( array( 'email', 'name' ) as $subElement )
                        {
                            if ( isset( $values->$subElement ) )
                            {
                                $tag = $xml->createElement( $this->getNamespacePrefix() . ':' . $subElement );
                                $val = $xml->createTextNode( $values->$subElement );
                                $tag->appendChild( $val );
                                $elementTag->appendChild( $tag );
                            }
                        }

                        break;

                    case 'newfeedurl':
                        $elementTag = $xml->createElement( $this->getNamespacePrefix() . ':' . 'new-feed-url' );
                        $root->appendChild( $elementTag );

                        $elementTag->nodeValue = $values->href;
                        break;

                    default:
                        $elementTag = $xml->createElement( $this->getNamespacePrefix() . ':' . $element );
                        $root->appendChild( $elementTag );

                        $elementTag->nodeValue = $values->__toString();
                        break;
                }
            }
        }
    }

    /**
     * Parses the XML element $node and creates a feed element in the current
     * module with name $name.
     *
     * @param string $name The name of the element belonging to the module
     * @param DOMElement $node The XML child from which to take the values for $name
     */
    public function parse( $name, DOMElement $node )
    {
        if ( $name === 'new-feed-url' )
        {
            $name = 'newfeedurl';
        }

        if ( $this->isElementAllowed( $name ) )
        {
            $element = $this->add( $name );
            $value = $node->textContent;

            switch ( $name )
            {
                case 'category':
                    foreach ( $node->childNodes as $subNode )
                    {
                        if ( get_class( $subNode ) === 'DOMElement' )
                        {
                            $subCategory = $element->add( $name );

                            if ( $subNode->hasAttribute( 'text' ) )
                            {
                                $subCategory->term = $subNode->getAttribute( 'text' );
                            }
                        }
                    }

                    if ( $node->hasAttribute( 'text' ) )
                    {
                        $element->term = $node->getAttribute( 'text' );
                    }
                    break;

                case 'image':
                    // no textContent in $node
                    if ( $node->hasAttribute( 'href' ) )
                    {
                        $element->link = $node->getAttribute( 'href' );
                    }
                    break;

                case 'newfeedurl':
                    $element->href = $node->textContent;
                    break;

                case 'owner':
                    $namespace = self::getNamespace();

                    $nodes = $node->getElementsByTagNameNS( $namespace, 'email' );
                    if ( $nodes->length >= 1 )
                    {
                        $element->email = $nodes->item( 0 )->textContent;
                    }

                    $nodes = $node->getElementsByTagNameNS( $namespace, 'name' );
                    if ( $nodes->length >= 1 )
                    {
                        $element->name = $nodes->item( 0 )->textContent;
                    }
                    break;

                case 'author':
                    $element->name = $value;
                    break;

                default:
                    $element->text = $value;
            }
        }
    }

    /**
     * Returns true if the element $name is allowed in the current module at the
     * current level (feed or item), and false otherwise.
     *
     * @param string $name The element name to check if allowed in the current module and level (feed or item)
     * @return bool
     */
    public function isElementAllowed( $name )
    {
        switch ( $this->level )
        {
            case 'feed':
                if ( in_array( $name, array( 'author', 'block', 'category',
                                             'explicit', 'image', 'keywords',
                                             'newfeedurl', 'owner', 'subtitle',
                                             'summary' ) ) )
                {
                    return true;
                }
                break;

            case 'item':
                if ( in_array( $name, array( 'author', 'block', 'duration',
                                             'explicit', 'image', 'keywords',
                                             'subtitle', 'summary' ) ) )
                {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * Adds a new ezcFeedElement element with name $name to this module and
     * returns it.
     *
     * @throws ezcFeedUnsupportedElementException
     *         if trying to add an element which is not supported.
     *
     * @param string $name The element name
     * @return ezcFeedElement
     */
    public function add( $name )
    {
        if ( $this->isElementAllowed( $name ) )
        {
            switch ( $name )
            {
                case 'category':
                    $node = new ezcFeedCategoryElement();
                    $this->properties[$name][] = $node;
                    break;

                case 'newfeedurl':
                    $node = new ezcFeedLinkElement();
                    $this->properties[$name] = $node;
                    break;

                case 'owner':
                case 'author':
                    $node = new ezcFeedPersonElement();
                    $this->properties[$name] = $node;
                    break;

                case 'image':
                    $node = new ezcFeedImageElement();
                    $this->properties[$name] = $node;
                    break;

                default:
                    $node = new ezcFeedTextElement();
                    $this->properties[$name] = $node;
                    break;
            }
            return $node;
        }
        else
        {
            throw new ezcFeedUnsupportedElementException( $name );
        }
    }

    /**
     * Returns the module name ('iTunes').
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'iTunes';
    }

    /**
     * Returns the namespace for this module ('http://www.itunes.com/dtds/podcast-1.0.dtd').
     *
     * @return string
     */
    public static function getNamespace()
    {
        return 'http://www.itunes.com/dtds/podcast-1.0.dtd';
    }

    /**
     * Returns the namespace prefix for this module ('itunes').
     *
     * @return string
     */
    public static function getNamespacePrefix()
    {
        return 'itunes';
    }
}
?>
