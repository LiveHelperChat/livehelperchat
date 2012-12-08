<?php
/**
 * File containing the ezcDocumentDocbookToHtmlXsltConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Converter for Docbook documents to XHtml using an available XSLT.
 *
 * By default the converter will try to download and use the XSLT provided at
 * http://docbook.sourceforge.net/release/xsl/current/html/docbook.xsl. You may
 * want to download and use the files locally.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToHtmlXsltConverter extends ezcDocumentXsltConverter
{
    /**
     * Construct new document converter.
     *
     * @param ezcDocumentDocbookToHtmlXsltConverterOptions $options
     */
    public function __construct( ezcDocumentDocbookToHtmlXsltConverterOptions $options = null )
    {
        parent::__construct(
            $options === null ?
                new ezcDocumentDocbookToHtmlXsltConverterOptions() :
                $options
        );
    }

    /**
     * Build document
     *
     * Build document of appropriate type from the DOMDocument, created by the
     * XSLT transformation.
     *
     * @param DOMDocument $document
     * @return ezcDocumentXmlBase
     */
    protected function buildDocument( DOMDocument $document )
    {
        $doc = new ezcDocumentXhtml();
        $doc->setDomDocument( $document );
        return $doc;
    }
}

?>
