<?php
/**
 * File containing the ezcFeed class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a feed.
 *
 * A feed has a type (eg. RSS1, RSS2 or ATOM). The feed type defines which
 * processor is used to parse and generate that type.
 *
 * The following feed processors are supported by the Feed component:
 *  - ATOM ({@link ezcFeedAtom}) -
 *    {@link http://atompub.org/rfc4287.html RFC4287}
 *  - RSS1 ({@link ezcFeedRss1}) -
 *    {@link http://web.resource.org/rss/1.0/spec Specifications}
 *  - RSS2 ({@link ezcFeedRss2}) -
 *    {@link http://www.rssboard.org/rss-specification Specifications}
 *
 * A new processor can be defined by creating a class which extends the class
 * {@link ezcFeedProcessor} and implements the interface {@link ezcFeedParser}.
 * The new class needs to be added to the supported feed types list by calling
 * the function {@link registerFeed}.
 *
 * The following modules are supported by the Feed component:
 *  - Content ({@link ezcFeedContentModule}) -
 *    {@link http://purl.org/rss/1.0/modules/content/ Specifications}
 *  - CreativeCommons ({@link ezcFeedCreativeCommonsModule}) -
 *    {@link http://backend.userland.com/creativeCommonsRssModule Specifications}
 *  - DublinCore ({@link ezcFeedDublinCoreModule}) -
 *    {@link http://dublincore.org/documents/dces/ Specifications}
 *  - Geo ({@link ezcFeedGeoModule}) -
 *    {@link http://www.w3.org/2003/01/geo/ Specifications}
 *  - iTunes ({@link ezcFeedITunesModule}) -
 *    {@link http://www.apple.com/itunes/store/podcaststechspecs.html Specifications}
 *
 * A new module can be defined by creating a class which extends the class
 * {@link ezcFeedModule}. The new class needs to be added to the supported modules
 * list by calling {@link registerModule}.
 *
 * A feed object can be created in different ways:
 *  - by calling the constructor (with the optional feed type). Example:
 *
 *  <code>
 *  $feed = new ezcFeed();
 *  </code>
 *
 *  - by parsing an existing XML file or URL. The feed type of the resulting
 *    ezcFeed object will be autodetected. Example:
 *
 *  <code>
 *  $feed = ezcFeed::parse( 'http://www.example.com/rss2.xml' ); // URL
 *  $feed = ezcFeed::parse( 'http://username:password@www.example.com/rss2.xml' ); // URL with HTTP authentication
 *  $feed = ezcFeed::parse( '/tmp/rss2.xml' ); // local file
 *  </code>
 *
 *  - by parsing an XML document stored in a string variable. The feed type of
 *    the resulting ezcFeed object will be autodetected. Example:
 *
 *  <code>
 *  $feed = ezcFeed::parseContent( $xmlString );
 *  </code>
 *
 * Parsing a feed (in the following examples $feed is an existing ezcFeed object):
 * - get a value from the feed object. Example:
 *
 * <code>
 * $title = $feed->title->__toString();
 * </code>
 *
 * - iterate over the items in the feed. Example:
 *
 * <code>
 * <?php
 * // retrieve the titles from the feed items
 * foreach ( $feed->item as $item )
 * {
 *     $titles[] = $item->title->__toString();
 * }
 * </code>
 *
 * - parse a module. Example of parsing the Geo module (ezcFeedGeoModule):
 *
 * <code>
 * <?php
 * $locations = array();
 * foreach ( $feed->item as $item )
 * {
 *     if ( isset( $item->Geo ) )
 *     {
 *         $locations[] = array(
 *             'title' => $item->title->__toString(),
 *             'alt' => isset( $item->Geo->alt ) ? $item->Geo->alt->__toString() : null,
 *             'lat' => isset( $item->Geo->lat ) ? $item->Geo->lat->__toString() : null,
 *             'long' => isset( $item->Geo->long ) ? $item->Geo->long->__toString() : null
 *             );
 *     }
 * }
 * ?>
 * </code>
 *
 * - iterate over the loaded modules in a feed item. Example:
 *
 * <code>
 * <?php
 * // display the names and namespaces of the modules loaded in the feed item $item
 * foreach ( $item->getModules() as $moduleName => $module )
 * {
 *     echo $moduleName . ':' . $module->getNamespace();
 * }
 * ?>
 * </code>
 *
 * Generating a feed:
 * - create a feed object. Example:
 *
 * <code>
 * $feed = new ezcFeed();
 * </code>
 *
 * - set a value to the feed object. Example:
 *
 * <code>
 * $feed->title = 'News';
 * </code>
 *
 * - add a new item to the feed. Example:
 *
 * <code>
 * <?php
 * $item = $feed->add( 'item' );
 * $item->title = 'Item title';
 * ?>
 * </code>
 *
 * - add a new module to the feed item. Example:
 *
 * <code>
 * <?php
 * $item = $feed->add( 'item' );
 * $module = $item->addModule( 'Content' );
 * $content->encoded = 'text content which will be encoded';
 * ?>
 * </code>
 *
 * - generate an XML document from the {@link ezcFeed} object. The result
 *   string should be saved to a file, and a link to a file made accessible
 *   to users of the application. Example:
 *
 * <code>
 * <?php
 * $xmlAtom = $feed->generate( 'atom' );
 * $xmlRss1 = $feed->generate( 'rss1' );
 * $xmlRss2 = $feed->generate( 'rss2' );
 * ?>
 * </code>
 *
 * Note: Assigning values to feed elements should be done in a way that will not
 * break the resulting XML document. In other words, encoding of special characters
 * to HTML entities is not done by default, and the developer is responsible with
 * calling htmlentities() himself when assigning values to feed elements. Example:
 * if the feed title contains the "&" character, it is the responsability of the
 * developer to encode it properly as "&amp;".
 *
 * Example of creating a feed with a user-defined type:
 *
 * <code>
 * <?php
 * ezcFeed::registerFeed( 'opml', 'myOpmlHandler');
 *
 * $feed = new ezcFeed();
 * // add properties to $feed
 *
 * $xml = $feed->generate( 'opml' );
 * ?>
 * </code>
 *
 * In the above example, myOpmlHandler extends {@link ezcFeedProcessor} and
 * implements {@link ezcFeedParser}.
 *
 * Example of creating a feed with a user-defined module:
 *
 * <code>
 * <?php
 * ezcFeed::registerModule( 'Slash', 'mySlashHandler', 'slash');
 *
 * $feed = new ezcFeed();
 * $item = $feed->add( 'item' );
 * $slash = $item->addModule( 'Slash' );
 * // add properties for the Slash module to $slash
 *
 * $xml = $feed->generate( 'rss2' ); // or the feed type which is needed
 * ?>
 * </code>
 *
 * In the above example mySlashHandler extends {@link ezcFeedModule}.
 *
 * @property array(ezcFeedPersonElement) $author
 *           Author(s) of the feed. Equivalents:
 *           ATOM-author (required, multiple),
 *           RSS1-none,
 *           RSS2-managingEditor (optional, recommended, single).
 * @property array(ezcFeedCategoryElement) $category
 *           Categories for the feed. Equivalents:
 *           ATOM-category (optional, multiple),
 *           RSS1-none,
 *           RSS2-category (optional, multiple).
 * @property ezcFeedCloudElement $cloud
 *           Allows processes to register with a cloud to be notified of updates
 *           to the channel, implementing a lightweight publish-subscribe
 *           protocol for RSS feeds. Equivalents:
 *           ATOM-none,
 *           RSS1-none,
 *           RSS2-cloud (optional, not recommended, single).
 * @property array(ezcFeedPersonElement) $contributor
 *           Contributor(s) for the feed. Equivalents:
 *           ATOM-contributor (optional, not recommended, multiple),
 *           RSS1-none,
 *           RSS2-none.
 * @property ezcFeedTextElement $copyright
 *           Copyright information for the feed. Equivalents:
 *           ATOM-rights (optional, single),
 *           RSS1-none,
 *           RSS2-copyright (optional, single).
 * @property ezcFeedTextElement $description
 *           A short description of the feed. Equivalents:
 *           ATOM-subtitle (required, single),
 *           RSS1-description (required, single),
 *           RSS2-description (required, single).
 * @property ezcFeedTextElement $docs
 *           An URL that points to the documentation for the format used in the
 *           feed file. Equivalents:
 *           ATOM-none,
 *           RSS1-none,
 *           RSS2-docs (optional, not recommended, single) - usual value is
 *           {@link http://www.rssboard.org/rss-specification}.
 * @property ezcFeedGeneratorElement $generator
 *           Indicates the software used to generate the feed. Equivalents:
 *           ATOM-generator (optional, single),
 *           RSS1-none,
 *           RSS2-generator (optional, single).
 * @property ezcFeedImageElement $icon
 *           An icon for a feed, similar with favicon.ico for websites. Equivalents:
 *           ATOM-icon (optional, not recommended, single),
 *           RSS1-none,
 *           RSS2-none.
 * @property ezcFeedIdElement $id
 *           A universally unique and permanent identifier for a feed. For
 *           example, it can be an Internet domain name. Equivalents:
 *           ATOM-id (required, single),
 *           RSS1-about (required, single),
 *           RSS2-id (optional, single).
 * @property ezcFeedImageElement $image
 *           An image associated with the feed. Equivalents:
 *           ATOM-logo (optional, single),
 *           RSS1-image (optional, single),
 *           RSS2-image (optional, single).
 * @property-read array(ezcFeedEntryElement) $item
 *           Feed items (entries). Equivalents:
 *           ATOM-entry (optional, recommended, multiple),
 *           RSS1-item (required, multiple),
 *           RSS2-item (required, multiple).
 * @property ezcFeedTextElement $language
 *           The language for the feed. Equivalents:
 *           ATOM-xml:lang attribute for title, description, copyright, content,
 *           comments (optional, single) - accessed as language through ezcFeed,
 *           RSS1-none,
 *           RSS2-language (optional, single).
 * @property array(ezcFeedLinkElement) $link
 *           URLs to the HTML websites corresponding to the channel. Equivalents:
 *           ATOM-link (required one link with rel='self', multiple),
 *           RSS1-link (required, single),
 *           RSS2-link (required, single).
 * @property ezcFeedDateElement $published
 *           The time the feed was published. Equivalents:
 *           ATOM-none,
 *           RSS1-none,
 *           RSS2-pubDate (optional, not recommended, single).
 * @property ezcFeedTextElement $rating
 *           The {@link http://www.w3.org/PICS/ PICS} rating for the channel. Equivalents:
 *           ATOM-none,
 *           RSS1-none,
 *           RSS2-rating (optional, not recommended, single).
 * @property ezcFeedSkipDaysElement $skipDays
 *           A hint for aggregators telling them which days they can skip when
 *           reading the feed. Equivalents:
 *           ATOM-none,
 *           RSS1-none,
 *           RSS2-skipDays (optional, not recommended, single).
 * @property ezcFeedSkipHoursElement $skipHours
 *           A hint for aggregators telling them which hours they can skip when
 *           reading the feed. Equivalents:
 *           ATOM-none,
 *           RSS1-none,
 *           RSS2-skipHours (optional, not recommended, single).
 * @property ezcFeedTextInputElement $textInput
 *           Specifies a text input box that can be displayed with the feed. Equivalents:
 *           ATOM-none,
 *           RSS1-textinput (optional, not recommended, single),
 *           RSS2-textInput (optional, not recommended, single).
 * @property ezcFeedTextElement $title
 *           Human readable title for the feed. For example, it can be the same
 *           as the website title. Equivalents:
 *           ATOM-title (required, single),
 *           RSS1-title (required, single),
 *           RSS2-title (required, single).
 * @property ezcFeedTextElement $ttl
 *           Number of minutes that indicates how long a channel can be cached
 *           before refreshing from the source. Equivalents:
 *           ATOM-none,
 *           RSS1-none,
 *           RSS2-ttl (optional, not recommended, single).
 * @property ezcFeedDateElement $updated
 *           The last time the feed was updated. Equivalents:
 *           ATOM-updated (required, single),
 *           RSS1-none,
 *           RSS2-lastBuildDate (optional, recommended, single).
 * @property ezcFeedPersonElement $webMaster
 *           The email address of the webmaster responsible for the feed. Equivalents:
 *           ATOM-none,
 *           RSS1-none,
 *           RSS2-webMaster (optional, not recommended, single).
 *
 * @todo parse() and parseContent() should(?) handle common broken XML files
 *       (for example if the first line is not <?xml version="1.0"?>)
 *
 * @package Feed
 * @version 1.3
 * @mainclass
 */
