<?php
/**
 * File containing the ezcDocumentDocbookToHtmlSpecialParagraphHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit special paragraphs
 *
 * Transform the paragraphs with special annotations like <note> and
 * <caution> to paragraphs inside the HTML document with a class
 * representing the meaning of the docbook elements. The mapping which is
 * used inside this method is used throughout the document comoponent and
 * compatible with the RTS mapping.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToHtmlSpecialParagraphHandler extends ezcDocumentDocbookToHtmlBaseHandler
{
    /**
     * Handled paragraph names / types
     *
     * @var array
     */
    protected $types = array(
        'note'      => 'note',
        'tip'       => 'notice',
        'warning'   => 'warning',
        'important' => 'attention',
        'caution'   => 'danger',
    );

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
        $type = $this->types[$node->tagName];
        $paragraph = $root->ownerDocument->createElement( 'p' );
        $paragraph->setAttribute( 'class', $type );
        $root->appendChild( $paragraph );
        $converter->visitChildren( $node, $paragraph );
        return $root;
    }
}

?>
