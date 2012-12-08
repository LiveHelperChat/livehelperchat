<?php
/**
 * File containing the ezcDocumentOdtStyleExtractor class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Extracts style information from an ODT document.
 *
 * An instance of this class is used to extract styles from an ODT 
 * DOMDocument.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtStyleExtractor
{
    /**
     * The ODT document.
     * 
     * @var DOMDocument
     */
    protected $odt;

    /**
     * XPath object on the ODT document.
     * 
     * @var DOMXPath
     */
    protected $xpath;

    /**
     * Creates a new style extractor for the given $odt document.
     * 
     * @param DOMDocument $odt 
     */
    public function __construct( DOMDocument $odt )
    {
        $this->odt = $odt;

        $this->xpath = new DOMXpath( $odt );
        $this->xpath->registerNamespace( 'style', ezcDocumentOdt::NS_ODT_STYLE );
        $this->xpath->registerNamespace( 'text',  ezcDocumentOdt::NS_ODT_TEXT );
    }

    /**
     * Extract the style identified by $family and $name.
     *
     * Returns the DOMElement for the style identified by $family and $name. If 
     * $name is left out, the default style for $family will be extracted.
     * 
     * @param string $family 
     * @param string $name 
     * @return DOMElement
     */
    public function extractStyle( $family, $name = null )
    {
        $xpath = '//';
        if ( $name === null )
        {
            $xpath .= "style:default-style[@style:family='{$family}']";
        }
        else
        {
            $xpath .= "style:style[@style:family='{$family}' and @style:name='{$name}']";
        }
        $styles = $this->xpath->query( $xpath );

        if ( $styles->length !== 1 )
        {
            throw new RuntimeException( "Style of family '$family' with name '$name' not found." );
        }
        return $styles->item( 0 );
    }

    /**
     * Extracts the list style identified by $name.
     *
     * Returns the DOMElement for the list style identified by $name.
     *
     * @param string $name 
     * @return DOMElement
     * @todo Make $name optional and allow extraction of default list styles.
     */
    public function extractListStyle( $name )
    {
        $xpath = '//text:list-style[@style:name="' . $name . '"]';

        $styles = $this->xpath->query( $xpath );

        if ( $styles->length !== 1 )
        {
            throw new RuntimeException( "List style with name '$name' not found." );
        }
        return $styles->item( 0 );
    }
}

?>
