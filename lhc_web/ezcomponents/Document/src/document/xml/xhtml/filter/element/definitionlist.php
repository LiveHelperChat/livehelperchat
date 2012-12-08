<?php
/**
 * File containing the ezcDocumentXhtmlDefinitionListElementFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for XHtml definition lists
 *
 * Definition lists in XHtml are a specilized markup for terms and their
 * descriptions / definitions. In Docbook a term an its definitions are
 * surrounded by an additional element, which is added by this filter.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlDefinitionListElementFilter extends ezcDocumentXhtmlElementBaseFilter
{
    /**
     * Filter a single element
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        // We need to create invalid markup here, as there is no surrounding
        // element allowed for groups of dt and dd elements.
        $entry = new ezcDocumentPropertyContainerDomElement( 'div' );

        $term   = $element->cloneNode( true );
        $parent = $element->parentNode;

        // Replace header with new section node
        $parent->replaceChild( $entry, $element );
        $entry->setProperty( 'type', 'varlistentry' );
        $entry->appendChild( $term );

        // Skip all preceeding child elements, until we reach the current node.
        $children = $parent->childNodes;
        $childCount = $children->length;
        for ( $i = 0; $i < $childCount; ++$i )
        {
            if ( $entry->isSameNode( $children->item( $i ) ) )
            {
                break;
            }
        }
        ++$i;

        while ( ( $node = $children->item( $i ) ) !== null )
        {
            if ( ( $node->nodeType === XML_ELEMENT_NODE ) &&
                 ( ( $node->tagName === 'dt' ) ||
                   ( $node->tagName === 'dd' ) ) )
            {
                $new = $node->cloneNode( true );
                $entry->appendChild( $new );
                $parent->removeChild( $node );
            }
            else
            {
                ++$i;
            }
        }

    }

    /**
     * Check if filter handles the current element
     *
     * Returns a boolean value, indicating weather this filter can handle
     * the current element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function handles( DOMElement $element )
    {
        return ( $element->tagName === 'dt' );
    }
}

?>
