<?php
/**
 * File containing the ezcDocumentXmlBase class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * A base class for XML based document type handlers.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentXmlBase extends ezcDocument implements ezcDocumentValidation
{
    /**
     * DOM tree as the internal representation for the loaded XML.
     *
     * @var DOMDocument
     */
    protected $document;

    /**
     * Create document from input string
     *
     * Create a document of the current type handler class and parse it into a
     * usable internal structure.
     *
     * @param string $string
     * @return void
     */
    public function loadString( $string )
    {
        // Use internal error handling to handle XML errors manually.
        $oldXmlErrorHandling = libxml_use_internal_errors( true );
        libxml_clear_errors();

        // Load XML document
        $this->document = new DOMDocument();

        // Check if we should format the document later
        if ( $this->options->indentXml )
        {
            $this->document->preserveWhiteSpace = false;
            $this->document->formatOutput = true;
        }

        $this->document->loadXml( $string );

        $errors = ( $this->options->failOnError ?
            libxml_get_errors() :
            null );

        libxml_clear_errors();
        libxml_use_internal_errors( $oldXmlErrorHandling );

        // If there are errors and the error handling is activated throw an
        // exception with the occured errors.
        if ( $errors )
        {
            throw new ezcDocumentErroneousXmlException( $errors );
        }
    }

    /**
     * Construct directly from DOMDocument
     *
     * To save execution time this method offers the construction of XML
     * documents directly from a DOM document instance.
     *
     * @param DOMDocument $document
     * @return void
     */
    public function loadDomDocument( DOMDocument $document )
    {
        $this->document = $document;
    }

    /**
     * Set DOMDocument
     *
     * Directly set the internally stored DOMDocument object, to spare
     * additional XML parsing overhead. Setting a broken or invalid docbook
     * document is not checked here, ebcause validation would cost too much
     * performace on each set. Be careful what you set here, invalid documents
     * may lead to unpredictable errors.
     *
     * @param DOMDocument $document
     * @return void
     */
    public function setDomDocument( DOMDocument $document )
    {
        $this->document = $document;
    }

    /**
     * Get DOMDocument
     *
     * Directly return the internally stored DOMDocument object, to spare
     * additional XML parsing overhead.
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->document;
    }

    /**
     * Return document as string
     *
     * Serialize the document to a string an return it.
     *
     * @return string
     */
    public function save()
    {
        return $this->document->saveXml();
    }
}

?>
