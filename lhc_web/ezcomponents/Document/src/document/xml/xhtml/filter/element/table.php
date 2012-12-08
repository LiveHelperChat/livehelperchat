<?php
/**
 * File containing the ezcDocumentXhtmlTableElementFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for XHtml table elements.
 *
 * Tables, where the rows are nor structured into a tbody and thead are
 * restructured into those by this filter.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlTableElementFilter extends ezcDocumentXhtmlElementBaseFilter
{
    /**
     * Filter a single element
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        $type       = false;
        $aggregated = array();
        $processed  = array();
        for ( $i = ( $element->childNodes->length - 1 ); $i >= -1; --$i )
        {
            // Get type of current row, or set row type to null, if it is no
            // table row.
            $child   = $element->childNodes->item( $i );
            $childNr = $i;
            if ( $child &&
                 ( $child->nodeType === XML_ELEMENT_NODE ) &&
                 ( $child->tagName === 'tr' ) )
            {
                $rowType = $this->getType( $child );
            }
            else
            {
                $rowType = null;
            }

            // There are three different actions, which need to be performed in
            // this loop:
            //  - Skip irrelevant nodes (whitespaces)
            //  - Aggregate tr nodes
            //  - Move tr nodes to new tbody / thead nodes, depending on their
            //    type, when the row type changes, we reached the last row, or
            //    their is some tbody / thead node found.
            if ( ( count( $aggregated ) ) &&
                   ( ( $i < 0 ) ||
                     ( ( $rowType !== null ) &&
                       ( $rowType !== $type ) ) ) )
            {
                // Move nodes to new subnode
                $lastNode = end( $aggregated );
                $parent   = $lastNode->parentNode;
                $newNode  = new ezcDocumentPropertyContainerDomElement( $type );
                $parent->insertBefore( $newNode, $lastNode );
                $newNode->setProperty( 'type', $type );

                // Append all aggregated nodes
                $aggregated = array_reverse( $aggregated );
                foreach ( $aggregated as $node )
                {
                    $cloned = $node->cloneNode( true );
                    $newNode->appendChild( $cloned );
                    $parent->removeChild( $node );
                }

                // Clean up
                $aggregated = array();
                $type = false;

                // Maybe we need to handle the current element again.
                ++$i;
            }

            if ( $child &&
                 ( $child->nodeType !== XML_ELEMENT_NODE ) )
            {
                $child->parentNode->removeChild( $child );
                continue;
            }
            elseif ( ( $rowType !== null ) &&
                     ( !isset( $processed[$childNr] ) ) )
            {
                // Aggregate nodes
                $aggregated[]        = $child;
                $processed[$childNr] = true;
                $type                = $rowType;
            }
        }
    }

    /**
     * Estimate type of a row
     *
     * Estimate, if a row in a table is a header or a footer row. This
     * estiamtion checks if there are more th elements, the td elements and
     * returns either 'thead' or 'tbody' as the row type on base of that.
     *
     * @param DOMElement $element
     * @return string
     */
    protected function getType( DOMElement $element )
    {
        $thCount = $element->getElementsByTagName( 'th' )->length;
        $tdCount = $element->getElementsByTagName( 'td' )->length;

        return ( $thCount < $tdCount ) ? 'tbody' : 'thead';
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
        return ( $element->tagName === 'table' );
    }
}

?>
