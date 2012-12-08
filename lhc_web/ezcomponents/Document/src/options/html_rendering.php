<?php
/**
 * File containing the ezcDocumentHtmlConverterOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentEzp3Xml class
 *
 * @property bool $dublinCoreMetadata
 *           Use the dublincore meta element names for metadata in HTML.
 * @property bool $formatOutput
 *           Indent the XHtml output
 * @property array $styleSheets
 *           Array of stylesheet URLs to embed in the HTML header, if there is
 *           one.
 * @property string $styleSheet
 *           Stylesheet to embed in the HTML header, if the property
 *           $stylesheets has not been set. This property contains the default
 *           stylesheet for HTML output.
 * @property string $headerLevel
 *           Header level to start with. Is only used by inline HTML renderer.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentHtmlConverterOptions extends ezcDocumentConverterOptions
{
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
        $this->dublinCoreMetadata = false;
        $this->formatOutput       = false;
        $this->styleSheets        = null;
        $this->styleSheet         = file_get_contents( dirname( __FILE__ ) . '/data/html_style.css' );
        $this->headerLevel        = 1;

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
            case 'formatOutput':
            case 'dublinCoreMetadata':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'boolean' );
                }

                $this->properties[$name] = $value;
                break;

            case 'styleSheets':
                if ( !is_array( $value ) &&
                     !is_null( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'null OR array( URL )' );
                }

                $this->properties[$name] = $value;
                break;

            case 'styleSheet':
                $this->properties[$name] = (string) $value;
                break;

            case 'headerLevel':
                if ( !is_int( $value ) ||
                     ( $value > 6 ) ||
                     ( $value < 0 ) )
                {
                    throw new ezcBaseValueException( $name, $value, '0 < int < 7' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}

?>
