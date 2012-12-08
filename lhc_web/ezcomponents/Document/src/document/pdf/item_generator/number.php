<?php
/**
 * File containing the ezcDocumentNumberedListItemGenerator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Numbered list item generator.
 *
 * Generator for list items using common arabic numbers. Just returns the 
 * number of the given list item as a string.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentNumberedListItemGenerator extends ezcDocumentListItemGenerator
{
    /**
     * Get list item.
     *
     * Get the n-th list item. The index of the list item is specified by the
     * number parameter.
     * 
     * @param int $number 
     * @return string
     */
    public function getListItem( $number )
    {
        return (string) $number;
    }
}

?>
