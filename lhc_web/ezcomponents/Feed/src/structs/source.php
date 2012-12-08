<?php
/**
 * File containing the ezcFeedSourceElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a feed source element.
 *
 * @property string $source
 *           The name of the source feed (RSS2 only).
 * @property string $url
 *           The url of the source feed (RSS2 only).
 * @property array(ezcFeedAuthorElement) $author
 *           The authors from the source feed (ATOM only).
 * @property ezcFeedCategoryElement $category
 *           The categories from the source feed (ATOM only).
 * @property array(ezcFeedAuthorElement) $contributor
 *           The contributors from the source feed (ATOM only).
 * @property ezcFeedTextElement $copyright
 *           The copyright information from the source feed (ATOM only).
 * @property ezcFeedTextElement $description
 *           The description from the source feed (ATOM only).
 * @property ezcFeedGeneratorElement $generator
 *           The generator information from the source feed (ATOM only).
 * @property ezcFeedImageElement $icon
 *           The icon from the source feed (ATOM only).
 * @property ezcFeedIdElement $id
 *           The id from the source feed (ATOM only).
 * @property ezcFeedImageElement $image
 *           The image from the source feed (ATOM only).
 * @property array(ezcFeedLinkElement) $link
 *           The links from the source feed (ATOM only).
 * @property ezcFeedTextElement $title
 *           The title from the source feed (ATOM only).
 * @property ezcFeedDateElement $updated
 *           The date and time of the last update from the source feed (ATOM only).
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedSourceElement extends ezcFeedElement
{
    /**
     * The name of the source feed (RSS2 only).
     *
     * @var string
     */
    public $source;

    /**
     * The url of the source feed (RSS2 only).
     *
     * @var string
     */
    public $url;

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
            case 'copyright':
                $element = new ezcFeedTextElement();
                $element->text = $value;
                $this->properties[$name] = $element;
                break;

            case 'author':
            case 'contributor':
                $element = new ezcFeedPersonElement();
                $element->name = $value;
                $this->properties[$name] = $element;
                break;

            case 'updated':
                $element = new ezcFeedDateElement();
                $element->date = $value;
                $this->properties[$name] = $element;
                break;

            case 'generator':
                $element = new ezcFeedGeneratorElement();
                $element->name = $value;
                $this->properties[$name] = $element;
                break;

            case 'image':
            case 'icon':
                $element = new ezcFeedImageElement();
                $element->link = $value;
                $this->properties[$name] = $element;
                break;

            case 'id':
                $element = new ezcFeedIdElement();
                $element->id = $value;
                $this->properties[$name] = $element;
                break;

            case 'link':
                $element = new ezcFeedLinkElement();
                $element->href = $value;
                $this->properties[$name] = $element;
                break;

            case 'category':
                $element = new ezcFeedCategoryElement();
                $element->term = $value;
                $this->properties[$name] = $element;
                break;

            default:
                parent::__set( $name, $value );
                break;
        }
    }

    /**
     * Returns the value of property $name.
     *
     * @param string $name The property name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'title':
            case 'description':
            case 'copyright':
            case 'author':
            case 'contributor':
            case 'updated':
            case 'generator':
            case 'image':
            case 'icon':
            case 'id':
            case 'link':
            case 'category':
                if ( isset( $this->properties[$name] ) )
                {
                    return $this->properties[$name];
                }
                break;

            default:
                return parent::__get( $name );
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
            case 'title':
            case 'description':
            case 'copyright':
            case 'author':
            case 'contributor':
            case 'updated':
            case 'generator':
            case 'image':
            case 'icon':
            case 'id':
            case 'link':
            case 'category':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Adds a new element with name $name to the source element and returns it.
     *
     * Example:
     * <code>
     * // $source is an ezcFeedSourceElement object
     * $link = $source->add( 'link' );
     * $link->href = 'http://ez.no/';
     * </code>
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
            case 'copyright':
                $element = new ezcFeedTextElement();
                $this->properties[$name] = $element;
                break;

            case 'generator':
                $element = new ezcFeedGeneratorElement();
                $this->properties[$name] = $element;
                break;

            case 'image':
            case 'icon':
                $element = new ezcFeedImageElement();
                $this->properties[$name] = $element;
                break;

            case 'updated':
                $element = new ezcFeedDateElement();
                $this->properties[$name] = $element;
                break;

            case 'link':
                $element = new ezcFeedLinkElement();
                $this->properties[$name][] = $element;
                break;

            default:
                throw new ezcFeedUnsupportedElementException( $name );
        }

        return $element;
    }

    /**
     * Returns the source attribute.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->source . '';
    }
}
?>
