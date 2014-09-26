<?php
/**
 * File containing the ezcFeedDateElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a date element.
 *
 * @property DateTime $date
 *                    The date stored as a DateTime object. An integer timestamp
 *                    or a formatted string date can be assigned to the $date
 *                    property, and it will be converted to a DateTime object.
 *                    If the conversion was not successful, the current date
 *                    is assigned to the property.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedDateElement extends ezcFeedElement
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
            case 'date':
                $this->properties[$name] = $this->prepareDate( $value );
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
            case 'date':
                if ( isset( $this->properties[$name] ) )
                {
                    return $this->properties[$name];
                }
                break;

            default:
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
        switch ( $name )
        {
            case 'date':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Returns the provided $date (timestamp, string or DateTime object) as a
     * DateTime object.
     *
     * It preserves the timezone if $date contained timezone information.
     *
     * @param mixed $date A date specified as a timestamp, string or DateTime object
     * @return DateTime
     */
    private function prepareDate( $date )
    {
        if ( is_numeric( $date ) )
        {
            return new DateTime( "@{$date}" );
        }
        else if ( $date instanceof DateTime )
        {
            return $date;
        }
        else
        {
            try
            {
                $d = new DateTime( $date );
            }
            catch ( Exception $e )
            {
                return new DateTime();
            }

            return $d;
        }
    }
}
?>
