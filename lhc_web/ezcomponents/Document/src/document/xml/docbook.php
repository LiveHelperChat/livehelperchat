<?php
/**
 * File containing the ezcDocumentDocbook class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The document handler for the docbook document markup.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbook extends ezcDocumentXmlBase
{
    /**
     * Construct document xml base.
     *
     * @ignore
     * @param ezcDocumentDocbookOptions $options
     * @return void
     */
    public function __construct( ezcDocumentDocbookOptions $options = null )
    {
        parent::__construct( $options === null ?
            new ezcDocumentDocbookOptions() :
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
        return $this;
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
        $this->path     = $document->getPath();
        $this->document = $document->getDomDocument();
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
        $document->schemaValidate( $this->options->schema );

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
        $document->schemaValidate( $this->options->schema );

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
