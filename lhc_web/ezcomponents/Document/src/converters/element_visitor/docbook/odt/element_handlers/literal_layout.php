<?php
/**
 * File containing the ezcDocumentDocbookToOdtLiteralLayoutHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Visit literallayout sections.
 *
 * Visit docbook <literallayout/> paragraphs and transform them into ODT 
 * <text:p/>.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentDocbookToOdtLiteralLayoutHandler extends ezcDocumentDocbookToOdtBaseHandler
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
        $p = $root->appendChild(
            $root->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'text:p'
            )
        );
        $this->styler->applyStyles( $node, $p );

        $converter->visitChildren( $node, $p );
        return $root;
    }
}

?>
