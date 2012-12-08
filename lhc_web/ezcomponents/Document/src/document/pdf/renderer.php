<?php
/**
 * File containing the ezcDocumentPdfRenderer class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Abstract renderer base class
 *
 * Implements some basic rendering methods, which are required by all 
 * renderers. Should be extended to render elements in Docbook documents, which 
 * are not yet handled.
 *
 * To use a new ccustom renderer one needs to register it in the main renderer, 
 * which by default is implemented in the class ezcDocumentPdfMainRenderer.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
abstract class ezcDocumentPdfRenderer
{
    /**
     * Used driver implementation
     *
     * @var ezcDocumentPdfDriver
     */
    protected $driver;

    /**
     * Used PDF style inferencer for evaluating current styling
     *
     * @var ezcDocumentPcssStyleInferencer
     */
    protected $styles;

    /**
     * Construct renderer from driver to use
     *
     * @param ezcDocumentPdfDriver $driver
     * @param ezcDocumentPcssStyleInferencer $styles
     * @return void
     */
    public function __construct( ezcDocumentPdfDriver $driver, ezcDocumentPcssStyleInferencer $styles )
    {
        $this->driver = $driver;
        $this->styles = $styles;
    }

    /**
     * Render box background
     *
     * Render box background for the given bounding box with the given
     * styles.
     * 
     * @param ezcDocumentPdfBoundingBox $space 
     * @param array $styles 
     * @return void
     */
    protected function renderBoxBackground( ezcDocumentPdfBoundingBox $space, array $styles )
    {
        if ( isset( $styles['background-color'] ) &&
             ( $styles['background-color']->value['alpha'] < 1 ) )
        {
            $this->driver->drawPolygon(
                array(
                    array(
                        $space->x -
                            $styles['padding']->value['left'] -
                            $styles['border']->value['left']['width'],
                        $space->y -
                            $styles['padding']->value['top'] -
                            $styles['border']->value['top']['width'],
                    ),
                    array(
                        $space->x +
                            $styles['padding']->value['right'] +
                            $styles['border']->value['right']['width'] +
                            $space->width,
                        $space->y -
                            $styles['padding']->value['top'] -
                            $styles['border']->value['top']['width'],
                    ),
                    array(
                        $space->x +
                            $styles['padding']->value['right'] +
                            $styles['border']->value['right']['width'] +
                            $space->width,
                        $space->y +
                            $styles['padding']->value['bottom'] +
                            $styles['border']->value['bottom']['width'] +
                            $space->height,
                    ),
                    array(
                        $space->x -
                            $styles['padding']->value['left'] -
                            $styles['border']->value['left']['width'],
                        $space->y +
                            $styles['padding']->value['bottom'] +
                            $styles['border']->value['bottom']['width'] +
                            $space->height,
                    ),
                ),
                $styles['background-color']->value
            );
        }
    }

    /**
     * Render box border
     *
     * Render box border for the given bounding box with the given
     * styles.
     * 
     * @param ezcDocumentPdfBoundingBox $space 
     * @param array $styles 
     * @param bool $renderTop 
     * @param bool $renderBottom 
     * @return void
     */
    protected function renderBoxBorder( ezcDocumentPdfBoundingBox $space, array $styles, $renderTop = true, $renderBottom = true )
    {
        $topLeft = array(
            $space->x -
                $styles['padding']->value['left'] -
                $styles['border']->value['left']['width'] / 2,
            $space->y -
                $styles['padding']->value['top'] -
                $styles['border']->value['top']['width'] / 2,
        );
        $topRight = array(
            $space->x +
                $styles['padding']->value['right'] +
                $styles['border']->value['right']['width'] / 2 +
                $space->width,
            $space->y -
                $styles['padding']->value['top'] -
                $styles['border']->value['top']['width'] / 2,
        );
        $bottomRight = array(
            $space->x +
                $styles['padding']->value['right'] +
                $styles['border']->value['right']['width'] / 2 +
                $space->width,
            $space->y +
                $styles['padding']->value['bottom'] +
                $styles['border']->value['bottom']['width'] / 2 +
                $space->height,
        );
        $bottomLeft = array(
            $space->x -
                $styles['padding']->value['left'] -
                $styles['border']->value['left']['width'] / 2,
            $space->y +
                $styles['padding']->value['bottom'] +
                $styles['border']->value['bottom']['width'] / 2 +
                $space->height,
        );

        if ( $styles['border']->value['left']['width'] > 0 )
        {
            $this->driver->drawPolyline(
                array( $topLeft, $bottomLeft ),
                $styles['border']->value['left']['color'],
                $styles['border']->value['left']['width']
            );
        }

        if ( $renderTop && $styles['border']->value['top']['width'] > 0 )
        {
            $this->driver->drawPolyline(
                array( $topLeft, $topRight ),
                $styles['border']->value['top']['color'],
                $styles['border']->value['top']['width']
            );
        }

        if ( $styles['border']->value['right']['width'] > 0 )
        {
            $this->driver->drawPolyline(
                array( $topRight, $bottomRight ),
                $styles['border']->value['right']['color'],
                $styles['border']->value['right']['width']
            );
        }

        if ( $renderBottom && $styles['border']->value['bottom']['width'] > 0 )
        {
            $this->driver->drawPolyline(
                array( $bottomRight, $bottomLeft ),
                $styles['border']->value['bottom']['color'],
                $styles['border']->value['bottom']['width']
            );
        }
    }

    /**
     * Set box covered
     *
     * Mark rendered space as convered on the page.
     *
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfBoundingBox $space 
     * @param array $styles 
     * @return void
     */
    protected function setBoxCovered( ezcDocumentPdfPage $page, ezcDocumentPdfBoundingBox $space, array $styles )
    {
        // Apply bounding box modifications
        $space = clone $space;
        $space->x      -=
            $styles['padding']->value['left'] +
            $styles['border']->value['left']['width'] +
            $styles['margin']->value['left'];
        $space->width  +=
            $styles['padding']->value['left'] +
            $styles['padding']->value['right'] +
            $styles['border']->value['left']['width'] +
            $styles['border']->value['right']['width'] +
            $styles['margin']->value['left'] +
            $styles['margin']->value['right'];
        $space->y      -=
            $styles['padding']->value['top'] +
            $styles['border']->value['top']['width'] +
            $styles['margin']->value['top'];
        $space->height +=
            $styles['padding']->value['top'] +
            $styles['padding']->value['bottom'] +
            $styles['border']->value['top']['width'] +
            $styles['border']->value['bottom']['width'] +
            $styles['margin']->value['top'] +
            $styles['margin']->value['bottom'];
        $page->setCovered( $space );
        $page->y += $space->height;
    }

    /**
     * Evaluate available bounding box
     *
     * Returns false, if not enough space is available on current
     * page, and a bounding box otherwise.
     *
     * @param ezcDocumentPdfPage $page
     * @param array $styles
     * @param float $width
     * @return mixed
     */
    protected function evaluateAvailableBoundingBox( ezcDocumentPdfPage $page, array $styles, $width )
    {
        // Grap the maximum available vertical space
        $space = $page->testFitRectangle( $page->x, $page->y, $width, null );
        if ( $space === false )
        {
            // Could not allocate space, required for even one line
            return false;
        }

        // Apply bounding box modifications
        $space->x      +=
            $styles['padding']->value['left'] +
            $styles['border']->value['left']['width'] +
            $styles['margin']->value['left'];
        $space->width  -=
            $styles['padding']->value['left'] +
            $styles['padding']->value['right'] +
            $styles['border']->value['left']['width'] +
            $styles['border']->value['right']['width'] +
            $styles['margin']->value['left'] +
            $styles['margin']->value['right'];
        $space->y      +=
            $styles['padding']->value['top'] +
            $styles['border']->value['top']['width'] +
            $styles['margin']->value['top'];
        $space->height -=
            $styles['padding']->value['top'] +
            $styles['padding']->value['bottom'] +
            $styles['border']->value['top']['width'] +
            $styles['border']->value['bottom']['width'] +
            $styles['margin']->value['top'] +
            $styles['margin']->value['bottom'];

        return $space;
    }
}
?>
