<?php
/**
 * File containing the ezcDocumentDocbookToRstVariableListHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit itemized list / bullet lists
 *
 * Visit itemized lists (bullet list) and maintain the correct indentation for
 * list items.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToRstVariableListHandler extends ezcDocumentDocbookToRstBaseHandler
{
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
        foreach ( $node->childNodes as $child )
        {
            if ( ( $child->nodeType === XML_ELEMENT_NODE ) &&
                 ( $child->tagName === 'varlistentry' ) )
            {
                $term = $child->getElementsByTagName( 'term' )->item( 0 );
                $root .= ezcDocumentDocbookToRstConverter::wordWrap( trim( $converter->visitChildren( $term, '' ) ) ) . "\n";
                ezcDocumentDocbookToRstConverter::$indentation += 4;
                foreach ( $child->childNodes as $subChild )
                {
                    if ( ( $subChild->nodeType === XML_ELEMENT_NODE ) &&
                         ( $subChild->tagName === 'listitem' ) )
                    {
                        $root = $converter->visitChildren( $subChild, $root );
                    }
                }
                ezcDocumentDocbookToRstConverter::$indentation -= 4;
            }
        }

        return $root;
    }
}

?>