class ezcFeed
{
    /**
     * The version of the feed generator, to be included in the generated feeds.
     */
    const GENERATOR_VERSION = '1.3';

    /**
     * The uri of the feed generator, to be included in the generated feeds.
     */
    const GENERATOR_URI = 'http://ezcomponents.org/docs/tutorials/Feed';

    /**
     * Holds a list of all supported feed types.
     *
     * @var array(string=>string)
     */
    private static $supportedFeedTypes = array();

    /**
     * Holds a list of all supported modules.
     *
     * @var array(string=>string)
     */
    private static $supportedModules = array();

    /**
     * Holds a list of all supported modules prefixes.
     *
     * @var array(string=>string)
     */
    private static $supportedModulesPrefixes = array();

    /**
     * Holds the feed type (eg. 'rss1').
     *
     * @var string
     */
    private $feedType;

    /**
     * Holds the feed content type (eg. 'application/rss+xml').
     *
     * @var string
     */
    private $contentType;

    /**
     * Holds the feed elements (ezcFeedElement).
     *
     * @var array(string=>mixed)
     */
    private $elements;

    /**
     * Holds the modules used by this feed.
     *
     * @var array(ezcFeedModule)
     */
    private $modules = array();

    /**
     * Creates a new feed object.
     *
     * The $type value is used when calling generate() without specifying a
     * feed type to output.
     *
     * @throws ezcFeedUnsupportedTypeException
     *         if the feed type $type is not supported
     *
     * @param string $type The type of feed to create
     */
    public function __construct( $type = null )
    {
        self::initSupportedTypes();

        if ( $type !== null )
        {
            $type = strtolower( $type );

            if ( !isset( self::$supportedFeedTypes[$type] ) )
            {
                throw new ezcFeedUnsupportedTypeException( $type );
            }

            $this->feedType = $type;
            $className = self::$supportedFeedTypes[$type];
            $this->contentType = constant( "{$className}::CONTENT_TYPE" );
        }

        // set default values
        $version = ( ezcFeed::GENERATOR_VERSION === '//auto' . 'gentag//' ) ? 'dev' : ezcFeed::GENERATOR_VERSION;

        $generator = $this->add( 'generator' );
        $generator->name = 'eZ Components Feed';
        $generator->version = $version;
        $generator->url = ezcFeed::GENERATOR_URI;
    }

