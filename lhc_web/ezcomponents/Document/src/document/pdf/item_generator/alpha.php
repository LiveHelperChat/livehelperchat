<?php
/**
 * File containing the ezcDocumentAlphaListItemGenerator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Numbered list item generator
 *
 * Generator for alphabetical list items. Generated list items start with "a" 
 * to "z" and will use more characters for lists with more then 26 list items, 
 * like "ab" for the 28th list item.
 *
 * Basically implements a number recoding to base 26, only using alphabetical 
 * characters.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentAlphaListItemGenerator extends ezcDocumentAlnumListItemGenerator
{
    /**
     * Get list item
     *
     * Get the n-th list item. The index of the list item is specified by the
     * number parameter.
     * 
     * @param int $number 
     * @return string
     */
    public function getListItem( $number )
    {
        $item = '';
        while ( $number > 0 )
        {
            $item   = chr( $number % 26 + ord( 'a' ) - 1 ) . $item;
            $number = floor( $number / 26 );
        }

        return $this->applyStyle( $item );
    }
}

?>
