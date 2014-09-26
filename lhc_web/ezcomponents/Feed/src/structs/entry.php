<?php
/**
 * File containing the ezcFeedEntryElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a feed entry.
 *
 * @property array(ezcFeedPersonElement) $author
 *           The authors of the entry. Equivalents:
 *           ATOM-author (required, multiple),
 *           RSS1-none,
 *           RSS2-author (optional, recommended, single).
 * @property array(ezcFeedCategoryElement) $category
 *           The categories of the entry. Equivalents:
 *           ATOM-author (optional, multiple),
 *           RSS1-none,
 *           RSS2-category (optional, multiple).
 * @property ezcFeedTextElement $comments
 *           The comments of the entry. Equivalents:
 *           ATOM-none,
 *           RSS1-none,
 *           RSS2-author (optional, single).
 * @property ezcFeedContentElement $content
 *           The complex text content of the entry. Equivalents:
 *           ATOM-content (optional, single),
 *           RSS1-none,
 *           RSS2-none.
 * @property array(ezcFeedPersonElement) $contributor
 *           The contributors of the entry. Equivalents:
 *           ATOM-contributor (optional, not recommended, multiple),
 *           RSS1-none,
 *           RSS2-none.
 * @property ezcFeedTextElement $copyright
 *           The copyright of the entry. Equivalents:
 *           ATOM-rights (optional, single),
 *           RSS1-none,
 *           RSS2-none.
 * @property ezcFeedTextElement $description
 *           The description of the entry. Equivalents:
 *           ATOM-summary (required, single),
 *           RSS1-description (required, single),
 *           RSS2-description (required, single).
 * @property array(ezcFeedEnclosureElement) $enclosure
 *           The enclosures of the entry. Equivalents:
 *           ATOM-link@rel="enclosure" (optional, multiple),
 *           RSS1-none,
 *           RSS2-enclosure (optional, single).
 * @property ezcFeedTextElement $id
 *           The id of the entry. Equivalents:
 *           ATOM-id (required, single),
 *           RSS1-about (required, single),
 *           RSS2-guid (optional, single).
 * @property array(ezcFeedLinkElement) $link
 *           The links of the entry. Equivalents:
 *           ATOM-link (required, multiple),
 *           RSS1-link (required, single),
 *           RSS2-link (required, single).
 * @property ezcFeedDateElement $published
 *           The published date of the entry. Equivalents:
 *           ATOM-published (optional, single),
 *           RSS1-none,
 *           RSS2-pubDate (optional, single).
 * @property ezcFeedTextElement $title
 *           The title of the entry. Equivalents:
 *           ATOM-title (required, single),
 *           RSS1-title (required, single),
 *           RSS2-title (required, single).
 * @property ezcFeedDateElement $updated
 *           The updated date of the entry. Equivalents:
 *           ATOM-updated (required, single),
 *           RSS1-none,
 *           RSS2-none.
 * @property ezcFeedSourceElement $source
 *           The source of the entry. Equivalents:
 *           ATOM-source (optional, not recommended, single),
 *           RSS1-none,
 *           RSS2-source (optional, not recommended, single).
 * @property ezcFeedTextElement $language
 *           The language of the entry. Equivalents:
 *           ATOM-source (optional, not recommended, single ),
 *           RSS1-none,
 *           RSS2-none.
*
 * @package Feed
 * @version 1.3
 * @mainclass
 */
class ezcFeedEntryElement extends ezcFeedElement
{
    /**
     * Holds the modules used by this feed item.
     *
     * @var array(string=>ezcFeedModule)
     */
    private $modules = array();

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
            case 'title':
            case 'description':
            case 'comments':
            case 'copyright':
            case 'language':
                $element = $this->add( $name );
                $element->text = $value;
                break;

            case 'content':
                $element = $this->add( $name );
                $element->text = $value;
                break;

            case 'author':
            case 'contributor':
                $element = $this->add( $name );
                $element->name = $value;
                break;

            case 'updated':
            case 'published':
                $element = $this->add( $name );
                $element->date = $value;
                break;

            case 'id':
                $element = $this->add( $name );
                $element->id = $value;
                break;

