<?php
/**
 * File containing the ezcDocumentRstImageDirective class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visitor for RST image directives
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstImageDirective extends ezcDocumentRstDirective implements ezcDocumentRstXhtmlDirective
{
    /**
     * Transform directive to docbook
     *
     * Create a docbook XML structure at the directives position in the
     * document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    public function toDocbook( DOMDocument $document, DOMElement $root )
    {
        $media = $document->createElement( $root->tagName === 'para' ? 'inlinemediaobject' : 'mediaobject' );
        $root->appendChild( $media );

        $imageObject = $document->createElement( 'imageobject' );
        $media->appendChild( $imageObject );

        $image = $document->createElement( 'imagedata' );
        $image->setAttribute( 'fileref', trim( $this->node->parameters ) );
        $imageObject->appendChild( $image );

        // Handle optional settings on images
        if ( isset( $this->node->options['alt'] ) )
        {
            $text = $document->createElement( 'textobject', htmlspecialchars( $this->node->options['alt'] ) );
            $media->appendChild( $text );
        }

        if ( isset( $this->node->options['width'] ) )
        {
            $image->setAttribute( 'width', (int) $this->node->options['width'] );
        }

        if ( isset( $this->node->options['height'] ) )
        {
            $image->setAttribute( 'depth', (int) $this->node->options['height'] );
        }

        if ( isset( $this->node->options['align'] ) )
        {
            $image->setAttribute( 'align', trim( $this->node->options['align'] ) );
        }
    }

    /**
     * Create iframe for media object
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    protected function toXhtmlObject( DOMDocument $document, DOMElement $root )
    {
        $image = $document->createElement( 'object' );
        $image->setAttribute( 'data', $file = trim( $this->node->parameters ) );
        $root->appendChild( $image );

        // Handle optional settings on images
        $settings = array(
            'alt'    => 'title',
            'width'  => 'width',
            'height' => 'height',
            'align'  => 'class',
        );

        foreach ( $settings as $option => $attribute )
        {
            if ( isset( $this->node->options[$option] ) )
            {
                $image->setAttribute( $attribute, htmlspecialchars( trim( $this->node->options[$option] ) ) );
            }
        }

        // Also set contained text to alternative text, if provided.
        if ( isset( $this->node->options['alt'] ) )
        {
            $image->appendChild( new DOMText( $this->node->options['alt'] ) );
        }
    }

    /**
     * Create common img element for media object
     *
     * For all images we use the common <img> XHtml element.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    protected function toXhtmlImage( DOMDocument $document, DOMElement $root )
    {
        $image = $document->createElement( 'img' );
        $image->setAttribute( 'src', $file = trim( $this->node->parameters ) );
        $root->appendChild( $image );

        // Handle optional settings on images
        $settings = array(
            'alt'    => 'alt',
            'width'  => 'width',
            'height' => 'height',
            'align'  => 'class',
        );

        foreach ( $settings as $option => $attribute )
        {
            if ( isset( $this->node->options[$option] ) )
            {
                $image->setAttribute( $attribute, htmlspecialchars( trim( $this->node->options[$option] ) ) );
            }
        }

        // Set default value for required attribute alt, if not provided.
        if ( !isset( $this->node->options['alt'] ) )
        {
            $image->setAttribute( 'alt', basename( $file ) );
        }
    }

    /**
     * Transform directive to HTML
     *
     * Create a XHTML structure at the directives position in the document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    public function toXhtml( DOMDocument $document, DOMElement $root )
    {
        $fileInfo = pathInfo( trim(  $this->node->parameters ) );

        // If the image should be embedded directly inside of the body element,
        // we add another div around it, to make it valid XHTML.
        if ( $root->tagName === 'body' )
        {
            $div = $document->createElement( 'div' );
            $div->setAttribute( 'class', 'image' );
            $root->appendChild( $div );
            $root = $div;
        }

        if ( in_array( strtolower( pathInfo( trim( $this->node->parameters ), PATHINFO_EXTENSION ) ), array( 'swf', 'svg' ) ) )
        {
            $this->toXhtmlObject( $document, $root );
        }
        else
        {
            $this->toXhtmlImage( $document, $root );
        }
    }
}

?>
