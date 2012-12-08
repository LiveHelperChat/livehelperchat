<?php
/**
 * File containing the ezcDocumentDocbookToOdtPageBreakHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Visit page-breaks.
 *
 * Visit docbook <beginpage/> and transform them into ODT <text:soft-page-break/>.
 *
 * Note that OpenOffice.org does not pay attention to these page-break 
 * information, but expects page-breaks to be encoded in styles. Therefore, 
 * additional page-break handling happens in {@link 
 * ezcDocumentOdtPcssParagraphStylePreprocessor}.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentDocbookToOdtPageBreakHandler extends ezcDocumentDocbookToOdtBaseHandler
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
        $pageBreak = $root->appendChild(
            $root->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'text:soft-page-break'
            )
        );

        return $root;
    }
}

?>
