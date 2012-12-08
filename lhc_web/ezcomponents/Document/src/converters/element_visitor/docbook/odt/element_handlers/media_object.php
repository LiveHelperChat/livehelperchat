<?php
/**
 * File containing the ezcDocumentDocbookToOdtMediaObjectHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Visit media objects.
 *
 * Visit docbook <mediaobject/> and transform them into ODT image frames.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 * @todo For later versions: Supporting non flat ODT, we can bundle images and 
 *       simply refer to them.
 */
class ezcDocumentDocbookToOdtMediaObjectHandler extends ezcDocumentDocbookToOdtBaseHandler
{
    /**
     * Counter to generate drawing names. 
     * 
     * @var integer
     */
    protected $counter = 0;

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
        $drawingId = ++$this->counter;

        if ( ( $imageData = $this->extractImageData( $node ) ) === false )
        {
            $converter->triggerError(
                E_PARSE,
                'Missing information in <meadiaobject /> or <inlinemediaobject />.'
            );
            return $root;
        }

        $frame = $root->appendChild(
            $root->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_DRAWING,
                'draw:frame'
            )
        );
        $frame->setAttributeNS(
            ezcDocumentOdt::NS_ODT_DRAWING,
            'draw:name',
            'graphics' . $drawingId
        );

        $this->styler->applyStyles( $node, $frame );

        $anchorType = $this->detectAnchorTye( $node );

        $frame->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:anchor-type',
            $anchorType
        );

        if ( $imageData->hasAttribute( 'width' ) )
        {
            $frame->setAttributeNS(
                ezcDocumentOdt::NS_ODT_SVG,
                'svg:width',
                $this->correctLengthMeasure( $converter, $imageData->getAttribute( 'width' ) )
            );
        }
        if ( $imageData->hasAttribute( 'depth' ) )
        {
            $frame->setAttributeNS(
                ezcDocumentOdt::NS_ODT_SVG,
                'svg:height',
                $this->correctLengthMeasure( $converter, $imageData->getAttribute( 'depth' ) )
            );
        }

        $image = $frame->appendChild(
            $root->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_DRAWING,
                'draw:image'
            )
        );

        $imgPath = $converter->getImageLocator()->locateImage(
            ( $imgFile = $imageData->getAttribute( 'fileref' ) )
        );

        if ( $imgPath === false )
        {
            $converter->triggerError(
                E_WARNING, "Could not find image '$imgFile'."
            );
            return $root;
        }

        if ( !is_readable( $imgPath ) )
        {
            $converter->triggerError(
                E_WARNING, "Image not readable '$imgFile'."
            );
            return $root;
        }

        $binaryData = $image->appendChild(
            $root->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_OFFICE,
                'office:binary-data',
                base64_encode(
                    file_get_contents(
                        $imgPath
                    )
                )
            )
        );

        return $root;
    }

    /**
     * Correct length measure value.
     *
     * ODT does not define a default for length measures. This method checks if 
     * a valid measure is already given in $length and appends the 
     * $lengthMeasure given in the converter options otherwise.
     * 
     * @param ezcDocumentElementVisitorConverter $converter 
     * @param string $length 
     * @return string
     */
    protected function correctLengthMeasure( ezcDocumentElementVisitorConverter $converter, $length )
    {
        if ( in_array( substr( $length, -2, 2 ), ezcDocumentDocbookToOdtConverterOptions::$validLengthMeasures ) )
        {
            return $length;
        }
        // @todo: Validate that number without measure is given
        return $length . $converter->options->lengthMeasure;
    }

    /**
     * Extracts the imagedata part of a media object.
     * 
     * @param DOMNode $node 
     * @return DOMNode
     */
    protected function extractImageData( DOMNode $node )
    {
        $imageDataElems = $node->getElementsByTagName( 'imagedata' );
        if ( $imageDataElems->length !== 1 )
        {
            return false;
        }
        $imageData = $imageDataElems->item( 0 );

        if ( !$imageData->hasAttribute( 'fileref' ) )
        {
            return false;
        }

        return $imageData;
    }

    /**
     * Detects and returns the anchortype of the given $node.
     *
     * Detects the correct ODT anchortype for the given DocBoom mediaobject 
     * which can be:
     *
     * - 'page' if the image frame is bound to a specific page
     * - 'paragraph' if the frame is bound to a specific paragraph
     * - 'char' if the frame is bound to a specific character in a paragraph
     * 
     * @param DOMElement $node 
     * @return string
     */
    protected function detectAnchorTye( DOMElement $node )
    {
        $anchorType = 'page';

        if ( !$this->isInsidePara( $node ) )
        {
            return $anchorType;
        }
        $anchorType = 'paragraph';

        if ( !$this->isInsideText( $node ) )
        {
            return $anchorType;
        }
        $anchorType = 'char';

        return $anchorType;
    }

    /**
     * Checks if $node is descendant of a <para/>.
     *
     * @param DOMNode $node 
     * @return bool
     */
    protected function isInsidePara( DOMNode $node )
    {
        $parent = $node->parentNode;

        if ( $parent === null )
        {
            return false;
        }
        if ( $parent->localName === 'para' )
        {
            return true;
        }
        return $this->isInsidePara( $parent );
    }

    /**
     * Checks if $node occurs in between plain text.
     *
     * @param DOMNode $node 
     * @return bool
     */
    protected function isInsideText( DOMNode $node )
    {
        $prevSib = $node->previousSibling;

        if ( $prevSib === null )
        {
            return false;
        }
        if ( $prevSib->nodeType === XML_TEXT_NODE && trim( $prevSib->nodeValue ) !== '' )
        {
            return true;
        }
        if ( $prevSib->nodeType === XML_ELEMENT_NODE )
        {
            // Spans or other inline elements
            return true;
        }

        return $this->isInsideText( $prevSib );
    }
}

?>
