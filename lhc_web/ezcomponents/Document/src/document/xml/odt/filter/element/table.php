<?php
/**
 * File containing the ezcDocumentOdtElementTableFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for ODT <table:table> elements.
 *
 * This filter generates tables in DocBook. Native DocBook tables are generated 
 * by this class, which are the common basis inside the Document component. 
 * This should be migrated to {@link ezcDocumentOdtHtmlTableFilter} when the 
 * Document component switches to XHTML style tables in DocBook.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementTableFilter extends ezcDocumentOdtElementBaseFilter
{
    /**
     * Mapping for table elements.
     *
     * Maps ODT table tags to a method in this class to convert them.
     * 
     * @var array
     */
    protected $methodMap = array(
        'table'             => 'convertTable',
        'table-column'      => 'convertColumn',
        'table-header-rows' => 'convertHeader',
        'table-row'         => 'convertRow',
        'table-cell'        => 'convertCell',
        // covered-table-cell is left alone, will be ignored
    );

    /**
     * Filter a single element.
     *
     * @param ezcDocumentPropertyContainerDomElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        $method = $this->methodMap[$element->localName];
        $this->$method( $element );
    }

    /**
     * Check if filter handles the current element.
     *
     * Returns a boolean value, indicating weather this filter can handle
     * the current element.
     *
     * @param ezcDocumentPropertyContainerDomElement $element
     * @return void
     */
    public function handles( DOMElement $element )
    {
        return ( $element->namespaceURI === ezcDocumentOdt::NS_ODT_TABLE
            && isset( $this->methodMap[$element->localName] ) );
    }

    /**
     * Converts the <table:table/> element.
     * 
     * @param ezcDocumentPropertyContainerDomElement $element 
     * @return void
     */
    protected function convertTable( ezcDocumentPropertyContainerDomElement $element )
    {
        $element->setProperty( 'type', 'table' );
        $this->aggregateRows( $element );
        
        $tGroup = $element->ownerDocument->createElementNS(
            ezcDocumentOdt::NS_EZC,
            'tgroup'
        );
        $tGroup->setProperty( 'type', 'tgroup' );

        for ( $i = ( $element->childNodes->length - 1 ); $i >= 0; --$i )
        {
            $child = $element->childNodes->item( $i );
            $tGroup->insertBefore(
                $child->cloneNode( true ),
                $tGroup->firstChild
            );
            $element->removeChild( $child );
        }
        $element->appendChild( $tGroup );
    }

    /**
     * Aggregates table rows into a <tbody/> element.
     *
     * This method aggregates all rows of a table into a single <tbody/> 
     * element in the custom {@link ezcDocumentOdt::NS_EZC} namespace. This 
     * element will later be converted into a DocBook <tbody/> element.
     *
     * @param ezcDocumentPropertyContainerDomElement $element 
     * @return void
     */
    protected function aggregateRows( ezcDocumentPropertyContainerDomElement $element )
    {
        // @todo: Does ODT support multiple heads? Does DocBook support that?
        $body = $element->ownerDocument->createElementNS( ezcDocumentOdt::NS_EZC, 'tbody' );
        $body->setProperty( 'type', 'tbody' );

        // Aggregate all <table:table-row/> into a <tbody/> element.
        for ( $i = ( $element->childNodes->length - 1 ); $i >= 0; --$i )
        {
            $child = $element->childNodes->item( $i );

            // Collect table row and remove it
            if ( $child->nodeType === XML_ELEMENT_NODE
                 && $child->localName === 'table-row'
               )
            {
                $body->insertBefore(
                    $child->cloneNode( true ),
                    $body->firstChild
                );
                $element->removeChild( $child );
            }
            // Break is something else is found
            // @todo: What about multiple head rows?
            else if ( $child->nodeType === XML_ELEMENT_NODE )
            {
                break;
            }
        }

        $element->appendChild( $body );
    }

    /**
     * Convert the <table:table-column/> element.
     * 
     * @param ezcDocumentPropertyContainerDomElement $element 
     * @return void
     */
    protected function convertColumn( ezcDocumentPropertyContainerDomElement $element )
    {
        // @todo: Not supported by our DocBook sub-set. Should we?
        // $element->setProperty( 'type', 'col' );
    }

    /**
     * Convert the <table:table-header-rows/> element.
     * 
     * @param ezcDocumentPropertyContainerDomElement $element 
     * @return void
     */
    protected function convertHeader( ezcDocumentPropertyContainerDomElement $element )
    {
        $element->setProperty( 'type', 'thead' );
    }

    /**
     * Convert the <table:table-row/> element.
     * 
     * @param ezcDocumentPropertyContainerDomElement $element 
     * @return void
     */
    protected function convertRow( ezcDocumentPropertyContainerDomElement $element )
    {
        $element->setProperty( 'type', 'row' );
    }

    /**
     * Convert the <table:table-cell/> element.
     * 
     * @param ezcDocumentPropertyContainerDomElement $element 
     * @return void
     */
    protected function convertCell( ezcDocumentPropertyContainerDomElement $element )
    {
        $element->setProperty( 'type', 'entry' );
        $attrs = $element->getProperty( 'attributes' );
        if ( !is_array( $attrs ) )
        {
            $attrs = array();
        }

        /*
         * Col spanning is not supported by native DocBook tables.
        if ( $element->hasAttributeNS(
             ezcDocumentOdt::NS_ODT_TABLE,
             'number-columns-spanned'
         ) )
        {
            $attrs['colspan'] = $element->getAttributeNS(
                ezcDocumentOdt::NS_ODT_TABLE,
                'number-columns-spanned'
            );
        }
        */

        if ( $element->hasAttributeNS(
             ezcDocumentOdt::NS_ODT_TABLE,
             'number-rows-spanned'
         ) )
        {
            $attrs['morerows'] = $element->getAttributeNS(
                ezcDocumentOdt::NS_ODT_TABLE,
                'number-rows-spanned'
            ) - 1;
        }

        $element->setProperty( 'attributes', $attrs );
    }
}

?>
