<?php
/**
 * File containing the ezcDocumentOdtListLevelStyleNumber class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Number list-level style.
 *
 * @property-read int $level
 *                The list level, starting with 1.
 * @property ezcDocumentOdtStyle|null $textStyle
 *           Text style for list bullet / number formatting.
 * @property string $numFormat
 *           Format for the list numbering.
 * @property int $displayLevels
 *           Numbers of levels to display in numbering.
 * @property int $startValue
 *           Value to start numbering with.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtListLevelStyleNumber extends ezcDocumentOdtListLevelStyle
{
    /**
     * Properties
     * 
     * @var array(string=>mixed)
     */
    private $properties = array(
        'numFormat'     => null,
        'displayLevels' => 1,
        'startValue'    => 1,
    );

    /**
     * Creates a new list-level style.
     * 
     * @param int $level 
     */
    public function __construct( $level )
    {
        parent::__construct( $level );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'numFormat':
                if ( !is_string( $value ) && $value !== null )
                {
                    throw new ezcBaseValueException( $name, $value, 'string or null' );
                }
                break;
            case 'displayLevels':
            case 'startValue':
                if ( !is_int( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'int' );
                }
                break;
            default:
                return parent::__set( $name, $value );
        }
        $this->properties[$name] = $value;
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        if ( array_key_exists( $name, $this->properties ) )
        {
            return $this->properties[$name];
        }
        return parent::__get( $name );
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name     
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        return array_key_exists( $name, $this->properties ) || parent::__isset( $name );
    }
}

?>
