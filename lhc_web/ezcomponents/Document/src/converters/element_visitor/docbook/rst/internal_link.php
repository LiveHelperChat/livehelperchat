<?php
/**
 * File containing the ezcDocumentDocbookToRstInternalLinkHandler class.
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
class ezcDocumentDocbookToRstInternalLinkHandler extends ezcDocumentDocbookToRstBaseHandler
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
        $root .= ' `' . $converter->visitChildren( $node, '' ) . '`__';
        $converter->appendLink( $node->getAttribute( 'linked' ) . '_' );
        return $root;
    }
}

?>
