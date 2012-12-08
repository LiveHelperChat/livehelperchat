<?php
/**
 * File containing the ezcDocumentDocbookToOdtFootnoteHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Visit footnotes.
 *
 * Visit docbook <footnote/> and transform them into ODT <text:note/>.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentDocbookToOdtFootnoteHandler extends ezcDocumentDocbookToOdtBaseHandler
{
    /**
     * Current footnote count.
     * 
     * @var int
     */
    protected $counter = 0;

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
        $label = $node->hasAttribute( 'label' ) ? $node->getAttribute( 'label' ) : ++$this->counter;

        // Adjust counter for inconsequently labeled notes
        if ( ctype_digit( $label ) && $label > $this->counter )
        {
            $this->counter = $label + 1;
        }

        $textNote = $root->ownerDocument->createElementNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:note'
        );
        $textNote->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:id',
            // OOO format
            'ftn' . $label
        );
        $textNote->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:note-class',
            'footnote'
        );

        $noteCitation = $root->ownerDocument->createElementNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:note-citation',
            $label
        );
        $noteCitation->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:label',
            $label
        );
        $textNote->appendChild( $noteCitation );

        $noteBody = $root->ownerDocument->createElementNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:note-body'
        );
        $textNote->appendChild( $noteBody );

        $root->appendChild( $textNote );

        $converter->visitChildren( $node, $noteBody );
        return $root;
    }
}

?>
