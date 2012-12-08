<?php
/**
 * File containing the ezcDocumentOdtListLevelStyleFilterRule class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style filter rule to distinguish list types.
 *
 * ODT does not distinguish between numbered and itemized lists on an XML 
 * element level, but through styling information. This rule implements 
 * detection of numbered lists.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 * @todo More information about the list can be extracted from the style, like 
 *       the start value and continuation of previous lists.
 */
class ezcDocumentOdtListLevelStyleFilterRule implements ezcDocumentOdtStyleFilterRule
{
    /**
     * Returns if the given $odtElement is handled by the rule.
     * 
     * @param DOMElement $odtElement 
     * @return bool
     */
    public function handles( DOMElement $odtElement )
    {
        return ( $odtElement->localName === 'list' );
    }

    /**
     * Detects numbered lists from ODT style information.
     *
     * This method detects the type of list in $odtElement by its list-level 
     * style and sets the attributes for $odtElement accordingly to have it 
     * converted properly to DocBook.
     * 
     * @param DOMElement $odtElement 
     * @param ezcDocumentOdtStyleInferencer $styleInferencer
     */
    public function filter( DOMElement $odtElement, ezcDocumentOdtStyleInferencer $styleInferencer )
    {
        $listStyle = $styleInferencer->getListStyle( $odtElement );
        
        $currentLevel = $this->getListLevel( $odtElement );
        
        switch ( get_class( $listStyle->listLevels[$currentLevel] ) )
        {
            case 'ezcDocumentOdtListLevelStyleNumber':
                $this->setNumberListProperties( $odtElement, $listStyle->listLevels[$currentLevel] );
                break;
            case 'ezcDocumentOdtListLevelStyleBullet':
                $this->setItemListProperties( $odtElement, $listStyle->listLevels[$currentLevel] );
                break;
        }
    }

    /**
     * Sets properties of numbered lists based on $listLevelProps.
     * 
     * @param DOMElement $numList 
     * @param ezcDocumentOdtListLevelStyleNumber $listLevelProps 
     * @return void
     */
    protected function setNumberListProperties( DOMElement $numList, ezcDocumentOdtListLevelStyleNumber $listLevelProps )
    {
        $numList->setProperty(
            'type',
            'orderedlist'
        );
    }

    /**
     * Sets properties of itemized lists based on $listLevelProps.
     * 
     * @param DOMElement $itemList 
     * @param ezcDocumentOdtListLevelStyleBullet $listLevelProps 
     */
    protected function setItemListProperties( DOMElement $itemList, ezcDocumentOdtListLevelStyleBullet $listLevelProps )
    {
        $itemList->setProperty(
            'type',
            'itemizedlist'
        );
    }

    /**
     * Determines the list level of $odtElement.
     *
     * Note that leveling starts with 1!
     * 
     * @param DOMElement $odtElement 
     * @return int
     */
    protected function getListLevel( DOMElement $odtElement )
    {
        if ( $odtElement->parentNode->nodeType === XML_DOCUMENT_NODE )
        {
            return 1;
        }
        return $this->getListLevel( $odtElement->parentNode )
            + ( $odtElement->parentNode->localName === 'list' ? 1 : 0 );
    }
}

?>