    /**
     * Sets the property $name to $value.
     *
     * @param string $name The property name
     * @param mixed $value The property value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'author':
            case 'contributor':
            case 'webMaster':
                $element = $this->add( $name );
                $element->name = $value;
                break;

            case 'title':
            case 'description':
            case 'docs':
            case 'ttl':
            case 'rating':
            case 'language':
            case 'copyright':
                $element = $this->add( $name );
                $element->text = $value;
                break;

            case 'generator':
                $element = $this->add( $name );
                $element->name = $value;
                break;

            case 'item':
                $element = $this->add( $name );
                break;

            case 'published':
            case 'updated':
                $element = $this->add( $name );
                $element->date = $value;
                break;

            case 'textInput':
                $element = $this->add( $name );
                break;

            case 'skipDays':
                $element = $this->add( $name );
                break;

            case 'skipHours':
                $element = $this->add( $name );
                break;

            case 'link':
                $element = $this->add( $name );
                $element->href = $value;
                break;

            case 'generator':
                $element = $this->add( $name );
                $element->name = $value;
                break;

            case 'image':
            case 'icon':
                $element = $this->add( $name );
                $element->link = $value;
                break;

            case 'id':
                $element = $this->add( $name );
                $element->id = $value;
                break;

            default:
                $supportedModules = ezcFeed::getSupportedModules();
                if ( isset( $supportedModules[$name] ) )
                {
                    $this->setModule( $name, $value );
                    return;
                }
        }
    }

    /**
     * Returns the value of property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the property $name does not exist.
     *
     * @param string $name The property name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'author':
            case 'category':
            case 'cloud':
            case 'contributor':
            case 'copyright':
            case 'description':
            case 'docs':
            case 'generator':
            case 'icon':
            case 'id':
            case 'image':
            case 'item':
            case 'language':
            case 'link':
            case 'published':
            case 'rating':
            case 'skipDays':
            case 'skipHours':
            case 'textInput':
            case 'title':
            case 'ttl':
            case 'updated':
            case 'webMaster':
                if ( isset( $this->elements[$name] ) )
                {
                    return $this->elements[$name];
                }
                break;

            default:
                $supportedModules = ezcFeed::getSupportedModules();
                if ( isset( $supportedModules[$name] ) )
                {
                    if ( $this->hasModule( $name ) )
                    {
                        return $this->getModule( $name );
                    }
                    else
                    {
                        throw new ezcFeedUndefinedModuleException( $name );
                    }
                }

                throw new ezcFeedUnsupportedModuleException( $name );
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
        switch ( $name )
        {
            case 'author':
            case 'category':
            case 'cloud':
            case 'contributor':
            case 'copyright':
            case 'description':
            case 'docs':
            case 'generator':
            case 'icon':
            case 'id':
            case 'image':
            case 'item':
            case 'language':
            case 'link':
            case 'published':
            case 'rating':
            case 'skipDays':
            case 'skipHours':
            case 'textInput':
            case 'title':
            case 'ttl':
            case 'updated':
            case 'webMaster':
                return isset( $this->elements[$name] );

            default:
                $supportedModules = ezcFeed::getSupportedModules();
                if ( isset( $supportedModules[$name] ) )
                {
                    return $this->hasModule( $name );
                }
        }

        return false;
    }

    /**
     * Adds a new module to this item and returns it.
     *
     * @param string $name The name of the module to add
     * @return ezcFeedModule
     */
    public function addModule( $name )
    {
        $this->$name = ezcFeedModule::create( $name, 'feed' );
        return $this->$name;
    }

