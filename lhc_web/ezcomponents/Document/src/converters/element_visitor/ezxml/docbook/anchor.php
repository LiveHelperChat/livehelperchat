<?php
/**
 * File containing the ezcDocumentEzXmlToDocbookAnchorHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit anchor elements.
 *
 * Anchor elements are manually added targets inside paragraphs.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentEzXmlToDocbookAnchorHandler extends ezcDocumentElementVisitorHandler
{
    /**
     * Handle a node.
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
        $anchor = $root->ownerDocument->createElement( 'anchor' );
        $anchor->setAttribute( 'ID', $node->getAttribute( 'name' ) );
        $root->appendChild( $anchor );

        $converter->visitChildren( $node, $anchor );
        return $root;
    }
}

?>
