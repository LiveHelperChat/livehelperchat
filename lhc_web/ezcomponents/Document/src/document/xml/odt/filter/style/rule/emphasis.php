<?php
/**
 * File containing the ezcDocumentOdtEmphasisStyleFilterRule class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style filter rule to detect <emphasis/> elements.
 *
 * This style filter rule checks <text:span/> elements in ODT for bold 
 * font-weight. Such elements are considered to be translated to <emphasis/> 
 * elements in DocBook.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 * @todo Emphasis can also be indicated by other styles like red color or 
 *       similar. In addition, emphasis should be detected relatively to the 
 *       surrounding style. Some kind of points-threshold-based system would 
 *       be nice.
 */
class ezcDocumentOdtEmphasisStyleFilterRule implements ezcDocumentOdtStyleFilterRule
{
    /**
     * Returns if the given $odtElement is handled by the rule.
     * 
     * @param DOMElement $odtElement 
     * @return bool
     */
    public function handles( DOMElement $odtElement )
    {
        return ( $odtElement->localName === 'span' );
    }

    /**
     * Detects emphasis elements by their style.
     *
     * This method checks the style of the given $odtElement for bold 
     * font-weight ("bold" or value >= 700). If this is detected, the type of 
     * the element is set to be <emphasis/>.
     * 
     * @param DOMElement $odtElement 
     * @param ezcDocumentOdtStyleInferencer $styleInferencer
     */
    public function filter( DOMElement $odtElement, ezcDocumentOdtStyleInferencer $styleInferencer )
    {
        $style = $styleInferencer->getStyle( $odtElement );
        $textProps = $style->formattingProperties->getProperties(
            ezcDocumentOdtFormattingProperties::PROPERTIES_TEXT
        );

        if ( isset( $textProps['font-weight'] ) && ( $textProps['font-weight'] === 'bold' || $textProps['font-weight'] >= 700 ) )
        {
            $odtElement->setProperty(
                'type',
                'emphasis'
            );
        }
    }
}

?>