    /**
     * Associates the module $module with the name $name.
     *
     * @param string $name The name of the module associate
     * @param ezcFeedModule $module The module to set under the name $name
     */
    public function setModule( $name, ezcFeedModule $module )
    {
        $this->modules[$name] = $module;
    }

    /**
     * Returns the loaded module $name.
     *
     * @param string $name The name of the module to return
     * @return ezcFeedModule
     */
    public function getModule( $name )
    {
        return $this->modules[$name];
    }

    /**
     * Returns true if the module $name is loaded, false otherwise.
     *
     * @param string $name The name of the module to check if loaded for this item
     * @return bool
     */
    public function hasModule( $name )
    {
        return isset( $this->modules[$name] );
    }

    /**
     * Returns an array with all the modules loaded at feed-level.
     *
     * @return array(ezcFeedModule)
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Adds a new ezcFeedElement element with name $name and returns it.
     *
     * @throws ezcFeedUnsupportedElementException
     *         if the element $name is not supported
     *
     * @param string $name The element name
     * @return ezcFeedElement|null
     */
    public function add( $name )
    {
        switch ( $name )
        {
            case 'item':
                $element = new ezcFeedEntryElement();
                $this->elements[$name][] = $element;
                break;

            case 'author':
            case 'contributor':
            case 'webMaster':
                $element = new ezcFeedPersonElement();
                $this->elements[$name][] = $element;
                break;

            case 'image':
            case 'icon':
                $element = new ezcFeedImageElement();
                $this->elements[$name] = $element;
                break;

            case 'category':
                $element = new ezcFeedCategoryElement();
                $this->elements[$name][] = $element;
                break;

            case 'textInput':
                $element = new ezcFeedTextInputElement();
                $this->elements[$name] = $element;
                break;

            case 'title':
            case 'description':
            case 'copyright':
            case 'rating':
            case 'comments':
            case 'ttl':
            case 'language':
            case 'docs':
                $element = new ezcFeedTextElement();
                $this->elements[$name] = $element;
                break;

            case 'skipDays':
                $element = new ezcFeedSkipDaysElement();
                $this->elements[$name] = $element;
                break;

            case 'skipHours':
                $element = new ezcFeedSkipHoursElement();
                $this->elements[$name] = $element;
                break;

            case 'link':
                $element = new ezcFeedLinkElement();
                $this->elements[$name][] = $element;
                break;

            case 'generator':
                $element = new ezcFeedGeneratorElement();
                $this->elements[$name] = $element;
                break;

            case 'cloud':
                $element = new ezcFeedCloudElement();
                $this->elements[$name] = $element;
                break;

            case 'id':
                $element = new ezcFeedIdElement();
                $this->elements[$name] = $element;
                break;

            case 'updated':
            case 'published':
                $element = new ezcFeedDateElement();
                $this->elements[$name] = $element;
                break;

            default:
                throw new ezcFeedUnsupportedElementException( $name );
        }

        return $element;
    }