            case 'link':
                $element = $this->add( $name );
                $element->href = $value;
                break;

            case 'enclosure':
                $element = $this->add( $name );
                $element->url = $value;
                break;

            case 'source':
                $element = $this->add( $name );
                $element->source = $value;
                break;

            default:
                $supportedModules = ezcFeed::getSupportedModules();
                if ( isset( $supportedModules[$name] ) )
                {
                    $this->modules[$name] = $value;
                }
                break;
        }
    }

    /**
     * Returns the value of property $name.
     *
     * @throws ezcFeedUndefinedModuleException
     *         if trying to fetch a module not defined yet
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
            case 'comments':
            case 'content':
            case 'contributor':
            case 'copyright':
            case 'description':
            case 'enclosure':
            case 'id':
            case 'link':
            case 'published':
            case 'title':
            case 'updated':
            case 'source':
            case 'language':
                if ( isset( $this->properties[$name] ) )
                {
                    return $this->properties[$name];
                }
                break;

            default:
                $supportedModules = ezcFeed::getSupportedModules();
                if ( isset( $supportedModules[$name] ) )
                {
                    if ( isset( $this->$name ) )
                    {
                        return $this->modules[$name];
                    }
                    else
                    {
                        throw new ezcFeedUndefinedModuleException( $name );
                    }
                }
                break;
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
            case 'comments':
            case 'content':
            case 'contributor':
            case 'copyright':
            case 'description':
            case 'enclosure':
            case 'id':
            case 'link':
            case 'published':
            case 'title':
            case 'updated':
            case 'source':
            case 'language':
                return isset( $this->properties[$name] );

            default:
                $supportedModules = ezcFeed::getSupportedModules();
                if ( isset( $supportedModules[$name] ) )
                {
                    return isset( $this->modules[$name] );
                }
        }
    }

    /**
     * Adds a new element with name $name to the feed item and returns it.
     *
     * Example:
     * <code>
     * // $item is an ezcFeedEntryElement object
     * $link = $item->add( 'link' );
     * $link->href = 'http://ez.no/';
     * </code>
     *
     * @throws ezcFeedUnsupportedElementException
     *         if the element $name is not supported
     *
     * @apichange All items are not encoded at all, in future versions this
     *            should be done in one of the ways as described in
     *            http://issues.ez.no/14093
     *
     * @param string $name The name of the element to add
     * @return ezcFeedElement
     */
    public function add( $name )
    {
        switch ( $name )
        {
            case 'author':
            case 'contributor':
                $element = new ezcFeedPersonElement();
                $this->properties[$name][] = $element;
                break;

            case 'id':
                $element = new ezcFeedIdElement();
                $this->properties[$name] = $element;
                break;

            case 'category':
                $element = new ezcFeedCategoryElement();
                $this->properties[$name][] = $element;
                break;

            case 'title':
            case 'description':
            case 'comments':
            case 'copyright':
            case 'language':
                $element = new ezcFeedTextElement();
                $this->properties[$name] = $element;
                break;

            case 'content':
                $element = new ezcFeedContentElement();
                $this->properties[$name] = $element;
                break;

            case 'updated':
            case 'published':
                $element = new ezcFeedDateElement();
                $this->properties[$name] = $element;
                break;

            case 'link':
                $element = new ezcFeedLinkElement();
                $this->properties[$name][] = $element;
                break;

            case 'enclosure':
                $element = new ezcFeedEnclosureElement();
                $this->properties[$name][] = $element;
                break;

            case 'source':
                $element = new ezcFeedSourceElement();
                $this->properties[$name] = $element;
                break;

            default:
                throw new ezcFeedUnsupportedElementException( $name );
        }

        return $element;
    }

    /**
     * Adds a new module to this item and returns it.
     *
     * @param string $name The name of the module to add
     * @return ezcFeedModule
     */
    public function addModule( $name )
    {
        $this->$name = ezcFeedModule::create( $name, 'item' );
        return $this->$name;
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
     * Returns an array with all the modules defined for this feed item.
     *
     * @return array(ezcFeedModule)
     */
    public function getModules()
    {
        return $this->modules;
    }
}
?>
