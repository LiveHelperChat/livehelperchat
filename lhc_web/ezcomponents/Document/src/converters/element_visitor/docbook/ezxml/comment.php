<?php
/**
 * File containing ezcDocumentDocbookToEzXmlCommentHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit docbook comment.
 *
 * Transform docbook comments into HTML ( / XML ) comments.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToEzXmlCommentHandler extends ezcDocumentElementVisitorHandler
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
        $comment = new DOMComment( htmlspecialchars( $node->textContent ) );
        $root->appendChild( $comment );

        return $root;
    }
}

?>
