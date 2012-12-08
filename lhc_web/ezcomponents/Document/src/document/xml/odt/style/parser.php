<?php
/**
 * File containing the ezcDocumentOdtStyleParser class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Parses ODT styles.
 *
 * An instance of this class is used parse style information from an DOMElement 
 * of a ODT document.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtStyleParser
{
    /**
     * Maps list-leve style XML elements to classes.
     *
     * @var array(string=>string)
     */
    protected static $listClassMap = array(
        'list-level-style-number' => 'ezcDocumentOdtListLevelStyleNumber',
        'list-level-style-bullet' => 'ezcDocumentOdtListLevelStyleBullet',
    );

    /**
     * Maps XML attributes to object attributes.
     *
     * @var array
     */
    protected static $listAttributeMap = array(
        'list-level-style-number' => array(
            ezcDocumentOdt::NS_ODT_STYLE => array(
                'num-format' => 'numFormat',
            ),
            ezcDocumentOdt::NS_ODT_TEXT => array(
                'display-levels' => 'displayLevels',
                'start-value'    => 'startValue'
            ),
        ),
        'list-level-style-bullet' => array(
            ezcDocumentOdt::NS_ODT_STYLE => array(
                'num-suffix'  => 'numSuffix',
                'num-prefix'  => 'numPrefix',
            ),
            ezcDocumentOdt::NS_ODT_TEXT => array(
                'bullet-char' => 'bulletChar',
            ),
        ),
    );

    /**
     * Parses the given $odtStyle.
     *
     * Parses the given $odtStyle and returns a style of $family with $name.
     * 
     * @param DOMElement $odtStyle 
     * @param string $family 
     * @param string $name 
     * @return ezcDocumentOdtStyle
     */
    public function parseStyle( DOMElement $odtStyle, $family, $name = null )
    {
        $style = new ezcDocumentOdtStyle( $family, $name );

        foreach ( $odtStyle->childNodes as $child )
        {
            if ( $child->nodeType === XML_ELEMENT_NODE )
            {
                $style->formattingProperties->setProperties(
                    $this->parseProperties( $child )
                );
            }
        }
        return $style;
    }

    /**
     * Parses the given $odtListStyle.
     *
     * Parses the given $odtListStyle and returns a list style with $name.
     * 
     * @param DOMElement $odtListStyle 
     * @param string $name 
     * @return ezcDocumentOdtListStyle
     */
    public function parseListStyle( DOMElement $odtListStyle, $name )
    {
        $listStyle = new ezcDocumentOdtListStyle( $name );

        foreach ( $odtListStyle->childNodes as $child )
        {
            if ( $child->nodeType === XML_ELEMENT_NODE )
            {
                $listLevel = $this->parseListLevel( $child );
                $listStyle->listLevels[$listLevel->level] = $listLevel;
            }
        }

        return $listStyle;
    }

    /**
     * Parses a list level style.
     *
     * Parses the given $listLevelElement and returns a corresponding 
     * list-level style object.
     * 
     * @param DOMElement $listLevelElement 
     * @return ezcDocumentOdtListLevelStyle
     */
    protected function parseListLevel( DOMElement $listLevelElement )
    {
        if ( !isset( self::$listClassMap[$listLevelElement->localName] ) )
        {
            throw new RuntimeException( "Unknown list-level element {$listLevelElement->localName}." );
        }

        $listLevelClass = self::$listClassMap[$listLevelElement->localName];
        $listLevel = new $listLevelClass(
            $listLevelElement->getAttributeNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'level'
            )
        );

        foreach ( self::$listAttributeMap[$listLevelElement->localName] as $ns => $attrs )
        {
            foreach ( $attrs as $xmlAttr => $objAttr )
            {
                if ( $listLevelElement->hasAttributeNS( $ns, $xmlAttr ) )
                {
                    $listLevel->$objAttr = $listLevelElement->getAttributeNS(
                        $ns,
                        $xmlAttr
                    );
                }
            }
        }

        return $listLevel;
    }

    /**
     * Parses the given property.
     * 
     * @param DOMElement $propElement 
     * @return ezcDocumentOdtFormattingProperties
     */
    protected function parseProperties( DOMElement $propElement )
    {
        $props = new ezcDocumentOdtFormattingProperties(
            $propElement->localName
        );
        // @todo: Parse sub-property elements
        foreach ( $propElement->attributes as $attrNode )
        {
            // @todo: Parse property values
            $props[$attrNode->localName] = $attrNode->value;
        }
        return $props;
    }
}

?>
