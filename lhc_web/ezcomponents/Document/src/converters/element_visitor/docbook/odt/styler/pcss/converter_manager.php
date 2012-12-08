<?php
/**
 * File containing the ezcDocumentOdtPcssConverterManager class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Manager for ezcDocumentOdtPcssConverter instances.
 *
 * An instance of this class is used to handle style converters. It uses the 
 * {@link ArrayAccess} interface to access style converters by the name of 
 * the CSS style attribute they handle.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtPcssConverterManager extends ArrayObject
{
    /**
     * Creates a new style converter manager.
     */
    public function __construct()
    {
        parent::__construct( array(), ArrayObject::STD_PROP_LIST );
        $this->init();
    }

    /**
     * Initialize default converters.
     */
    protected function init()
    {
        $this['text-decoration']  = new ezcDocumentOdtPcssTextDecorationConverter();
        $this['font-size']        = new ezcDocumentOdtPcssFontSizeConverter();
        $this['font-name']        = new ezcDocumentOdtPcssFontNameConverter();
        $this['font-weight']      = ( $font = new ezcDocumentOdtPcssFontConverter() );
        $this['color']            = ( $color = new ezcDocumentOdtPcssColorConverter() );
        $this['background-color'] = $color;
        $this['text-align']       = ( $default = new ezcDocumentOdtDefaultPcssConverter() );
        $this['widows']           = $default;
        $this['orphans']          = $default;
        $this['text-indent']      = $default;
        $this['margin']           = new ezcDocumentOdtPcssMarginConverter();
        $this['border']           = new ezcDocumentOdtPcssBorderConverter();
        $this['break-before']     = $default;
    }

    /**
     * Sets a new style converter.
     *
     * The key must be the CSS style property this converter handles, the 
     * $value must be the style converter itself.
     * 
     * @param string $key 
     * @param ezcDocumentOdtPcssConverter $value 
     */
    public function offsetSet( $key, $value )
    {
        if ( !is_string( $key ) )
        {
            throw new ezcBaseValueException( 'key', $key, 'string' );
        }
        if ( !is_object( $value ) || !( $value instanceof ezcDocumentOdtPcssConverter ) )
        {
            throw new ezcBaseValueException(
                'value',
                $key,
                'ezcDocumentOdtPcssConverter'
            );
        }
        parent::offsetSet( $key, $value );
    }

    /**
     * Appending elements is not allowed.
     *
     * Appending a style is not allowed. Please use the array access with the 
     * style name to set a new style converter.
     * 
     * @param mixed $value 
     * @throws RuntimeException
     */
    public function append( $value )
    {
        throw new RuntimeException( 'Appending values is not allowed.' );
    }
}

?>
