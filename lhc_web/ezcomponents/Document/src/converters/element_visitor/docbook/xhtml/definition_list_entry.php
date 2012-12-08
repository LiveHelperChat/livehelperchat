<?php
/**
 * File containing the ezcDocumentDocbookToHtmlDefinitionListEntryHandler 
 * class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit definition list entries
 *
 * Definition list entries are encapsulated in docbook, while the HTML
 * variant only consists of a list of terms and their description. This
 * method transforms the elements accordingly.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToHtmlDefinitionListEntryHandler extends ezcDocumentDocbookToHtmlBaseHandler
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
        foreach ( $node->childNodes as $child )
        {
            if ( ( $child->nodeType === XML_ELEMENT_NODE ) &&
                 ( ( $child->tagName === 'term' ) ||
                   ( $child->tagName === 'listitem' ) ) )
            {
                $entry = $root->ownerDocument->createElement( $child->tagName === 'term' ? 'dt' : 'dd' );
                $root->appendChild( $entry );
                $converter->visitChildren( $child, $entry );
            }
        }

        return $root;
    }
}

?>
