<?php
/**
 * File containing the ezcDocumentDocbookToOdtInlineHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Visit inline elements.
 *
 * Visit docbook inline elements and transform them into ODT <text:span/>.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentDocbookToOdtInlineHandler extends ezcDocumentDocbookToOdtBaseHandler
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
        $inline = $root->ownerDocument->createElementNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:span'
        );
        $root->appendChild( $inline );

        $this->styler->applyStyles( $node, $inline );

        $converter->visitChildren( $node, $inline );
        return $root;
    }
}

?>
