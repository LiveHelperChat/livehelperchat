<?php
/**
 * File containing the ezcDocumentDocbookToEzXmlInternalLinkHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit internal links.
 *
 * Internal links are transformed into local links in HTML, where the name
 * of the target is prefixed with a number sign.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToEzXmlInternalLinkHandler extends ezcDocumentElementVisitorHandler
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
        $link = $root->ownerDocument->createElement( 'link' );
        $link->setAttribute( 'anchor_name', $node->getAttribute( 'linked' ) );
        $root->appendChild( $link );
        $converter->visitChildren( $node, $link );
        return $root;
    }
}

?>
