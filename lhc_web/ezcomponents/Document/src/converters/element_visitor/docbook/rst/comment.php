<?php
/**
 * File containing the ezcDocumentDocbookToRstCommentHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit docbook comment
 *
 * Transform docbook comments into HTML ( / XML ) comments.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToRstCommentHandler extends ezcDocumentDocbookToRstBaseHandler
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
        $comment = $converter->visitChildren( $node, '' );
        $converter->setSkipPostDecoration( false );
        $root .= '.. ' . trim( ezcDocumentDocbookToRstConverter::wordWrap( $comment, 3 ) ) . "\n\n";

        return $root;
    }
}

?>
