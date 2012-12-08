<?php
/**
 * File containing the ezcDocumentDocbookToWikiItemizedListHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit itemized list / bullet lists.
 *
 * Visit itemized lists (bullet list) and maintain the correct indentation for
 * list items.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToWikiItemizedListHandler extends ezcDocumentDocbookToWikiBaseHandler
{
    /**
     * Current list indentation level.
     *
     * @var int
     */
    protected $level = 0;

    /**
     * Handle a node.
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
        ++$this->level;

        foreach ( $node->childNodes as $child )
        {
            if ( ( $child->nodeType === XML_ELEMENT_NODE ) &&
                 ( $child->tagName === 'listitem' ) )
            {
                foreach ( $child->childNodes as $para )
                {
                    if ( $para->nodeType !== XML_ELEMENT_NODE )
                    {
                        continue;
                    }

                    if ( $para->tagName === 'para' )
                    {
                        $root .= str_repeat( '*', $this->level ) . ' ' .
                            trim( $converter->visitChildren( $para, '' ) ) . "\n\n";
                    }
                    else
                    {
                        $root = $converter->visitNode( $para, $root );
                    }
                }
            }
        }

        --$this->level;
        return $root;
    }
}

?>