    /**
     * Generates and returns an XML document of type $type from the current
     * object.
     *
     * If the type was defined when creating the ezcFeed object, then that
     * type will be used if no type is specified when calling generate().
     *
     * If no type was specified when calling the constructor and no type
     * was specified when calling generate then an exception will be thrown.
     *
     * @throws ezcFeedUnsupportedTypeException
     *         if the feed type $type is not supported
     *
     * @param string $type The feed type to generate
     * @return string
     */
    public function generate( $type = null )
    {
        if ( $this->feedType === null
             && $type === null )
        {
            throw new ezcFeedUnsupportedTypeException( null );
        }

        if ( $type !== null )
        {
            $type = strtolower( $type );

            if ( !isset( self::$supportedFeedTypes[$type] ) )
            {
                throw new ezcFeedUnsupportedTypeException( $type );
            }
        }

        if ( $type !== null )
        {
            $this->feedType = $type;
        }

        $className = self::$supportedFeedTypes[$this->feedType];
        $generator = new $className( $this );

        $this->contentType = constant( "{$className}::CONTENT_TYPE" );
        return $generator->generate();
    }

    /**
     * Parses the XML document in the $uri and returns an ezcFeed object with
     * the type autodetected from the XML document.
     *
     * Example of parsing an XML document stored at an URL:
     * <code>
     * $feed = ezcFeed::parse( 'http://www.example.com/rss2.xml' );
     * </code>
     *
     * Example of parsing an XML document protected with HTTP authentication:
     * <code>
     * $feed = ezcFeed::parse( 'http://username:password@www.example.com/rss2.xml' );
     * </code>
     *
     * If trying to parse an XML document protected with HTTP authentication
     * without providing a valid username and password, the exception
     * {@link ezcFeedParseErrorException} will be thrown.
     *
     * Example of parsing an XML document stored in a local file:
     * <code>
     * $feed = ezcFeed::parse( '/tmp/feed.xml' );
     * </code>
     *
     * @throws ezcBaseFileNotFoundException
     *         If the XML file at $uri could not be found.
     * @throws ezcFeedParseErrorException
     *         If the content at $uri is not a valid XML document.
     *
     * @param string $uri An URI which stores an XML document
     * @return ezcFeed
     */
    public static function parse( $uri )
    {
        if ( !file_exists( $uri ) )
        {
            // hide the notices caused by getaddrinfo (php_network_getaddresses)
            // in case of unreachable hosts ("Name or service not known")
            $headers = @get_headers( $uri );
            // HTTP headers
            // 200 = OK
            // 301 = moved permanently
            // 302 = found
            // 307 = temporary redirect
            if ( preg_match( "@200|301|302|307@", $headers[0] ) === 0 )
            {
                throw new ezcBaseFileNotFoundException( $uri );
            }
        }

        $xml = new DOMDocument();
        $oldSetting = libxml_use_internal_errors( true );
        $retval = $xml->load( $uri );
        libxml_use_internal_errors( $oldSetting );

        if ( $retval === false )
        {
            libxml_clear_errors();
            throw new ezcFeedParseErrorException( $uri, "It is not a valid XML file" );
        }

        return self::dispatchXml( $xml );
    }

