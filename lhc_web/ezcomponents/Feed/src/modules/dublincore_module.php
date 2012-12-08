<?php
/**
 * File containing the ezcFeedDublinCoreModule class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Support for the DublinCore module: data container, generator, parser.
 *
 * Specifications: {@link http://dublincore.org/documents/dces/}.
 *
 * Each DublinCore property can appear multiple times both on the feed-level
 * or the item-level.
 *
 * Each DublinCore property can have the language attribute, which appears in
 * the generated XML file as a 'xml:lang' attribute.
 *
 * Create example:
 *
 * <code>
 * <?php
 * // $feed is an ezcFeed object
 * $item = $feed->add( 'item' );
 * $module = $item->addModule( 'DublinCore' );
 * $creator = $module->add( 'creator' );
 * $creator->name = 'Creator name';
 * $creator->language = 'en'; // optional
 * // more elements of the same type can be added
 * ?>
 * </code>
 *
 * Parse example:
 *
 * <code>
 * <?php
 * // $item is an ezcFeedEntryElement object
 * foreach ( $item->DublinCore->creator as $creator )
 * {
 *     echo $creator->name;
 *     echo $creator->language;
 * }
 * ?>
 * </code>
 *
 * @property array(ezcFeedPersonElement) $contributor
 *                                       An entity responsible for making contributions to
 *                                       the resource.
 *                                       Usually the name of a person, organization or service.
 * @property array(ezcFeedTextElement) $coverage
 *                                     The spatial or temporal topic of the resource, the
 *                                     spatial applicability of the resource, or the
 *                                     jurisdiction under which the resource is relevant.
 *                                     A recommended practice is to use a controlled
 *                                     vocabulary such as
 *                                     {@link http://www.getty.edu/research/tools/vocabulary/tgn/index.html TGN}.
 * @property array(ezcFeedPersonElement) $creator
 *                                       An entity responsible for making the resource.
 *                                       Usually the name of a person or organization.
 * @property array(ezcFeedDateElement) $date
 *                                     A point or period of time associated with an event
 *                                     in the lifecycle of the resource. It is a Unix
 *                                     timestamp, which will be converted to an
 *                                     {@link http://www.w3.org/TR/NOTE-datetime ISO 8601}
 *                                     date when generating the feed.
 * @property array(ezcFeedTextElement) $description
 *                                     A description of the resource.
 * @property array(ezcFeedTextElement) $format
 *                                     The file format, physical medium, or dimensions of
 *                                     the resource.
 *                                     Recommended best practices is to use a controlled
 *                                     vocabulary such as the list of
 *                                     {@link http://www.iana.org/assignments/media-types/ Internet Media Types}
 *                                     (MIME).
 * @property array(ezcFeedIdElement) $identifier
 *                                   An unambiguous reference to the resource within a
 *                                   given context.
 * @property array(ezcFeedTextElement) $language
 *                                     A language of the resource.
 *                                     Recommended best practice is to use a controlled
 *                                     vocabulary such as
 *                                     {@link http://www.faqs.org/rfcs/rfc4646.html RFC 4646}.
 * @property array(ezcFeedPersonElement) $publisher
 *                                       An entity responsible for making the resource available.
 *                                       Usually the name of a person, organization or service.
 * @property array(ezcFeedTextElement) $relation
 *                                     A related resource.
 * @property array(ezcFeedTextElement) $rights
 *                                     Information about rights held in and over the resource.
 * @property array(ezcFeedSourceElement) $source
 *                                       A related resource from which the described resource
 *                                       is derived.
 * @property array(ezcFeedTextElement) $subject
 *                                     The topic of the resource.
 * @property array(ezcFeedTextElement) $title
 *                                     The name given to the resource.
 * @property array(ezcFeedTextElement) $type
 *                                     The nature or genre of the resource.
 *                                     Recommended best practice is to use a controlled
 *                                     vocabulary such as the
 *                                     {@link http://dublincore.org/documents/dcmi-type-vocabulary/ DCMI Type Vocabulary}
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedDublinCoreModule extends ezcFeedModule
{
    /**
     * Constructs a new ezcFeedDublinCoreModule object.
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
                case 'date':
                    $node = $this->add( $name );
                    $node->date = $value;
                    break;

                case 'contributor':
                case 'creator':
                case 'publisher':
                    $node = $this->add( $name );
                    $node->name = $value;
                    break;

                case 'identifier':
                    $node = $this->add( $name );
                    $node->id = $value;
                    break;

                case 'source':
                    $node = $this->add( $name );
                    $node->source = $value;
                    break;

                default:
                    $node = new ezcFeedTextElement();
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
                if ( in_array( $name, array( 'contributor', 'coverage', 'creator',
                                             'date', 'description', 'format',
                                             'identifier', 'language', 'publisher',
                                             'relation', 'rights', 'source',
                                             'subject', 'title', 'type' ) ) )
                {
                    return true;
                }
                break;

            case 'item':
                if ( in_array( $name, array( 'contributor', 'coverage', 'creator',
                                             'date', 'description', 'format',
                                             'identifier', 'language', 'publisher',
                                             'relation', 'rights', 'source',
                                             'subject', 'title', 'type' ) ) )
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
                case 'date':
                    $node = new ezcFeedDateElement();
                    break;

                case 'contributor':
                case 'creator':
                case 'publisher':
                    $node = new ezcFeedPersonElement();
                    break;

                case 'identifier':
                    $node = new ezcFeedIdElement();
                    break;

                case 'source':
                    $node = new ezcFeedSourceElement();
                    break;

                default:
                    $node = new ezcFeedTextElement();
                    break;
            }

            $this->properties[$name][] = $node;
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
        $elements = array( 'contributor', 'coverage', 'creator',
                           'date', 'description', 'format',
                           'identifier', 'language', 'publisher',
                           'relation', 'rights', 'source',
                           'subject', 'title', 'type');

        foreach ( $elements as $element )
        {
            if ( isset( $this->$element ) )
            {
                foreach ( $this->$element as $values )
                {
                    $elementTag = $xml->createElement( $this->getNamespacePrefix() . ':' . $element );
                    $root->appendChild( $elementTag );

                    switch ( $element )
                    {
                        case 'date':
                            $elementTag->nodeValue = $values->date->format( 'c' );
                            break;

                        default:
                            $elementTag->nodeValue = $values->__toString();
                            break;
                    }

                    if ( isset( $values->language ) )
                    {
                        $this->addAttribute( $xml, $elementTag, 'xml:lang', $values->language );
                    }
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
        if ( $this->isElementAllowed( $name ) )
        {
            $element = $this->add( $name );
            $value = $node->textContent;

            switch ( $name )
            {
                case 'date':
                    $element->date = $value;
                    break;

                case 'contributor':
                case 'creator':
                case 'publisher':
                    $element->name = $value;
                    break;

                case 'identifier':
                    $element->id = $value;
                    break;

                case 'source':
                    $element->source = $value;
                    break;

                default:
                    $element->text = $value;
            }

            if ( $node->hasAttributes() )
            {
                foreach ( $node->attributes as $attribute )
                {
                    switch ( $attribute->name )
                    {
                        case 'lang':
                            $element->language = $attribute->value;
                            break;
                    }
                }
            }
        }
    }

    /**
     * Returns the module name ('DublinCore').
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'DublinCore';
    }

    /**
     * Returns the namespace for this module ('http://purl.org/dc/elements/1.1/').
     *
     * @return string
     */
    public static function getNamespace()
    {
        return 'http://purl.org/dc/elements/1.1/';
    }

    /**
     * Returns the namespace prefix for this module ('dc').
     *
     * @return string
     */
    public static function getNamespacePrefix()
    {
        return 'dc';
    }
}
?>
