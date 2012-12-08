<?php
/**
 * File containing the ezcDocumentDocbookToRstCitationHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit citations
 *
 * Citations in docbook are emebedded at the position, the reference should
 * occur. We store the contents, to be rendered at the end of the RST
 * document, and only render a number referencing the actual citation at the
 * position of the citation in the docbook document.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToRstCitationHandler extends ezcDocumentDocbookToRstBaseHandler
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
        $converter->setSkipPostDecoration( true );
        $citationContent = trim( $converter->visitChildren( $node, '' ) );
        $converter->setSkipPostDecoration( false );
        $number = $converter->appendCitation( $citationContent );

        // Add autonumbered citation reference
        $root .= sprintf( '[CIT%03d]_ ', $number );

        return $root;
    }
}

?>
