<?php
/**
 * File containing the abstract ezcDocumentOdtStyleGenerator base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Base class for style generators.
 *
 * Style generators used in {@link ezcDocumentOdtStyler} must extend this 
 * abstract class.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
abstract class ezcDocumentOdtStyleGenerator
{
    /**
     * Style converters. 
     * 
     * @var ezcDocumentOdtPcssConverterManager
     */
    protected $styleConverters;

    /**
     * Counters for style prefixes. 
     * 
     * @var array(string=>int)
     */
    protected $prefixCounters = array();

    /**
     * Creates a new style genertaor.
     * 
     * @param ezcDocumentOdtPcssConverterManager $styleConverters 
     */
    public function __construct( ezcDocumentOdtPcssConverterManager $styleConverters )
    {
        $this->styleConverters = $styleConverters;
    }

    /**
     * Returns if a style generator handles style generation for $odtElement.
     * 
     * @param DOMElement $odtElement 
     * @return bool
     */
    public abstract function handles( DOMElement $odtElement );

    /**
     * Creates the necessary styles to apply $styleAttributes to $odtElement.
     *
     * This method should create the necessary styles to apply $styleAttributes 
     * to the given $odtElement. In addition, it must set the correct 
     * attributes on $odtElement to source this style.
     * 
     * @param ezcDocumentOdtStyleInformation $styleInfo 
     * @param DOMElement $odtElement 
     * @param array $styleAttributes 
     */
    public abstract function createStyle( ezcDocumentOdtStyleInformation $styleInfo, DOMElement $odtElement, array $styleAttributes );

    /**
     * Returns a unique style name with the given $prefix.
     *
     * Note that generated name is only unique within this style generator, 
     * which is no problem, if only a single style generator takes care for a 
     * certain style family.
     * 
     * @param string $prefix 
     * @return string
     */
    protected function getUniqueStyleName( $prefix = 'style' )
    {
        if ( !isset( $this->prefixCounters[$prefix] ) )
        {
            $this->prefixCounters[$prefix] = 0;
        }
        return $prefix . ++$this->prefixCounters[$prefix];
    }
}

?>