    /**
     * Parses the XML document stored in $content and returns an ezcFeed object
     * with the type autodetected from the XML document.
     *
     * Example of parsing an XML document stored in a string:
     * <code>
     * // $xmlString contains a valid XML string
     * $feed = ezcFeed::parseContent( $xmlString );
     * </code>
     *
     * @throws ezcFeedParseErrorException
     *         If $content is not a valid XML document.
     *
     * @param string $content A string variable which stores an XML document
     * @return ezcFeed
     */
    public static function parseContent( $content )
    {
        if ( empty( $content ) )
        {
            throw new ezcFeedParseErrorException( null, "Content is empty" );
        }

        $xml = new DOMDocument();
        $oldSetting = libxml_use_internal_errors( true );
        $retval = $xml->loadXML( $content );
        libxml_use_internal_errors( $oldSetting );

        if ( $retval === false )
        {
            libxml_clear_errors();
            throw new ezcFeedParseErrorException( null, "Content is no valid XML" );
        }

        return self::dispatchXml( $xml );
    }

    /**
     * Returns the supported feed types.
     *
     * The array returned is (default):
     * <code>
     * array(
     *    'rss1' => 'ezcFeedRss1',
     *    'rss2' => 'ezcFeedRss2',
     *    'atom' => 'ezcFeedAtom'
     * );
     * </code>
     *
     * If the function {@link registerFeed} was used to add another supported feed
     * type to ezcFeed, it will show up in the returned array as well.
     *
     * @return array(string)
     */
    public static function getSupportedTypes()
    {
        return self::$supportedFeedTypes;
    }

