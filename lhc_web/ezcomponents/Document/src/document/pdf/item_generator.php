<?php
/**
 * File containing the ezcDocumentListItemGenerator class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * List item generator
 *
 * Generator for list items, like bullet list items, and more important,
 * enumerated lists.
 *
 * Intended to return a list item, which is most likely a single character, 
 * based on the passed number. The list item generator implementation is 
 * choosen in the list renderer, depending on the properties of the element to 
 * render.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
abstract class ezcDocumentListItemGenerator
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
    abstract public function getListItem( $number );
}

?>
