<?php
/**
 * File containing the ezcDocumentOdtStyleListPropertyGenerator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * List property generator.
 *
 * Creates and fills the <style:paragraph-properties/> element.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtStyleListPropertyGenerator extends ezcDocumentOdtStylePropertyGenerator
{
    /**
     * Creates a new paragraph-properties generator.
     * 
     * @param ezcDocumentOdtPcssConverterManager $styleConverters 
     */
    public function __construct( ezcDocumentOdtPcssConverterManager $styleConverters )
    {
        parent::__construct(
            $styleConverters,
            array(
                'margin',
                'text-indent',
            )
        );
    }

    /**
     * Creates the paragraph-properties element.
     *
     * Creates the paragraph-properties element in $parent and applies the fitting $styles.
     * 
     * @param DOMElement $parent 
     * @param array $styles 
     * @return DOMElement The created property
     */
    public function createProperty( DOMElement $parent, array $styles )
    {
        $prop = $parent->appendChild(
            $parent->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_STYLE,
                'style:list-level-properties'
            )
        );
        $prop->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:list-level-position-and-space-mode',
            'label-alignment'
        );

        $this->createLabelAllignement( $prop, $styles );

        return $prop;
    }

    /**
     * Creates the <style:list-level-label-alignment/> element in $prop. 
     * 
     * @param DOMElement $prop 
     * @param array $styles 
     * @return void
     */
    protected function createLabelAllignement( DOMElement $prop, array $styles )
    {
        $alignementProp = $prop->appendChild(
            $prop->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_STYLE,
                'style:list-level-label-alignment'
            )
        );

        // This is from ODF specs 1.3, but used by OOO already
        $alignementProp->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:label-followed-by',
            'listtab'
        );
        // As defined by OOO
        $alignementProp->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:list-tab-stop-position',
            sprintf( '%smm', $styles['margin']->value['left'] )
        );
        // Indentation of list bullet/number as negative padding
        $alignementProp->setAttributeNS(
            ezcDocumentOdt::NS_ODT_FO,
            'fo:text-indent',
            sprintf( '%smm', $styles['text-indent']->value )
        );
        $alignementProp->setAttributeNS(
            ezcDocumentOdt::NS_ODT_FO,
            'fo:margin-left',
            sprintf( '%smm', $styles['margin']->value['left'] )
        );
    }
}

?>
