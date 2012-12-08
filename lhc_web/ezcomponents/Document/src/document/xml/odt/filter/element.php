<?php
/**
 * File containing the ezcDocumentOdtElementFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter, which assigns semantic information just on the base of ODT element
 * semantics to the tree.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementFilter extends ezcDocumentOdtBaseFilter
{
    /**
     * List of element filter objects.
     *
     * @var array
     */
    protected $elementFilter = array();

    /**
     * Constructor
     *
     * Create initial filter array.
     *
     * @return void
     */
    public function __construct()
    {
        $this->elementFilter = array(
            // Basic mapping filter
            new ezcDocumentOdtElementHeaderFilter(),
            new ezcDocumentOdtElementTableFilter(),
            new ezcDocumentOdtElementParagraphFilter(),
            new ezcDocumentOdtElementFrameFilter(),
            new ezcDocumentOdtElementImageFilter(),
            new ezcDocumentOdtElementListFilter(),
            new ezcDocumentOdtElementFootnoteFilter(),
            new ezcDocumentOdtElementLinkFilter(),
            new ezcDocumentOdtElementWhitespaceFilter(),
        );
    }

    /**
     * Filter ODT document.
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
        $xpath->registerNamespace( 'office', ezcDocumentOdt::NS_ODT_OFFICE );
        $root = $xpath->query( '//office:body' )->item( 0 );
        $this->filterNode( $root );
    }

    /**
     * Add additional element filter.
     *
     * @param ezcDocumentOdtElementBaseFilter $filter
     */
    public function addFilter( ezcDocumentOdtElementBaseFilter $filter )
    {
        $this->elementFilter[] = $filter;
    }

    /**
     * Filter node.
     *
     * Depending on the element name, it parents and maybe element attributes
     * semantic information is assigned to nodes. This method also implements 
     * handling of significant whitespaces.
     *
     * @param DOMElement $element
     */
    protected function filterNode( DOMElement $element )
    {
        // Check all available filters, if they want to handle the current
        // element and let them handle the element in this case.
        foreach ( $this->elementFilter as $filter )
        {
            if ( $filter->handles( $element ) )
            {
                $filter->filterElement( $element );
            }
        }

        // Recurse into child elements
        //
        // We do the recursion in the reverse order of the elements, so that
        // restructuring of the tree does not affect the nodes, which are yet
        // up for filtering - this might end in errors about missing nodes
        // otherwise.
        for ( $i = ( $element->childNodes->length - 1 ); $i >= 0; --$i )
        {
            $child = $element->childNodes->item( $i );
            if ( $child->nodeType === XML_ELEMENT_NODE )
            {
                $this->filterNode( $child );
            }

            // Attempt for new whitespace handling
            if ( $child->nodeType === XML_TEXT_NODE && trim( $child->data ) === '' )
            {
                if ( $child->parentNode->localName === 'p'
                     && $i > 0
                     && $child->nextSibling !== null
                     // Work around for flat ODT, where XML is formatted 
                     // readable. OOO does not work properly when loading this.
                     && ( $child->nextSibling->nodeType !== XML_ELEMENT_NODE 
                          || ( $child->nextSibling->localName !== 'tab' 
                               && $child->nextSibling->localName !== 'line-break'
                             )
                        )
                   )
                {
                    $child->parentNode->replaceChild( new DOMText( ' ' ), $child );
                }
                else
                {
                    $child->parentNode->removeChild( $child );
                }
            }
        }
    }
}

?>
