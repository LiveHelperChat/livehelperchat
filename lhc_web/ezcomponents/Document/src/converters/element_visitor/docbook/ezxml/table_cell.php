<?php
/**
 * File containing the ezcDocumentDocbookToEzXmlTableCellHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit table cells
 *
 * Table cells are quite trivial to transform, but some attributes need to
 * be converted, like rowspan.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToEzXmlTableCellHandler extends ezcDocumentElementVisitorHandler
{
    /**
     * Handle a node
     *
     * Handle / transform a given node, and return the result of the
     * conversion.
     *
     * @param ezcDocumentElementVisitorConverter $converter
     * @param DOMElement $node
     * @param mixed $root
     * @return mixed
     */
    public function handle( ezcDocumentElementVisitorConverter $converter, DOMElement $node, $root )
    {
        $isHeader = ( $node->parentNode->parentNode->tagName === 'thead' );
        $cell = $root->ownerDocument->createElement( $isHeader ? 'th' : 'td' );

        if ( $node->hasAttribute( 'morerows' ) )
        {
            $cell->setAttribute( 'rowspan', $node->getAttribute( 'morerows' ) + 1 );
        }

        $root->appendChild( $cell );
        $converter->visitChildren( $node, $cell );
        return $root;
    }
}

?>
