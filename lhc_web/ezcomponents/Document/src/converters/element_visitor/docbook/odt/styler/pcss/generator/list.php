<?php
/**
 * File containing the ezcDocumentOdtListStyleGenerator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Class to generate styles for lists (<text:list/>).
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtListStyleGenerator extends ezcDocumentOdtStyleGenerator
{
    /**
     * Text style generator.
     * 
     * @var ezcDocumentOdtTextStyleGenerator
     */
    protected $textStyleGenerator;

    /**
     * List property generator. 
     * 
     * @var ezcDocumentOdtStyleListPropertyGenerator
     */
    protected $listPropertyGenerator;

    /**
     * List IDs.
     *
     * @var int 
     */
    protected $id = 0;

    /**
     * Creates a new style genertaor.
     * 
     * @param ezcDocumentOdtPcssConverterManager $styleConverters 
     */
    public function __construct( ezcDocumentOdtPcssConverterManager $styleConverters )
    {
        parent::__construct( $styleConverters );
        $this->textStyleGenerator = new ezcDocumentOdtTextStyleGenerator(
            $styleConverters
        );
        $this->listPropertyGenerator = new ezcDocumentOdtStyleListPropertyGenerator(
            $styleConverters
        );
    }

    /**
     * Returns if the given $odtElement is handled by this generator.
     * 
     * @param DOMElement $odtElement 
     * @return bool
     */
    public function handles( DOMElement $odtElement )
    {
        return (
            $odtElement->localName === 'list'
        );
    }
    
    /**
     * Creates the styles with $styleAttributes for the given $odtElement.
     * 
     * @param ezcDocumentOdtStyleInformation $styleInfo
     * @param DOMElement $odtElement 
     * @param array(string=>ezcDocumentPcssStyleValue) $styleAttributes 
     */
    public function createStyle( ezcDocumentOdtStyleInformation $styleInfo, DOMElement $odtElement, array $styleAttributes )
    {
        $baseListDef = $this->getBaseList( $odtElement );

        if ( $baseListDef['list'] === null )
        {
            $listStyle = $this->createNewListStyle( $odtElement, $styleInfo );
            $level = 1;
        }
        else
        {
            $listStyle = $this->retrieveListStyle( $baseListDef['list'], $styleInfo );
            $level = $baseListDef['depth'];
        }

        $this->createListLevelStyle( $styleInfo, $listStyle, $level, $styleAttributes );
    }

    /**
     * Creates a style for the <text:list /> element.
     *
     * Checks if the list is nested in a different list. If this is not the 
     * case, a new list style is generated. Otherwise, the existing list style 
     * is retrieved and a list definition for the corresponding nesting depth 
     * is created.
     * 
     * @param ezcDocumentOdtStyleInformation $styleInfo 
     * @param DOMElement $list 
     * @param array $styleAttributes 
     * @return void
     */
    protected function createListStyle( ezcDocumentOdtStyleInformation $styleInfo, DOMElement $list, array $styleAttributes )
    {
    }

    /**
     * Creates a new <text:list-style/> and applies it to the given 
     * $odtElement.
     *
     * This method creates and returns a new list style DOMElement in 
     * $styleInfo for $odtElement and assigns its name to the $odtElement. The 
     * list style can then be filled with list properties of different levels.
     * 
     * @param DOMElement $odtElement 
     * @param ezcDocumentOdtStyleInformation $styleInfo 
     * @return DOMElement
     */
    protected function createNewListStyle( DOMElement $odtElement, ezcDocumentOdtStyleInformation $styleInfo )
    {

        $listStyle = $styleInfo->automaticStyleSection->appendChild(
            $styleInfo->automaticStyleSection->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'text:list-style'
            )
        );
        $listStyle->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            'style:name',
            ( $styleName = $this->getUniqueStyleName( 'l' ) )
        );
        
        $odtElement->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:style-name',
            $styleName
        );

        // OOO attaches IDs to root lists, so do we.
        $odtElement->setAttributeNS(
            ezcDocumentOdt::NS_XML,
            'xml:id',
            sprintf( "%s%s", 'list', ++$this->id )
        );

        return $listStyle;
    }

    /**
     * Creates the <text:list-level-style-* /> element for $styleAttributes.
     *
     * This method creates a list-level-style in $listStyle for the given list 
     * $level applying $styleAttributes to this list level.
     * 
     * @param ezcDocumentOdtStyleInformation $styleInfo 
     * @param DOMElement $listStyle 
     * @param int $level 
     * @param array $styleAttributes 
     */
    protected function createListLevelStyle( ezcDocumentOdtStyleInformation $styleInfo, DOMElement $listStyle, $level, array $styleAttributes )
    {
        $styleAttributes = $this->calculateListLevelMeasures(
            $listStyle,
            $level,
            $styleAttributes
        );

        $listLevelStyle = $listStyle->appendChild(
            $listStyle->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'text:list-level-style-' . $styleAttributes['list-type']->value
            )
        );

        $listLevelStyle->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'level',
            $level
        );

        // Creates the text:style-name attribute with a new style that is 
        // applied to the bullet/numbering.
        $this->textStyleGenerator->createStyle(
            $styleInfo,
            $listLevelStyle,
            $styleAttributes
        );

        // Set by OOO no matter if bullet or number list
        // @todo: Make styleable
        $listLevelStyle->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            'style:num-suffix',
            '.'
        );

        $this->listPropertyGenerator->createProperty(
            $listLevelStyle,
            $styleAttributes
        );

        if ( $styleAttributes['list-type']->value === 'bullet' )
        {
            $listLevelStyle->setAttributeNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'text:bullet-char',
                $styleAttributes['list-bullet']->value
            );
        }
        else
        {
            $listLevelStyle->setAttributeNS(
                ezcDocumentOdt::NS_ODT_STYLE,
                'style:num-format',
                $styleAttributes['list-number']->value
            );
        }
    }

    /**
     * Calculates the list margin and indent.
     *
     * Margin and indent are handled in a strange way in ODF. This method 
     * calculates the margin for a list level, based on the previous level margin 
     * and the current margin and padding. In addition, the text-indent is set 
     * to fit the previous list-level. The new $styleAttributes are returned.
     * 
     * @param DOMElement $listStyle 
     * @param int $level 
     * @param array $styleAttributes
     * @return array(string=>float)
     */
    protected function calculateListLevelMeasures( DOMElement $listStyle, $level, $styleAttributes )
    {
        $previousMargin = 0;

        foreach( $listStyle->childNodes as $listStyleChild )
        {
            if ( $listStyleChild->nodeType === XML_ELEMENT_NODE
              && strpos( $listStyleChild->localName, 'list-level-style-' ) === 0
              && $listStyleChild->hasAttributeNS( ezcDocumentOdt::NS_ODT_TEXT, 'level' )
              && $listStyleChild->getAttributeNS( ezcDocumentOdt::NS_ODT_TEXT, 'level' ) == ( $level - 1 )
            )
            {
                $alignementProps = $listStyleChild->getElementsByTagNameNS(
                    ezcDocumentOdt::NS_ODT_STYLE,
                    'list-level-label-alignment'
                );
                if ( $alignementProps->length === 1 )
                {
                    $previousMargin = (int) $alignementProps->item( 0 )->getAttributeNS(
                        ezcDocumentOdt::NS_ODT_FO,
                        'margin-left'
                    );
                }
                break;
            }
        }

        $styleAttributes['margin']->value['left'] = $previousMargin
            + ( $margin = $styleAttributes['margin']->value['left'] )
            + ( $padding = $styleAttributes['padding']->value['left'] );
        $styleAttributes['text-indent'] = new ezcDocumentPcssStyleMeasureValue(
            - ( $margin + $padding )
        );

        return $styleAttributes;
    }

    /**
     * Returns the <text:list-style> DOMElement assigned to $odtList.
     * 
     * @param DOMElement $odtList 
     * @param ezcDocumentOdtStyleInformation $styleInfo 
     * @return DOMElement
     */
    protected function retrieveListStyle( $odtList, ezcDocumentOdtStyleInformation $styleInfo )
    {
        $styleName = $odtList->getAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'style-name'
        );

        $xpath = new DOMXpath( $styleInfo->automaticStyleSection->ownerDocument );
        $xpath->registerNamespace( ezcDocumentOdt::NS_ODT_TEXT, 'text' );
        $xpath->registerNamespace( ezcDocumentOdt::NS_ODT_STYLE, 'style' );

        $styleList = $xpath->query(
            "text:list-style[@style:name='{$styleName}']",
            $styleInfo->automaticStyleSection
        );
        
        if ( $styleList->length !== 1 )
        {
            throw new RuntimeException(
                "Inconsistent style section. Found {$styleList->length} list styles with name '{$styleName}'"
            );
        }

        return $styleList->item( 0 );
    }

    /**
     * Returns the parent <text:list/> element or null.
     *
     * This method returns the parent <text:list/> element for the given $list and the nesting depth of $list, 
     * if it is nested in another list. The returned structure is:
     *
     * <code>
     * <?php
     *  array(
     *      'base'  => <DOMElement|null>,
     *      'depth' => <int>
     *  );
     * ?>
     * </code>
     * 
     * @param DOMElement $list 
     * @param int $depth
     * @return array
     */
    protected function getBaseList( DOMElement $list, $depth = 1 )
    {
        $parent = $list->parentNode;
        if ( $parent === null || $parent->nodeType === XML_DOCUMENT_NODE )
        {
            return array(
                'list'   => null,
                'depth' => $depth
            );
        }
        if ( $parent->nodeType === XML_ELEMENT_NODE && $parent->localName === 'list' )
        {
            ++$depth;
            if ( $parent->hasAttributeNs( ezcDocumentOdt::NS_ODT_TEXT, 'style-name' ) )
            {
                return array(
                    'list' => $parent,
                    'depth' => $depth
                );
            }
        }
        return $this->getBaseList( $parent, $depth );
    }
}

?>