    /**
     * Returns the supported feed modules.
     *
     * The array returned is (default):
     * <code>
     * array(
     *    'Content'         => 'ezcFeedContentModule',
     *    'CreativeCommons' => 'ezcFeedCreativeCommonsModule',
     *    'DublinCore'      => 'ezcFeedDublinCoreModule',
     *    'Geo'             => 'ezcFeedGeoModule',
     *    'iTunes'          => 'ezcFeedITunesModule'
     * );
     * </code>
     *
     * If the function {@link registerModule} was used to add another supported
     * module type to ezcFeed, it will show up in the returned array as well.
     *
     * @return array(string=>string)
     */
    public static function getSupportedModules()
    {
        return self::$supportedModules;
    }

    /**
     * Returns the supported feed modules prefixes.
     *
     * The array returned is (default):
     * <code>
     * array(
     *    'content'         => 'Content',
     *    'creativeCommons' => 'CreativeCommons',
     *    'dc'              => 'DublinCore',
     *    'geo'             => 'Geo',
     *    'itunes'          => 'iTunes'
     * );
     * </code>
     *
     * If the function {@link registerModule} was used to add another supported
     * module type to ezcFeed, it will show up in the returned array as well.
     *
     * @return array(string=>string)
     */
    public static function getSupportedModulesPrefixes()
    {
        return self::$supportedModulesPrefixes;
    }

    /**
     * Returns the feed type of this feed object (eg. 'rss2').
     *
     * @return string
     */
    public function getFeedType()
    {
        return $this->feedType;
    }

    /**
     * Returns the feed content type of this feed object
     * (eg. 'application/rss+xml').
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Adds the feed type $name to the supported list of feed types.
     *
     * After registering a feed type, it can be used to create or parse feed
     * documents.
     *
     * Example of creating a feed with a user-defined type:
     * <code>
     * ezcFeed::registerFeed( 'opml', 'myOpmlHandler');
     *
     * $feed = new ezcFeed( 'opml' );
     * // add properties for the Opml feed type to $feed
     * </code>
     *
     * In the above example, myOpmlHandler extends {@link ezcFeedProcessor}
     * and implements {@link ezcFeedParser}.
     *
     * @param string $name The feed type (eg. 'opml' )
     * @param string $class The handler class for this feed type (eg. 'myOpmlHandler')
     */
    public static function registerFeed( $name, $class )
    {
        self::$supportedFeedTypes[$name] = $class;
    }

