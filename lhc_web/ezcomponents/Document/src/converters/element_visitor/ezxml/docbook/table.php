<?php
/**
 * File containing the ezcDocumentEzXmlToDocbookTableHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit eZXml table.
 *
 * Visit tables, which are quite similar to HTML tables and transform to
 * classic Docbook tables.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentEzXmlToDocbookTableHandler extends ezcDocumentElementVisitorHandler
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
        $element = $root->ownerDocument->createElement( 'table' );
        $root->appendChild( $element );

        // Handle attributes

        // Recurse
        $converter->visitChildren( $node, $element );
        return $root;
    }
}

?>
