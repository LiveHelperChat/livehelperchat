<?php
/**
 * File containing the ezcDocumentDocbookToOdtListHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Visit lists.
 *
 * Visit docbook <orderedlist/> and <itemizedlist/> and transform them into ODT
 * <text:list/>. Note that distinguishing between ordered and itemized lists 
 * happens in the styles in ODT.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentDocbookToOdtListHandler extends ezcDocumentDocbookToOdtBaseHandler
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
        $list = $root->ownerDocument->createElementNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:list'
        );
        $root->appendChild( $list );

        $this->styler->applyStyles( $node, $list );

        $converter->visitChildren( $node, $list );
        return $root;
    }
}

?>
