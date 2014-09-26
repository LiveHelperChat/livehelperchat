<?php
/**
 * File containing the ezcFeedSkipHoursElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a skipHours element.
 *
 * @property array(int) $hours
 *                      Which hours to skip when retrieving a feed, for example
 *                      array(1,4,23). The values 0 and 24 can be used for midnight.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedSkipHoursElement extends ezcFeedElement
{
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
            case 'hours':
                if ( !is_array( $value ) )
                {
                    $value = array( $value );
                }

                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
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
            case 'hours':
                if ( isset( $this->properties[$name] ) )
                {
                    return $this->properties[$name];
                }
                break;

            default:
                return parent::__get( $name );
        }
    }
}
?>
