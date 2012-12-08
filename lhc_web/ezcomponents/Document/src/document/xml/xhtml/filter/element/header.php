<?php
/**
 * File containing the ezcDocumentXhtmlHeaderElementFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for XHtml header elements, including grouping all following siblings
 * on the same header level in a section.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlHeaderElementFilter extends ezcDocumentXhtmlElementBaseFilter
{
    /**
     * Filter a single element
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        // Create new parent node if we found a header and aggregate everything
        // below the actual header into this node.
        $section = new ezcDocumentPropertyContainerDomElement( 'div' );

        $parent = $element->parentNode;

        // Replace header with new section node
        $parent->replaceChild( $section, $element );
        $section->setProperty( 'type', 'section' );
        $section->setProperty( 'level', $level = $this->getHeaderLevel( $element ) );

        $section->appendChild( $element );
        $element->setProperty( 'type', 'title' );

        // Skip all preceeding child elements, until we reach the current node.
        $children = $parent->childNodes;
        $childCount = $children->length;
        for ( $i = 0; $i < $childCount; ++$i )
        {
            if ( $section->isSameNode( $children->item( $i ) ) )
            {
                break;
            }
        }
        ++$i;

        while ( ( $node = $children->item( $i ) ) !== null )
        {
            if ( ( $node->nodeType === XML_ELEMENT_NODE ) &&
                 ( $node->tagName === 'div' ) &&
                 ( $node->getProperty( 'type' ) === 'section' ) &&
                 ( $node->getProperty( 'level' ) <= $level ) )
            {
                break;
            }
            else
            {
                $new = $node->cloneNode( true );
                $section->appendChild( $new );
                $parent->removeChild( $node );
            }
        }
    }

    /**
     * Get header level
     *
     * Get the header level of a HTML heading. Additionally to the default
     * levels h1-6 we repect a level specified in the class attribute, which is
     * for example used by the RST to XHtml conversion to specify header levels
     * higher then 6.
     *
     * @param DOMElement $element
     * @return int
     */
    protected function getHeaderLevel( DOMElement $element )
    {
        $headerLevel = (int) $element->tagName[1];
        if ( $headerLevel === 6 )
        {
            if ( $element->hasAttribute( 'class' ) &&
                 preg_match( '((?:\s|^)h(?P<level>\d+)(?:\s|$))', $element->getAttribute( 'class' ), $match ) )
            {
                $headerLevel = (int) $match['level'];
            }
        }

        return $headerLevel;
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
        return (bool) preg_match( '(^[hH][1-6]$)', $element->tagName );
    }
}

?>
