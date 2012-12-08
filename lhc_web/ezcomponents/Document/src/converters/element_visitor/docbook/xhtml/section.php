<?php
/**
 * File containing the ezcDocumentDocbookToHtmlSectionHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit docbook sections
 *
 * Updates the docbook sections, which give us information about the depth
 * in the document, and may also be reference targets.
 *
 * Also visits title elements, which are commonly the first element in sections
 * and define section titles, which are converted to HTML header elements of
 * the respective level of indentation
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToHtmlSectionHandler extends ezcDocumentDocbookToHtmlBaseHandler
{
    /**
     * Current level of indentation in the docbook document.
     *
     * @var int
     */
    protected $level = 0;

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
        if ( $node->tagName === 'title' )
        {
            // Also set the document title from the first heading
            if ( $this->level === 1 )
            {
                $head = $this->getHead( $root );
                $title = $root->ownerDocument->createElement( 'title', htmlspecialchars( trim( $node->textContent ) ) );
                $head->appendChild( $title );
            }

            // Create common HTML headers
            $header = $root->ownerDocument->createElement( 'h' . min( 6, $this->level ) );
            if ( $this->level >= 6 )
            {
                $header->setAttribute( 'class', 'h' . $this->level );
            }
            $root->appendChild( $header );

            // Recurse
            $converter->visitChildren( $node, $header );
        }
        else
        {
            ++$this->level;

            // Set internal cross reference target if section has an ID assigned
            if ( $node->hasAttribute( 'ID' ) )
            {
                $target = $root->ownerDocument->createElement( 'a' );
                $target->setAttribute( 'name', $node->getAttribute( 'ID' ) );
                $root->appendChild( $target );
            }

            // Recurse
            $converter->visitChildren( $node, $root );

            // Reduce header level back to original state after recursion
            --$this->level;
        }

        return $root;
    }
}

?>