    /**
     * Removes a previously registered feed type from the list of supported
     * feed types.
     *
     * @param string $name The name of the feed type to remove (eg. 'opml')
     */
    public static function unregisterFeed( $name )
    {
        if ( isset( self::$supportedFeedTypes[$name] ) )
        {
            unset( self::$supportedFeedTypes[$name] );
        }
    }

    /**
     * Adds the module $name to the supported list of modules.
     *
     * After registering a module, it can be used to create or parse feed
     * documents.
     *
     * Example of creating a feed with a user-defined module:
     * <code>
     * ezcFeed::registerModule( 'Slash', 'mySlashHandler', 'slash');
     *
     * $feed = new ezcFeed( 'rss2' );
     * $item = $feed->add( 'item' );
     * $slash = $item->addModule( 'Slash' );
     * // add properties for the Slash module to $slash
     * </code>
     *
     * @param string $name The module name (eg. 'Slash' )
     * @param string $class The handler class for this module (eg. 'mySlashHandler')
     * @param string $namespacePrefix The XML namespace prefix for this module (eg. 'slash')
     */
    public static function registerModule( $name, $class, $namespacePrefix )
    {
        self::$supportedModules[$name] = $class;
        self::$supportedModulesPrefixes[$namespacePrefix] = $name;
    }

    /**
     * Removes a previously registered module from the list of supported modules.
     *
     * @param string $name The name of the module to remove (eg. 'Slash')
     */
    public static function unregisterModule( $name )
    {
        if ( isset( self::$supportedModules[$name] ) )
        {
            $namePrefix = null;
            foreach ( self::$supportedModulesPrefixes as $prefix => $module )
            {
                if ( $module === $name )
                {
                    $namePrefix = $prefix;
                    break;
                }
            }
            unset( self::$supportedModulesPrefixes[$prefix] );
            unset( self::$supportedModules[$name] );
        }
    }

    /**
     * Parses the $xml object by dispatching it to the processor that can
     * handle it.
     *
     * @throws ezcFeedParseErrorException
     *         If the $xml object could not be parsed by any available processor.
     *
     * @param DOMDocument $xml The XML object to parse
     * @return ezcFeed
     */
    private static function dispatchXml( DOMDocument $xml )
    {
        if ( count( self::getSupportedTypes() ) === 0 )
        {
            self::initSupportedTypes();
        }

        foreach ( self::getSupportedTypes() as $feedType => $feedClass )
        {
            $canParse = call_user_func( array( $feedClass, 'canParse' ), $xml );
            if ( $canParse === true )
            {
                $feed = new ezcFeed( $feedType );
                $parser = new $feedClass( $feed );
                return $parser->parse( $xml );
            }
        }

        throw new ezcFeedParseErrorException( $xml->documentURI, 'Feed type not recognized' );
    }

    /**
     * Initializes the supported feed types and modules to the default values.
     */
    private static function initSupportedTypes()
    {
        self::registerFeed( 'rss1', 'ezcFeedRss1' );
        self::registerFeed( 'rss2', 'ezcFeedRss2' );
        self::registerFeed( 'atom', 'ezcFeedAtom' );

        self::registerModule( 'Content', 'ezcFeedContentModule', 'content' );
        self::registerModule( 'CreativeCommons', 'ezcFeedCreativeCommonsModule', 'creativeCommons' );
        self::registerModule( 'DublinCore', 'ezcFeedDublinCoreModule', 'dc' );
        self::registerModule( 'Geo', 'ezcFeedGeoModule', 'geo' );
        self::registerModule( 'iTunes', 'ezcFeedITunesModule', 'itunes' );
    }
}
?>
