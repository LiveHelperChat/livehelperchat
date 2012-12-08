<?php
/**
 * File containing the ezcDocumentDocbookToOdtUlinkHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Visit ulinks.
 *
 * Visit docbook <ulink/> and transform them into ODT <text:a/>.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentDocbookToOdtUlinkHandler extends ezcDocumentDocbookToOdtBaseHandler
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
        $a = $root->appendChild(
            $root->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'text:a'
            )
        );
        $a->setAttributeNS(
            ezcDocumentOdt::NS_XLINK,
            'xlink:type',
            'simple'
        );
        $a->setAttributeNS(
            ezcDocumentOdt::NS_XLINK,
            'xlink:href',
            $node->getAttribute( 'url' )
        );

        $this->styler->applyStyles( $node, $a );

        $converter->visitChildren( $node, $a );
        return $root;
    }
}

?>
