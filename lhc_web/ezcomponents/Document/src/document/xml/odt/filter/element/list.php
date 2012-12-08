<?php
/**
 * File containing the ezcDocumentOdtElementListFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for ODT <text:list/> and <text:list-item/> elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementListFilter extends ezcDocumentOdtElementBaseFilter
{
    /**
     * Mapping for list elements.
     *
     * Maps ODT list tags to DocBook list tags.
     *
     * @var array(string=>string)
     */
    protected $mapping = array(
        'list'      => 'itemizedlist',
        'list-item' => 'listitem',
    );

    /**
     * Filter a single element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        $element->setProperty( 'type', $this->mapping[$element->localName] );
    }

    /**
     * Check if filter handles the current element.
     *
     * Returns a boolean value, indicating weather this filter can handle
     * the current element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function handles( DOMElement $element )
    {
        return ( $element->namespaceURI === ezcDocumentOdt::NS_ODT_TEXT
            && isset( $this->mapping[$element->localName] ) );
    }
}

?>
