<?php
/**
 * File containing the ezcDocumentOdtElementWhitespaceFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for ODT <text:s/>, <text:tab/> and <text:line-break/> elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtElementWhitespaceFilter extends ezcDocumentOdtElementBaseFilter
{
    /**
     * Filter a single element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        $spaces = '';
        switch ( $element->localName )
        {
            case 's':
                $count = $element->getAttributeNS( ezcDocumentOdt::NS_ODT_TEXT, 'c' );
                $spaces = str_repeat(
                    ' ',
                    ( $count !== '' ? (int) $count : 1 )
                );
                break;
            case 'tab':
                $spaces = "\t";
                break;
            case 'line-break':
                $spaces = "\n";
                break;
        }
        $element->setProperty( 'spaces', $spaces );
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
            && ( $element->localName === 's' 
                 || $element->localName === 'tab' 
                 || $element->localName === 'line-break'
               )
        );
    }
}

?>
