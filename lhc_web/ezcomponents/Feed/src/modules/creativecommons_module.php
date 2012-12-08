<?php
/**
 * File containing the ezcFeedCreativeCommonsModule class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Support for the CreativeCommons module: data container, generator, parser.
 *
 * Specifications: {@link http://backend.userland.com/creativeCommonsRssModule}.
 *
 * Create example:
 *
 * <code>
 * <?php
 * // $feed is an ezcFeed object
 * $item = $feed->add( 'item' );
 * $module = $item->addModule( 'CreativeCommons' );
 * $module->license = 'text content';
 * ?>
 * </code>
 *
 * Parse example:
 *
 * <code>
 * <?php
 * // $item is an ezcFeedEntryElement object
 * $text = $item->CreativeCommons->license;
 * ?>
 * </code>
 *
 * @property ezcFeedElement $license
 *                          An URL to a license description. Can appear at both
 *                          feed-level and item-level. A list of possible licenses
 *                          are found here {@link http://creativecommons.org/licenses/},
 *                          but other licenses can be used as well.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedCreativeCommonsModule extends ezcFeedModule
{
    /**
     * Constructs a new ezcFeedCreativeCommonsModule object.
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
            $node = $this->add( $name );
            $node->text = $value;
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
                if ( in_array( $name, array( 'license' ) ) )
                {
                    return true;
                }
                break;

            case 'item':
                if ( in_array( $name, array( 'license' ) ) )
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
                case 'license':
                    $node = new ezcFeedTextElement();
                    break;
            }

            $this->properties[$name] = $node;
            return $node;
        }
        else
        {
            throw new ezcFeedUnsupportedElementException( $name );
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
        if ( isset( $this->license ) )
        {
            $elementTag = $xml->createElement( $this->getNamespacePrefix() . ':' . 'license' );
            $root->appendChild( $elementTag );

            $elementTag->nodeValue = $this->license->__toString();
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
        if ( $this->isElementAllowed( $name ) )
        {
            $element = $this->add( $name );
            $value = $node->textContent;

            switch ( $name )
            {
                case 'license':
                    $element->text = $value;
                    break;
            }
        }
    }

    /**
     * Returns the module name ('CreativeCommons').
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'CreativeCommons';
    }

    /**
     * Returns the namespace for this module ('http://backend.userland.com/creativeCommonsRssModule').
     *
     * @return string
     */
    public static function getNamespace()
    {
        return 'http://backend.userland.com/creativeCommonsRssModule';
    }

    /**
     * Returns the namespace prefix for this module ('creativeCommons').
     *
     * @return string
     */
    public static function getNamespacePrefix()
    {
        return 'creativeCommons';
    }
}
?>
