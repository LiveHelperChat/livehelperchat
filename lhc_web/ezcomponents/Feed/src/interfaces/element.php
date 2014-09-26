<?php
/**
 * File containing the ezcFeedElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Base container for element data.
 *
 * @property string $language
 *                  The language of a feed element. Mainly used by ATOM elements
 *                  with the type ezcFeedTextElement (title, description, content,
 *                  copyright, etc). It is inherited in all feed elements.
 *
 * @package Feed
 * @version 1.3
 * @mainclass
 */
class ezcFeedElement
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     *
     * @param string $name The property name
     * @param mixed $value The property value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'language':
                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     *
     * @param string $name The property name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'language':
                if ( isset( $this->properties[$name] ) )
                {
                    return $this->properties[$name];
                }
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns whether the property $name is set.
     *
     * @param string $name The property name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'language':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }
}
?>
