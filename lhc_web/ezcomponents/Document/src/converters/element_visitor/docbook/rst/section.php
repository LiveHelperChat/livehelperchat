<?php
/**
 * File containing the ezcDocumentDocbookToRstSectionHandler class.
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
class ezcDocumentDocbookToRstSectionHandler extends ezcDocumentDocbookToRstBaseHandler
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
        if ( ezcDocumentDocbookToRstConverter::$indentation > 0 )
        {
            $converter->triggerError( E_WARNING, "Indented section found, cannot be represented in RST." );
        }

        // Reset indenteation level, ever we reach a new section
        ezcDocumentDocbookToRstConverter::$indentation = 0;

        if ( $node->tagName === 'title' )
        {
            // Get actual title string by recursing into the title node
            $converter->setSkipPostDecoration( true );
            $title = trim( $converter->visitChildren( $node, '' ) );
            $converter->setSkipPostDecoration( false );

            // Get RST title decoration characters
            if ( !isset( $converter->options->headerTypes[$this->level] ) )
            {
                $converter->triggerError( E_ERROR,
                    "No characters for title of level {$this->level} defined."
                );
                return $root . $title;
            }

            if ( strlen( $marker = $converter->options->headerTypes[$this->level] ) > 1 )
            {
                return $root . sprintf( "\n%s\n%s\n%s\n\n",
                    $marker = str_repeat( $marker[0], strlen( $title ) ),
                    $title,
                    $marker
                );
            }
            else
            {
                return $root . sprintf( "\n%s\n%s\n\n",
                    $title,
                    str_repeat( $marker, strlen( $title ) )
                );
            }
        }
        else
        {
            ++$this->level;

            // Set internal cross reference target if section has an ID assigned
            if ( $node->hasAttribute( 'ID' ) )
            {
                $root .= '.. _' . $node->getAttribute( 'ID' ) . ":\n\n";
            }

            // Recurse
            $root = $converter->visitChildren( $node, $root );

            // Reduce header level back to original state after recursion
            --$this->level;
        }

        return $root;
    }
}

?>
