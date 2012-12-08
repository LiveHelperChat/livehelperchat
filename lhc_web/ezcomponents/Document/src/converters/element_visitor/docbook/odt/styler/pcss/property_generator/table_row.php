<?php
/**
 * File containing the ezcDocumentOdtStyleTableRowPropertyGenerator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Table row property generator.
 *
 * Creates and fills the <style:table-row-properties/> element.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtStyleTableRowPropertyGenerator extends ezcDocumentOdtStylePropertyGenerator
{
    /**
     * Creates a new table-row-properties generator.
     * 
     * @param ezcDocumentOdtPcssConverterManager $styleConverters 
     */
    public function __construct( ezcDocumentOdtPcssConverterManager $styleConverters )
    {
        parent::__construct(
            $styleConverters,
            array(
                'background-color',
            )
        );
    }

    /**
     * Creates the table-row-properties element.
     *
     * Creates the table-row-properties element in $parent and applies the fitting $styles.
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
                'style:table-row-properties'
            )
        );

        $this->applyStyleAttributes(
            $prop,
            $styles
        );

        return $prop;
    }
}

?>
