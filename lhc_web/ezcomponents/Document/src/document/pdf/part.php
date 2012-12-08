<?php
/**
 * File containing the ezcDocumentPdfPart class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base class for additional PDF parts
 *
 * Parts can be new elements in a PDF page, which can hook into the rendering
 * of the PDF page, like footers or headers.
 *
 * This abstract part abse class offers a list of hooks which will be called,
 * if an instance of this class is registered in the renderer, these hooks are:
 *
 * - hookPageCreation
 * - hookPageRendering
 * - hookDocumentCreation
 * - hookDocumentRendering
 *
 * All these hooks do nothing by default, and should be overwritten to
 * accomplish the desired functionality.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentPdfPart
{
    /**
     * Reference to main renderer
     *
     * @var ezcDocumentPdfMainRenderer
     */
    protected $renderer;

    /**
     * Reference to driver
     *
     * @var ezcDocumentPdfDriver
     */
    protected $driver;

    /**
     * Reference to style inferencer
     *
     * @var ezcDocumentPcssStyleInferencer
     */
    protected $styles;

    /**
     * Registration function called by the renderer.
     *
     * Function called by the renderer, to set its properties, which pass the
     * relevant state objects to the part.
     *
     * @param ezcDocumentPdfMainRenderer $renderer
     * @param ezcDocumentPdfDriver $driver
     * @param ezcDocumentPcssStyleInferencer $styles
     * @return void
     */
    public function registerContext( ezcDocumentPdfMainRenderer $renderer, ezcDocumentPdfDriver $driver, ezcDocumentPcssStyleInferencer $styles )
    {
        $this->renderer = $renderer;
        $this->driver   = $driver;
        $this->styles   = $styles;
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
    }

    /**
     * Hook on page rendering
     *
     * Hook called on page rendering, which means, that a page is complete, by
     * all knowledge of the main renderer.
     *
     * @param ezcDocumentPdfPage $page
     * @return void
     */
    public function hookPageRendering( ezcDocumentPdfPage $page )
    {
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
    }

    /**
     * Hook on document rendering
     *
     * Hook called once a document is completely rendered.
     *
     * @return void
     */
    public function hookDocumentRendering()
    {
    }
}
?>
