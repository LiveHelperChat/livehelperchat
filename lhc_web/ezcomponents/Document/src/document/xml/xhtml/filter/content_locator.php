<?php
/**
 * File containing the ezcDocumentXhtmlContentLocatorFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter, which tries to lacate the relevant content nodes in a HTML document,
 * and ignores all layout stuff around that.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlContentLocatorFilter extends ezcDocumentXhtmlBaseFilter
{
    /**
     * Bonus for special HTML element, so that the importance of a node is
     * increased, if it has such child nodes.
     *
     * @var array
     */
    protected $bonus = array(
        'a'          => 10,
        'b'          => 10,
        'big'        => 20,
        'blockquote' => 50,
        'cite'       => 25,
        'code'       => 25,
        'em'         => 20,
        'h1'         => 100,
        'h2'         => 80,
        'h3'         => 60,
        'h4'         => 40,
        'h5'         => 30,
        'h6'         => 20,
        'i'          => 10,
        'ol'         => 50,
        'p'          => 50,
        'q'          => 10,
        'small'      => 10,
        'strong'     => 20,
        'table'      => 25,
    );

    /**
     * Maximum importance found in the document.
     *
     * @var float
     */
    protected $maximumImportance = 0;

    /**
     * Most important node in the document
     *
     * @var float
     */
    protected $mostImportantNode = false;

    /**
     * Filter XHtml document
     *
     * Filter for the document, which may modify / restructure a document and
     * assign semantic information bits to the elements in the tree.
     *
     * @param DOMDocument $document
     * @return DOMDocument
     */
    public function filter( DOMDocument $document )
    {
        $xpath = new DOMXPath( $document );
        $body = $xpath->query( '/*[local-name() = "html"]/*[local-name() = "body"]' )->item( 0 );
        $this->calculateContentFactors( $body );

        if ( $this->mostImportantNode !== false )
        {
            // Replace contents of body node with the found "most important"
            // section, so we keep the metadata, but omit everything we consider as
            // layout.
            $contentNode = $this->mostImportantNode->cloneNode( true );

            // Remove all childs from HTML body
            for ( $i = ( $body->childNodes->length - 1 ); $i >= 0; --$i )
            {
                $body->removeChild( $body->childNodes->item( $i ) );
            }

            // Readd detected content node
            $body->appendChild( $contentNode );
        }
    }

    /**
     * Calculate content factors
     *
     * Try to calculate some kind of probability for each node in the document,
     * that the respective node is the root of the actual document content.
     *
     * @param DOMElement $element
     * @return float
     */
    protected function calculateContentFactors( DOMElement $element )
    {
        $textLength     = 0;
        $childElements  = 0;
        $childFactors   = 0;
        $childTypeBonus = 0;
        foreach ( $element->childNodes as $child )
        {
            switch ( $child->nodeType )
            {
                case XML_ELEMENT_NODE:
                    ++$childElements;
                    $childFactors += $this->calculateContentFactors( $child );

                    if ( isset( $this->bonus[$child->tagName] ) )
                    {
                        $childTypeBonus += $this->bonus[$child->tagName];
                    }
                    break;

                case XML_TEXT_NODE:
                    $textLength += strlen( trim( $child->wholeText ) );
                    break;
            }
        }

        // Use an exponential metric on text amount.
        $textFactor = max( 1, pow( $textLength / 50, 4 ) );

        $factor = $textFactor * ( ( $childFactors + $childTypeBonus ) / max( 1, abs( 10 - $childElements ) ) );

        if ( ( $factor > $this->maximumImportance ) &&
             ( $element->getProperty( 'type' ) === 'section' ) )
        {
            $this->maximumImportance = $factor;
            $this->mostImportantNode = $element;
        }

//        $attributes = $element->getProperty( 'attributes' );
//        $attributes['factor'] = $factor;
//        $element->setProperty( 'attributes', $attributes );

        return $factor;
    }
}

?>
