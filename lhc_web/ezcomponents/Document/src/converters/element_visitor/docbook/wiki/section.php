<?php
/**
 * File containing the ezcDocumentDocbookToWikiSectionHandler class.
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
class ezcDocumentDocbookToWikiSectionHandler extends ezcDocumentDocbookToWikiBaseHandler
{
    /**
     * Current level of indentation in the docbook document.
     *
     * @var int
     */
    protected $level = -1;

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
        // Reset indenteation level, ever we reach a new section
        ezcDocumentDocbookToWikiConverter::$indentation = 0;

        if ( $node->tagName === 'title' )
        {
            // Get actual title string by recursing into the title node
            $title = trim( $converter->visitChildren( $node, '' ) );

            return $root . sprintf( "\n%s %s\n\n",
                str_repeat( "=", $this->level + 1 ),
                $title
            );
        }
        else
        {
            ++$this->level;

            // Recurse
            $root = $converter->visitChildren( $node, $root );

            // Reduce header level back to original state after recursion
            --$this->level;
        }

        return $root;
    }
}

?>
