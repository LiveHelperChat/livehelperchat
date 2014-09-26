<?php
/**
 * File containing the ezcFeedSkipDaysElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a skipDays element.
 *
 * @property array(string) $days
 *                         Which days to skip when retrieving a feed, for example
 *                         array('Saturday', 'Sunday').
 * @package Feed
 * @version 1.3
 */
class ezcFeedSkipDaysElement extends ezcFeedElement
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
            case 'days':
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
            case 'days':
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
