<?php
/**
 * File containing the ezcDocumentDocbookToEzXmlAnchorHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit anchor elements.
 *
 * Anchor elements are manually added targets inside paragraphs, which are
 * transformed to HTML <a> element targets.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToEzXmlAnchorHandler extends ezcDocumentElementVisitorHandler
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
        $link = $root->ownerDocument->createElement( 'anchor' );
        $link->setAttribute( 'name', $node->getAttribute( 'ID' ) );
        $root->appendChild( $link );
        $converter->visitChildren( $node, $link );
        return $root;
    }
}

?>
