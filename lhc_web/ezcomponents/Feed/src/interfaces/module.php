<?php
/**
 * File containing the ezcFeedModule class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Container for feed module data.
 *
 * Currently implemented by these feed modules:
 *  - Content ({@link ezcFeedContentModule}) -
 *    {@link http://purl.org/rss/1.0/modules/content/ Specifications}
 *  - DublinCore ({@link ezcFeedDublinCoreModule}) -
 *    {@link http://dublincore.org/documents/dces/ Specifications}
 *  - iTunes ({@link ezcFeedITunesModule}) -
 *    {@link http://www.apple.com/itunes/store/podcaststechspecs.html Specifications}
 *  - Geo ({@link ezcFeedGeoModule}) -
 *    {@link http://www.w3.org/2003/01/geo/ Specifications}
 *  - CreativeCommons ({@link ezcFeedCreativeCommonsModule}) -
 *    {@link http://backend.userland.com/creativeCommonsRssModule Specifications}
 *
 * The child classes must implement these static methods:
 * - isElementAllowed() - Returns true if an element can be added to the module.
 * - add() - Adds an element to the module.
 * - getModuleName() - Returns the module name (eg. 'DublinCore')
 * - getNamespace() - Returns the namespace for the module
 *   (eg. 'http://purl.org/dc/elements/1.1/').
 * - getNamespacePrefix() - Returns the namespace prefix for the module (eg. 'dc').
 *
 * @package Feed
 * @version 1.3
 */
abstract class ezcFeedModule
{
    /**
     * The level of the module data container. Possible values are 'feed' or 'item'.
     *
     * @var string
     */
    protected $level;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Constructs a new ezcFeedModule object.
     *
     * @param string $level The level of the data container ('feed' or 'item')
     */
    public function __construct( $level = 'feed' )
    {
        $this->level = $level;
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
        throw new ezcBasePropertyNotFoundException( $name );
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
        throw new ezcBasePropertyNotFoundException( $name );
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
        return false;
    }

    /**
     * Returns a new instance of the $name module with data container level $level.
     *
     * @param string $name The name of the module to create
     * @param string $level The level of the data container ('feed' or 'item')
     * @return ezcFeedModule
     */
    public static function create( $name, $level = 'feed' )
    {
        $supportedModules = ezcFeed::getSupportedModules();

        if ( !isset( $supportedModules[$name] ) )
        {
            throw new ezcFeedUnsupportedModuleException( $name );
        }

        return new $supportedModules[$name]( $level );
    }

    /**
     * Adds an attribute to the XML node $node.
     *
     * @param DOMDocument $xml The XML document where the node is stored
     * @param DOMNode $node The node to add the attribute to
     * @param string $attribute The name of the attribute to add
     * @param mixed $value The value of the attribute
     * @ignore
     */
    protected function addAttribute( DOMDocument $xml, DOMNode $node, $attribute, $value )
    {
        $attr = $xml->createAttribute( $attribute );
        $val = $xml->createTextNode( $value );
        $attr->appendChild( $val );
        $node->appendChild( $attr );
    }

    /**
     * Returns true if the element $name is allowed in the current module at the
     * current level (feed or item), and false otherwise.
     *
     * @param string $name The element name to check if allowed in the current module and level (feed or item)
     * @return bool
     */
    abstract public function isElementAllowed( $name );

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
    abstract public function add( $name );

    /**
     * Adds the module elements to the $xml XML document, in the container $root.
     *
     * @param DOMDocument $xml The XML document in which to add the module elements
     * @param DOMNode $root The parent node which will contain the module elements
     */
    abstract public function generate( DOMDocument $xml, DOMNode $root );

    /**
     * Parses the XML element $node and creates a feed element in the current
     * module with name $name.
     *
     * @param string $name The name of the element belonging to the module
     * @param DOMElement $node The XML child from which to take the values for $name
     */
    abstract public function parse( $name, DOMElement $node );
}
?>
