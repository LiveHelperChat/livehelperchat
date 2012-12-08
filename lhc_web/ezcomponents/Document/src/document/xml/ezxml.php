<?php
/**
 * File containing the ezcDocumentEzXml class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The document handler for the eZ Publish 3 XML document markup.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentEzXml extends ezcDocumentXmlBase implements ezcDocumentValidation
{
    /**
     * Construct document xml base.
     *
     * @ignore
     * @param ezcDocumentEzXmlOptions $options
     * @return void
     */
    public function __construct( ezcDocumentEzXmlOptions $options = null )
    {
        parent::__construct( $options === null ?
            new ezcDocumentEzXmlOptions() :
            $options );
    }

    /**
     * Return document compiled to the docbook format
     *
     * The internal document structure is compiled to the docbook format and
     * the resulting docbook document is returned.
     *
     * This method is required for all formats to have one central format, so
     * that each format can be compiled into each other format using docbook as
     * an intermediate format.
     *
     * You may of course just call an existing converter for this conversion.
     *
     * @return ezcDocumentDocbook
     */
    public function getAsDocbook()
    {
        $converter = new ezcDocumentEzXmlToDocbookConverter();
        $converter->options->errorReporting = $this->options->errorReporting;

        $document = $converter->convert( $this );
        $document->setPath( $this->path );

        return $document;
    }

    /**
     * Create document from docbook document
     *
     * A document of the docbook format is provided and the internal document
     * structure should be created out of this.
     *
     * This method is required for all formats to have one central format, so
     * that each format can be compiled into each other format using docbook as
     * an intermediate format.
     *
     * You may of course just call an existing converter for this conversion.
     *
     * @param ezcDocumentDocbook $document
     * @return void
     */
    public function createFromDocbook( ezcDocumentDocbook $document )
    {
        if ( $this->options->validate &&
             $document->validateString( $document ) !== true )
        {
            $this->triggerError( E_WARNING, "You try to convert an invalid docbook document. This may lead to invalid output." );
        }

        $this->path = $document->getPath();

        $converter = new ezcDocumentDocbookToEzXmlConverter();
        $converter->options->errorReporting = $this->options->errorReporting;
        $doc = $converter->convert( $document );
        $this->document = $doc->getDomDocument();
    }

    /**
     * Validate the input file
     *
     * Validate the input file against the specification of the current
     * document format.
     *
     * Returns true, if the validation succeded, and an array with
     * ezcDocumentValidationError objects otherwise.
     *
     * @param string $file
     * @return mixed
     */
    public function validateFile( $file )
    {
        $oldSetting = libxml_use_internal_errors( true );
        libxml_clear_errors();
        $document = new DOMDocument();
        $document->load( $file );
        $document->relaxNGValidate( $this->options->relaxNgSchema );

        // Get all errors
        $xmlErrors = libxml_get_errors();
        $errors = array();
        foreach ( $xmlErrors as $error )
        {
            $errors[] = ezcDocumentValidationError::createFromLibXmlError( $error );
        }
        libxml_clear_errors();
        libxml_use_internal_errors( $oldSetting );

        return ( count( $errors ) ? $errors : true );
    }

    /**
     * Validate the input string
     *
     * Validate the input string against the specification of the current
     * document format.
     *
     * Returns true, if the validation succeded, and an array with
     * ezcDocumentValidationError objects otherwise.
     *
     * @param string $string
     * @return mixed
     */
    public function validateString( $string )
    {
        $oldSetting = libxml_use_internal_errors( true );
        libxml_clear_errors();
        $document = new DOMDocument();
        $document->loadXml( $string );
        $document->relaxNGValidate( $this->options->relaxNgSchema );

        // Get all errors
        $xmlErrors = libxml_get_errors();
        $errors = array();
        foreach ( $xmlErrors as $error )
        {
            $errors[] = ezcDocumentValidationError::createFromLibXmlError( $error );
        }
        libxml_clear_errors();
        libxml_use_internal_errors( $oldSetting );

        return ( count( $errors ) ? $errors : true );
    }
}

?>
