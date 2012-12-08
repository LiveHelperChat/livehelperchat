<?php
/**
 * File containing the ezcDocumentDocbookToRstMediaObjectHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit media objects
 *
 * Media objects are all kind of other media types, embedded in the
 * document, like images.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToRstMediaObjectHandler extends ezcDocumentDocbookToRstBaseHandler
{
    /**
     * Extract directive parameters
     *
     * Extract the image directive parameters from a media object or inline
     * media object node in the Docbook document. Returns an array with
     * named keys containing the directive parameters.
     *
     * @param ezcDocumentElementVisitorConverter $converter
     * @param DOMElement $node
     * @return array
     */
    protected function getDirectiveParameters( ezcDocumentElementVisitorConverter $converter, DOMElement $node )
    {
        // Get image resource
        $resource = $node->getElementsBytagName( 'imagedata' )->item( 0 );

        $parameter = $resource->getAttribute( 'fileref' );
        $options = array();
        $content = null;

        // Transform attributes
        $attributes = array(
            'width'   => 'width',
            'depth'   => 'height',
        );
        foreach ( $attributes as $src => $dst )
        {
            if ( $resource->hasAttribute( $src ) )
            {
                $options[$dst] = $resource->getAttribute( $src );
            }
        }

        // Check if the image has a description
        if ( ( $textobject = $node->getElementsBytagName( 'textobject' ) ) &&
               ( $textobject->length > 0 ) )
        {
            $options['alt'] = trim( $textobject->item( 0 )->textContent );
        }

        // Check if the image has additional description assigned. In such a
        // case we wrap the image and the text inside another block.
        if ( ( $textobject = $node->getElementsBytagName( 'caption' ) ) &&
               ( $textobject->length > 0 ) )
        {
            $textobject = $textobject->item( 0 );

            // Decorate the childs of the caption node recursively, as it might
            // contain additional markup.
            $content = $converter->visitChildren( $textobject, '' );
        }

        // If the directive has explicit content, we render it as a figure
        // instead of an image.
        $type = ( $content !== null ) ? 'figure' : 'image';

        return array(
            'type'      => $type,
            'parameter' => $parameter,
            'options'   => $options,
            'content'   => $content,
        );
    }

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
        $directive = $this->getDirectiveParameters( $converter, $node );
        $root .= $this->renderDirective(
            $directive['type'],
            $directive['parameter'],
            $directive['options'],
            $directive['content']
        );

        return $root;
    }
}

?>
