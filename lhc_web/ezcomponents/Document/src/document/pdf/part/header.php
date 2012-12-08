<?php
/**
 * File containing the ezcDocumentPdfHeaderPdfPart class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Just an alias for the footer class, but will be positioned on the
 * top of a page by default.
 *
 * A header, or any other PDF part, can be registered for rendering in the main
 * PDF class using the registerPdfPart() method, like:
 *
 * <code>
 *  $pdf = new ezcDocumentPdf();
 *
 *  // Add a customized footer
 *  $pdf->registerPdfPart( new ezcDocumentPdfHeaderPdfPart(
 *      new ezcDocumentPdfFooterOptions( array( 
 *          'showPageNumber' => false,
 *          'height'         => '10mm',
 *      ) )
 *  ) );
 *
 *  $pdf->createFromDocbook( $docbook );
 *  file_put_contents( __FILE__ . '.pdf', $pdf );
 * </code>
 *
 * Since it is just an alias class for the
 * ezcDocumentPdfFooterPdfPart it is also confugured by using the
 * ezcDocumentPdfFooterOptions class.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentPdfHeaderPdfPart extends ezcDocumentPdfFooterPdfPart
{
    /**
     * Create a new footer PDF part.
     *
     * @param ezcDocumentPdfFooterOptions $options 
     */
    public function __construct( ezcDocumentPdfFooterOptions $options = null )
    {
        $this->options = ( $options === null ?
            new ezcDocumentPdfFooterOptions() :
            $options );
        $this->options->footer = false;
    }
}
?>
