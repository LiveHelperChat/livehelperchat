<?php
/**
 * File containing the ezcDocumentDocbookToWikiExternalLinkHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit external links.
 *
 * Transform external docbook links (<ulink>) to common HTML links.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToWikiExternalLinkHandler extends ezcDocumentDocbookToWikiBaseHandler
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
        $root .= ' [[' . $node->getAttribute( 'url' ) . '|' . $converter->visitChildren( $node, '' ) . ']]';
        return $root;
    }
}

?>
