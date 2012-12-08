<?php
/**
 * File containing the ezcDocumentPdf class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Document handler for PDF documents.
 * 
 * This document handler can load Docbook documents and generate PDF documents
 * from them. It can be configured using its option class
 * ezcDocumentPdfOptions. The example below shows the configuration of a
 * driver.
 *
 * <code>
 *  // Load the docbook document and create a PDF from it
 *  $pdf = new ezcDocumentPdf();
 *  $pdf->options->driver = new ezcDocumentPdfHaruDriver();
 *
 *  // Load a custom style sheet
 *  $pdf->loadStyles( 'custom.css' );
 *
 *  // Add a customized footer
 *  $pdf->registerPdfPart( new ezcDocumentPdfFooterPdfPart(
 *      new ezcDocumentPdfFooterOptions( array( 
 *          'showDocumentTitle'  => false,
 *          'showDocumentAuthor' => false,
 *          'height'             => '10mm',
 *      ) )
 *  ) );
 *
 *  // Add a customized header
 *  $pdf->registerPdfPart( new ezcDocumentPdfHeaderPdfPart(
 *      new ezcDocumentPdfFooterOptions( array( 
 *          'showPageNumber'     => false,
 *          'height'             => '10mm',
 *      ) )
 *  ) );
 *
 *  $pdf->createFromDocbook( $docbook );
 *  file_put_contents( __FILE__ . '.pdf', $pdf );
 * </code>
 *
 * Like shown in the example, it is possible to load any amount of custom style
 * definitions and register additional PDF parts, like headers and footers.
 *
 * @package Document
 * @version 1.3.1
 * @mainclass
 */
class ezcDocumentPdf extends ezcDocument
{
    /**
     * Container for style directives.
     *
     * @var ezcDocumentPcssStyleInferencer
     */
    protected $styles;

    /**
     * The generated PDF
     *
     * @var string
     */
    protected $content;

    /**
     * List of PDF parts to append to documents
     *
     * @var array(ezcDocumentPdfPart)
     */
    protected $pdfParts = array();

    /**
     * Construct RST document.
     *
     * @ignore
     * @param ezcDocumentPdfOptions $options
     * @return void
     */
    public function __construct( ezcDocumentPdfOptions $options = null )
    {
        parent::__construct( $options === null ?
            new ezcDocumentPdfOptions() :
            $options );

        $this->styles          = new ezcDocumentPcssStyleInferencer();

        if ( $this->options->driver === null )
        {
            $this->options->driver = new ezcDocumentPdfHaruDriver();
        }
    }

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
        throw new ezcBaseFunctionalityNotSupportedException( 'Reading PDF', 'Not implemented yet.' );
    }

    /**
     * Load style definition file
     *
     * Parse and load a PCSS file and use the resulting style definitions for
     * rendering.
     *
     * @param string $file
     * @return void
     */
    public function loadStyles( $file )
    {
        $parser = new ezcDocumentPcssParser();
        $this->styles->appendStyleDirectives(
            $parser->parseFile( $file )
        );
    }

    /**
     * Append a PDF part
     *
     * Register additional PDF parts to be included in the rendering process,
     * like headers and footers.
     *
     * @param ezcDocumentPdfPart $part
     * @return void
     */
    public function registerPdfPart( ezcDocumentPdfPart $part )
    {
        $this->pdfParts[] = $part;
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
        throw new RuntimeException( 'Reading PDF documents is not implemented.' );
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

        $this->options->driver->setOptions( $this->options );
        $renderer = new ezcDocumentPdfMainRenderer(
            $this->options->driver,
            $this->styles,
            $this->options
        );

        foreach ( $this->pdfParts as $part )
        {
            $renderer->registerPdfPart( $part );
        }

        $this->content = $renderer->render( $document, $this->options->hyphenator, $this->options->tokenizer );

        // Merge errors from renderer
        $this->errors = array_merge(
            $this->errors,
            $renderer->getErrors()
        );
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
        return $this->content;
    }
}

?>
