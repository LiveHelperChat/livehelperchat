<?php
/**
 * File containing the ezcDocumentDocbookToOdtConverterOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentToOdtConverter.
 *
 * @property string $template
 *           The base ODT template file to load for generated ODTs. The default 
 *           template resides in /data/template.fodt in the component 
 *           directory.
 * @property ezcDocumentOdtStyler $styler
 *           Styler to use for generating ODTs. By default, an {@link 
 *           ezcDocumentOdtPcssStyler} is used.
 * @property string $lengthMeasure
 *           Default length measure unit to use for measures defined in DocBook 
 *           without a unit. Valid values are: "cm", "mm", "in", "pt", "pc" and 
 *           "px".
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToOdtConverterOptions extends ezcDocumentConverterOptions
{
    /**
     * Valid length measures.
     *
     * @var array(string)
     * @access private
     */
    public static $validLengthMeasures = array(
        'cm', 'mm', 'in', 'pt', 'pc', 'px'
    );

    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->template      = dirname( __FILE__ ) . '/data/template.fodt';
        $this->styler        = new ezcDocumentOdtPcssStyler();
        $this->lengthMeasure = 'px';
        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'template':
                if ( !is_string( $value ) || !is_file( $value ) || !is_readable( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'file, readable' );
                }
                break;
            case 'styler':
                if ( !is_object( $value ) || !( $value instanceof ezcDocumentOdtStyler ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcDocumentOdtStyler' );
                }
                break;
            case 'lengthMeasure':
                if ( !is_string( $value ) || !in_array( $value, self::$validLengthMeasures ) )
                {
                    throw new ezcBaseValueException( $name, $value, implode( ', ', self::$validLengthMeasures ) );
                }
                break;
            default:
                parent::__set( $name, $value );
                break;
        }
        $this->properties[$name] = $value;
    }
}

?>
