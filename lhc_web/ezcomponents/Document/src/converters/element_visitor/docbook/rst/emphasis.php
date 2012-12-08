<?php
/**
 * File containing the ezcDocumentDocbookToRstEmphasisHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit emphasis
 *
 * Emphasis markup is used to emphasize text inside a paragraph and is
 * rendered, depending on the assigned role, as strong or em tags in HTML.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToRstEmphasisHandler extends ezcDocumentDocbookToRstBaseHandler
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
        if ( $node->hasAttribute( 'Role' ) &&
             ( $node->getAttribute( 'Role' ) === 'strong' ) )
        {
            $marker = '**';
        }
        else
        {
            $marker = '*';
        }

        return $root . ' ' . $marker . $converter->visitChildren( $node, '' ) . $marker;
    }
}

?>
