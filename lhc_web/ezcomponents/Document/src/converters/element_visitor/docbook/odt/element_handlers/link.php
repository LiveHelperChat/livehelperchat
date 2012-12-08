<?php
/**
 * File containing the ezcDocumentDocbookToOdtLinkHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Visit links.
 *
 * Visit docbook <link/> and transform them into ODT <text:reference-ref/>.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentDocbookToOdtLinkHandler extends ezcDocumentDocbookToOdtBaseHandler
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
        $refRef = $root->appendChild(
            $root->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'text:reference-ref',
                $node->nodeValue
            )
        );
        $refRef->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:ref-name',
            $node->getAttribute( 'linked' )
        );

        return $root;
    }
}

?>
