<?php
/**
 * File containing the ezcDocumentEzXmlToDocbookLinkHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit links.
 *
 * Transform links, internal or external, into the appropriate docbook markup.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentEzXmlToDocbookLinkHandler extends ezcDocumentElementVisitorHandler
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
        if ( $node->hasAttribute( 'anchor_name' ) )
        {
            // This is an internal reference
            $link = $root->ownerDocument->createElement( 'link' );
            $link->setAttribute( 'linked', $node->getAttribute( 'anchor_name' ) );
            $root->appendChild( $link );
        }
        else
        {
            switch ( true )
            {
                case $node->hasAttribute( 'url_id' ):
                    $method = 'fetchUrlById';
                    $value  = $node->getAttribute( 'url_id' );
                    break;

                case $node->hasAttribute( 'node_id' ):
                    $method = 'fetchUrlByNodeId';
                    $value  = $node->getAttribute( 'node_id' );
                    break;

                case $node->hasAttribute( 'object_id' ):
                    $method = 'fetchUrlByObjectId';
                    $value  = $node->getAttribute( 'object_id' );
                    break;

                default:
                    $converter->triggerError( E_WARNING, 'Unhandled link type.' );
                    return $root;
            }

            $link = $root->ownerDocument->createElement( 'ulink' );
            $link->setAttribute(
                'url',
                $converter->options->linkProvider->$method(
                    $value,
                    $node->hasAttribute( 'view' ) ? $node->getAttribute( 'view' ) : null,
                    $node->hasAttribute( 'show_path' ) ? $node->getAttribute( 'show_path' ) : null
                )
            );
            $root->appendChild( $link );
        }

        $converter->visitChildren( $node, $link );
        return $root;
    }
}

?>
