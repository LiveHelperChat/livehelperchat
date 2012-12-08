<?php
/**
 * File containing the ezcDocumentOdtStyleTableCellPropertyGenerator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Table cell property generator.
 *
 * Creates and fills the <style:table-cell-properties/> element.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtStyleTableCellPropertyGenerator extends ezcDocumentOdtStylePropertyGenerator
{
    /**
     * Creates a new table-cell-properties generator.
     * 
     * @param ezcDocumentOdtPcssConverterManager $styleConverters 
     */
    public function __construct( ezcDocumentOdtPcssConverterManager $styleConverters )
    {
        parent::__construct(
            $styleConverters,
            array(
                'vertical-align',
                'background-color',
                'border',
                'padding',
            )
        );
    }

    /**
     * Creates the table-cell-properties element.
     *
     * Creates the table-cell-properties element in $parent and applies the fitting $styles.
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
                'style:table-cell-properties'
            )
        );

        $this->applyStyleAttributes(
            $prop,
            $styles
        );
        $this->setFixedAttributes( $prop );

        return $prop;
    }

    /**
     * Sets fixed properties.
     *
     * Some properties need to be set, but cannot be influenced by PCSS. These 
     * are set in this method.
     * 
     * @param DOMElement $prop 
     */
    protected function setFixedAttributes( DOMElement $prop )
    {
        // Align table cells via fo:align
        $prop->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            'style:text-align-source',
            'fix'
        );
    }
}

?>
