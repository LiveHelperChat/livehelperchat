<?php
/**
 * File containing the ezcDocumentXhtmlMetadataFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter, which assigns semantic information just on the base of XHtml element
 * semantics to the tree.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlMetadataFilter extends ezcDocumentXhtmlBaseFilter
{
    /**
     * Metadata name mapping
     *
     * @var array
     */
    protected $mapping = array(
        // Common meta field names
        'description' => 'abstract',
        'version'     => 'releaseinfo',
        'date'        => 'date',
        'author'      => 'author',
        'authors'     => 'author',

        // Meta element dublin core extensions
        'dc.title'       => 'title',
        'dc.creator'     => 'author',
        // 'dc.subject' => '',
        'dc.description' => 'abstract',
        'dc.publisher'   => 'publisher',
        'dc.contributor' => 'contrib',
        'dc.date'        => 'date',
        // 'dc.type' => '',
        // 'dc.format' => '',
        // 'dc.identifier' => '',
        // 'dc.source' => '',
        // 'dc.relation' => '',
        // 'dc.coverage' => '',
        'dc.rights'      => 'copyright',
    );

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

        // Remove document title, as it is not
        $metadata = $xpath->query( '/*[local-name() = "html"]/*[local-name() = "head"]/*[local-name() = "meta"]' );
        foreach ( $metadata as $node )
        {
            $this->filterMetaData( $node );
        }
    }

    /**
     * Filter meta data
     *
     * Filter meta elements in HTML header for relevant metadata.
     *
     * @param DOMElement $element
     * @return void
     */
    protected function filterMetaData( DOMElement $element )
    {
        if ( $element->hasAttribute( 'name' ) &&
             $element->hasAttribute( 'content' ) &&
             ( $name = strtolower( $element->getAttribute( 'name' ) ) ) &&
             ( isset( $this->mapping[$name] ) ) )
        {
            // Set type of element
            $element->setProperty( 'type', $this->mapping[$name] );

            // Apply special parsing and conversion to some of the given
            // elements
            switch ( $this->mapping[$name] )
            {
                case 'abstract':
                    $textNode = $element->ownerDocument->createElement( 'span' );
                    $textNode->setProperty( 'type', 'para' );
                    $element->appendChild( $textNode );
                    break;

                default:
                    $textNode = $element;
            }

            // Set conents as child text node.
            $text = new DOMText( $element->getAttribute( 'content' ) );
            $textNode->appendChild( $text );
        }
    }
}

?>
