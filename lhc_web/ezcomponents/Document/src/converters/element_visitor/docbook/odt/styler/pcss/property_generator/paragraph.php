<?php
/**
 * File containing the ezcDocumentOdtStyleParagraphPropertyGenerator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Paragraph property generator.
 *
 * Creates and fills the <style:paragraph-properties/> element.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtStyleParagraphPropertyGenerator extends ezcDocumentOdtStylePropertyGenerator
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
                'text-align',
                'widows',
                'orphans',
                'text-indent',
                'margin',
                'border',
                // Custom, @see ezcDocumentOdtPcssParagraphStylePreprocessor
                'break-before',
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
                'style:paragraph-properties'
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
