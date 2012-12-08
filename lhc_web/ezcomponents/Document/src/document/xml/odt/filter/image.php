<?php
/**
 * File containing the ezcDocumentOdtImageFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter which extracts images from FODT (flat ODT) documents and stores them 
 * in the desired directory.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtImageFilter extends ezcDocumentOdtBaseFilter
{
    /**
     * ODT document options. 
     * 
     * @var ezcDocumentOdtOptions
     */
    protected $options;

    /**
     * Creates the filter object.
     *
     * Creates the filter object. Makes use of $imageDirectory, defined in the 
     * $options.
     *
     * @param ezcDocumentOdtOptions $options
     * @return void
     */
    public function __construct( ezcDocumentOdtOptions $options )
    {
        $this->options = $options;
    }

    /**
     * Filter ODT document.
     *
     * Filter for the document, which may modify / restructure a document and
     * assign semantic information bits to the elements in the tree.
     *
     * @param DOMDocument $document
     * @return DOMDocument
     */
    public function filter( DOMDocument $document )
    {
        $xpath = new DOMXPath( $document );

        $xpath->registerNamespace( 'office', ezcDocumentOdt::NS_ODT_OFFICE );
        $xpath->registerNamespace( 'draw', ezcDocumentOdt::NS_ODT_DRAWING );

        $binaries = $xpath->query( '//draw:image/office:binary-data' );
        
        foreach ( $binaries as $binary )
        {
            $this->extractBinary( $binary );
        }
        return $document;
    }

    /**
     * Extracts the binary content from $binary into a file.
     *
     * Extracts the binary image content from $binary to a file in the image 
     * directory ({@link $options}). The file name is created using {@link tempnam()} 
     * and set as an XLink HREF on the parent <draw:image/> element, as it 
     * would typically be in an ODT.
     *
     * @param DOMElement $binary
     */
    protected function extractBinary( DOMElement $binary )
    {
        $fileName = tempnam( $this->options->imageDir, 'ezcDocumentOdt' );
        
        file_put_contents(
            $fileName,
            base64_decode( $binary->nodeValue )
        );

        $binary->parentNode->setAttributeNS(
            ezcDocumentOdt::NS_XLINK,
            'xlink:href',
            $fileName
        );
    }
}

?>
