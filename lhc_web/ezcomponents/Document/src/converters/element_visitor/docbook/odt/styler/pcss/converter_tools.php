<?php
/**
 * File containing the ezcDocumentOdtPcssConverterTools class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Tool class for ODT PCSS styles.
 *
 * Contains static helper functions which are used in multiple style converters.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtPcssConverterTools
{
    /**
     * Serializes a color value.
     * 
     * @param array $colorValue 
     * @return string
     */
    public static function serializeColor( array $colorValue )
    {
        if ( $colorValue['alpha'] >= .5 )
        {
            return 'transparent';
        }
        else
        {
            return sprintf(
                '#%02x%02x%02x',
                round( $colorValue['red'] * 255 ),
                round( $colorValue['green'] * 255 ),
                round( $colorValue['blue'] * 255 )
            );
        }
    }
}

?>
