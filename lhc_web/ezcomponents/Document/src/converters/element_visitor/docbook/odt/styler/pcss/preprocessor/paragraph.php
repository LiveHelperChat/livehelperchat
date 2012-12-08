<?php
/**
 * File containing the ezcDocumentOdtPcssParagraphStylePreprocessor class.
 *
 * @access private
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Paragraph style pre-processor.
 *
 * Pre-processes paragraph styles. If there is a <beginpage/> element right 
 * before the processed paragraph the custom "break-before" PCSS property is 
 * set to "page", which will result in a corresponding ODT style attribute.
 *
 * @access private
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentOdtPcssParagraphStylePreprocessor
{
    /**
     * Pre-process styles and return them.
     *
     * Performs some detection of list styles in the $docBookElement and its 
     * document and sets according PCSS properties in $styles.
     *
     * @param ezcDocumentOdtStyleInformation $styleInfo
     * @param DOMElement $docBookElement
     * @param DOMElement $odtElement 
     * @param array $styles 
     * @return array
     */
    public function process( ezcDocumentOdtStyleInformation $styleInfo, DOMElement $docBookElement, DOMElement $odtElement, array $styles )
    {
        if ( ( $odtElement->localName === 'h' || $odtElement->localName === 'p' )
             && $this->isOnNewPage( $docBookElement )
           )
        {
            $styles['break-before'] = new ezcDocumentPcssStyleStringValue( 'page' );
        }
        return $styles;
    }

    /**
     * Returns if the given $docBookElement is to be rendered on a new page.
     *
     * @param DOMElement $docBookElement
     * @return bool
     */
    protected function isOnNewPage( DOMElement $docBookElement )
    {
        while ( $docBookElement->previousSibling !== null )
        {
            $docBookElement = $docBookElement->previousSibling;
            if ( $docBookElement->nodeType === XML_ELEMENT_NODE )
            {
                if ( $docBookElement->localName === 'beginpage' )
                {
                    return true;
                }
                break;
            }
        }
        return false;
    }
}

?>
