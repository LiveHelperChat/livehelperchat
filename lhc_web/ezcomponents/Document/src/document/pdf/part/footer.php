<?php
/**
 * File containing the ezcDocumentPdfFooterPdfPart class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Basic foot class, which renders a simple page footer including information
 * from the document.
 *
 * Configured using the ezcDocumentPdfFooterOptions options class.
 * 
 * A footer, or any other PDF part, can be registered for rendering in the main
 * PDF class using the registerPdfPart() method, like:
 *
 * <code>
 *  $pdf = new ezcDocumentPdf();
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
 *  $pdf->createFromDocbook( $docbook );
 *  file_put_contents( __FILE__ . '.pdf', $pdf );
 * </code>
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentPdfFooterPdfPart extends ezcDocumentPdfPart
{
    /**
     * Options of footer
     *
     * @var ezcDocumentPdfFooterOptions
     */
    protected $options;

    /**
     * Reference to rendered document
     *
     * @var DOMDocument
     */
    protected $document;

    /**
     * Extracted title information
     *
     * @var mixed
     */
    protected $documentTitle;

    /**
     * Extracted author information
     *
     * @var mixed
     */
    protected $documentAuthor;

    /**
     * Create a new footer PDF part
     *
     * @param ezcDocumentPdfFooterOptions $options
     */
    public function __construct( ezcDocumentPdfFooterOptions $options = null )
    {
        $this->options = ( $options === null ?
            new ezcDocumentPdfFooterOptions() :
            $options );
    }

    /**
     * Hook on page creation
     *
     * Hook called on page creation, so that certain areas might be reserved or
     * it already may render stuff on the frshly created page.
     *
     * @param ezcDocumentPdfPage $page
     * @return void
     */
    public function hookPageCreation( ezcDocumentPdfPage $page )
    {
        // Get default styles from document
        $style = $this->styles->inferenceFormattingRules( $this->document );
        foreach ( $style as $name => $value )
        {
            $this->driver->setTextFormatting( $name, $value->value );
        }

        // Allocate space for footer
        if ( ( $space = $page->testFitRectangle(
                $page->x,
                $this->options->footer ? $page->y + $page->innerHeight - $this->options->height->get() : $page->y,
                $page->innerWidth,
                $this->options->height->get()
            ) ) === false )
        {
            // If we can't allocate the designated space, exit.
            return false;
        }

        // Calculate vertical alignement
        $offset = 0;
        if ( $this->options->footer )
        {
            $offset = $space->height - 2.1 * $this->driver->getCurrentLineHeight();
        }

        // Render document title
        if ( $this->documentTitle &&
             $this->options->showDocumentTitle )
        {
            // Inference these settings somehow
            $this->driver->setTextFormatting( 'font-weight', 'bold' );
            $width = $this->driver->calculateWordWidth( $this->documentTitle );
            $this->driver->drawWord(
                $space->x + ( $page->innerWidth - $width ) / 2,
                $space->y + $offset + $this->driver->getCurrentLineHeight(),
                $this->documentTitle
            );
            $offset += 1.1 * $this->driver->getCurrentLineHeight();
        }

        // Render document author
        if ( $this->documentAuthor &&
             $this->options->showDocumentAuthor )
        {
            // Inference these settings somehow
            $this->driver->setTextFormatting( 'font-weight', 'normal' );
            $this->driver->setTextFormatting( 'font-style', 'italic' );
            $width = $this->driver->calculateWordWidth( $this->documentAuthor );
            $this->driver->drawWord(
                $space->x + ( $page->innerWidth - $width ) / 2,
                $space->y + $offset + $this->driver->getCurrentLineHeight(),
                $this->documentAuthor
            );
            $offset += 1.1 * $this->driver->getCurrentLineHeight();
        }

        // Render page number
        if ( $this->options->showPageNumber )
        {
            $pageNumber = $page->number + $this->options->pageNumberOffset;
            $postion = $pageNumber % 2 ? $space->width - $this->driver->calculateWordWidth( $pageNumber ) : 0;
            $this->driver->drawWord(
                $space->x + $postion,
                $space->y + ( $space->height - $this->driver->getCurrentLineHeight() ) / 2
                    + $this->driver->getCurrentLineHeight(),
                $pageNumber
            );
        }

        $page->setCovered( $space );
        if ( !$this->options->footer )
        {
            $page->y += $space->height;
        }
    }

    /**
     * Hook on document creation
     *
     * Hook called when a new document is created.
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    public function hookDocumentCreation( ezcDocumentLocateableDomElement $element )
    {
        $this->document = $element;

        // Extract title and author information
        $xpath = new DOMXPath( $this->document->ownerDocument );

        $nodes = $xpath->query( '//*[local-name() = "title"]' );
        $this->documentTitle = $nodes->length ? $nodes->item( 0 )->textContent : null;

        $nodes = $xpath->query( '//*[local-name() = "author"]' );
        $this->documentAuthor = $nodes->length ? $nodes->item( 0 )->textContent : null;
    }
}
?>
