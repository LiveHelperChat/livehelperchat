<?php
/**
 * File containing the ezcDocumentOdtElementParagraphFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for ODT <text:p> elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementParagraphFilter extends ezcDocumentOdtElementBaseFilter
{
    /**
     * Filter a single element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        if ( $this->hasSignificantWhitespace( $element ) )
        {
            $element->setProperty( 'type', 'literallayout' );
        }
        else 
        {
            $element->setProperty( 'type', 'para' );
        }
    }

    /**
     * Returns if significant whitespaces occur in the paragraph.
     *
     * This method checks if the paragraph $element contains significant
     * whitespaces in form of <text:s/> or <text:tab/> elements.
     * 
     * @param DOMElement $element 
     * @return bool
     */
    protected function hasSignificantWhitespace( DOMElement $element ) 
    {
        $xpath = new DOMXpath( $element->ownerDocument );
        $xpath->registerNamespace( 'text', ezcDocumentOdt::NS_ODT_TEXT );
        $whitespaces = $xpath->evaluate( './/text:s|.//text:tab|.//text:line-break', $element );

        return ( $whitespaces instanceof DOMNodeList && $whitespaces->length > 0 );
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
            && $element->localName === 'p' );
    }
}

?>
