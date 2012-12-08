<?php
/**
 * File containing the ezcDocumentPdfTableRenderer class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Renders a table.
 *
 * Tries to render a table into the available space, and aborts if
 * not possible.
 *
 * A more detailed explanation of the main renderer stacking used for tbale 
 * rendering and the page level transaction ahndling can be found in the class 
 * level docblock of the ezcDocumentPdfMainRenderer class.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfTableRenderer extends ezcDocumentPdfMainRenderer
{
    /**
     * Reference to the main renderer.
     * 
     * @var ezcDocumentPdfMainRenderer
     */
    protected $mainRenderer;

    /**
     * Width of current cell.
     * 
     * @var flaot
     */
    protected $cellWidth;

    /**
     * Areas covored while rendering a single cell, so that the cell contents 
     * do not get in the way of other cells contents.
     * 
     * @var array
     */
    protected $covered = array();

    /**
     * Box of the whole table.
     * 
     * @var array
     */
    protected $tableBox = array();

    /**
     * Boxes for all currently drawn cells so their border can be renderer once 
     * the row baseline is known.
     * 
     * @var array
     */
    protected $cellBoxes = array();

    /**
     * The last page the current cell rendered contents on.
     * 
     * @var ezcDocumentPdfPage
     */
    protected $lastPageForCell;

    /**
     * Additional borders to render.
     *
     * A list of borders to render, detected on page wrapps. Delayed to not be 
     * reverted by reverted transactions in sub renderers.
     * 
     * @var array
     */
    protected $additionalBorders = array();

    /**
     * Construct renderer from driver to use.
     *
     * @param ezcDocumentPdfDriver $driver 
     * @param ezcDocumentPcssStyleInferencer $styles 
     * @param ezcDocumentPdfOptions $options 
     */
    public function __construct( ezcDocumentPdfDriver $driver, ezcDocumentPcssStyleInferencer $styles, ezcDocumentPdfOptions $options = null )
    {
        $this->driver         = $driver;
        $this->styles         = $styles;
        $this->options        = $options;
        $this->errorReporting = $options !== null ? $options->errorReporting : 15;
    }

    /**
     * Render a block level element.
     *
     * Renders a block level element by applzing margin and padding and
     * recursing to all nested elements.
     *
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfHyphenator $hyphenator 
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $block 
     * @param ezcDocumentPdfMainRenderer $mainRenderer 
     * @return bool
     */
    public function renderNode( ezcDocumentPdfPage $page, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $block, ezcDocumentPdfMainRenderer $mainRenderer )
    {
        $this->hyphenator = $hyphenator;
        $this->tokenizer  = $tokenizer;

        $styles         = $this->styles->inferenceFormattingRules( $block );
        $page->y       += $styles['padding']->value['top'] +
                          $styles['margin']->value['top'];
        $page->xOffset += $styles['padding']->value['left'] +
                          $styles['margin']->value['left'];
        $page->xReduce += $styles['padding']->value['right'] +
                          $styles['margin']->value['right'];

        $this->mainRenderer = $mainRenderer;
        $this->processTable( $page, $hyphenator, $tokenizer, $block, $mainRenderer );

        $page->y       += $styles['padding']->value['bottom'] +
                          $styles['margin']->value['bottom'];
        $page->xOffset -= $styles['padding']->value['left'] +
                          $styles['margin']->value['left'];
        $page->xReduce -= $styles['padding']->value['right'] +
                          $styles['margin']->value['right'];
        return true;
    }

    /**
     * Calculate text width.
     *
     * Calculate the available horizontal space for texts depending on the
     * page layout settings.
     *
     * @param ezcDocumentPdfPage $page
     * @param ezcDocumentLocateableDomElement $text
     * @return float
     */
    public function calculateTextWidth( ezcDocumentPdfPage $page, ezcDocumentLocateableDomElement $text )
    {
        return $this->cellWidth;
    }

    /**
     * Get next rendering position.
     *
     * If the current space has been exceeded this method calculates
     * a new rendering position, optionally creates a new page for
     * this, or switches to the next column. The new rendering;
     * position is set on the returned page object.
     *
     * As the parameter you need to pass the required width for the object to
     * place on the page.
     *
     * @param float $move
     * @param float $width
     * @return ezcDocumentPdfPage
     */
    public function getNextRenderingPosition( $move, $width )
    {
        // Close all table cells
        $oldPage = $this->driver->currentPage();
        foreach ( $this->cellBoxes as $nr => $cell )
        {
            if ( isset( $this->additionalBorders[$oldPage->orderedId] ) &&
                 isset( $this->additionalBorders[$oldPage->orderedId][$nr] ) )
            {
                continue;
            }

            $cell['box']->height = ( $oldPage->startY + $oldPage->innerHeight ) - $cell['box']->y;
            $this->additionalBorders[$oldPage->orderedId][$nr] = array( clone $cell['box'], $cell['styles'], false );
        }

        // Close table border
        if ( !isset( $this->additionalBorders[$oldPage->orderedId] ) ||
             !isset( $this->additionalBorders[$oldPage->orderedId]['table'] ) )
        {
            $this->tableBox['box']->height = ( $oldPage->startY + $oldPage->innerHeight ) - $this->tableBox['box']->y;
            $this->additionalBorders[$oldPage->orderedId]['table'] = array( clone $this->tableBox['box'], $this->tableBox['styles'], false, false );
        }

        // Call parent handler to get next rendering position
        $page = parent::getNextRenderingPosition( $move, $width );

        if ( $page === $oldPage )
        {
            $this->lastPageForCell = $page;
            return $page;
        }

        // Update all boxes to start on top of the new page
        foreach ( $this->cellBoxes as $cell )
        {
            $cell['box']->y = $page->y;
            $this->renderTopBorder( $cell['styles'], $cell['box'] );
        }

        // Maintain horizontal rendering position
        $page->x = $oldPage->x;
        $page->y = $page->startY;

        $this->tableBox['box']->y = $page->y;
        return $this->lastPageForCell = $page;
    }

    /**
     * Render a single table cell.
     * 
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfHyphenator $hyphenator 
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $cell 
     * @param array $styles 
     * @param ezcDocumentPdfBoundingBox $space 
     * @param float $start 
     * @param float $width 
     */
    protected function renderCell( ezcDocumentPdfPage $page, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $cell, array $styles, ezcDocumentPdfBoundingBox $space, $start, $width )
    {
        $styles         = $this->styles->inferenceFormattingRules( $cell );
        $this->covered  = array();

        // Mark space used, which will be covered by the other table cells
        if ( $start > 0 )
        {
            $this->covered[] = $page->setCovered( new ezcDocumentPdfBoundingBox(
                $space->x,
                $space->y,
                $space->width * $start,
                $page->height
            ) );
        }

        if ( $start + $width < 1 )
        {
            $this->covered[] = $page->setCovered( new ezcDocumentPdfBoundingBox(
                $space->x + $space->width * ( $start + $width ),
                $space->y,
                $space->width * ( 1 - ( $start + $width ) ),
                $page->height
            ) );
        }

        // Evaluate available space for box
        $page->x = $space->x + $start * $space->width;
        $page->y = $space->y;

        if ( ( $box = $this->evaluateAvailableBoundingBox( $page, $styles, $width * $space->width ) ) === false )
        {
            $page = $this->getNextRenderingPosition( 0, $space->width );
            $box  = $this->evaluateAvailableBoundingBox( $page, $styles, $width * $space->width );
        }

        $this->cellBoxes[] = array(
            'box'     => $box,
            'styles'  => $styles,
        );
        $this->cellWidth = $box->width;
        $this->renderTopBorder( $styles, $box );

        // Render cell contents
        $page->x = $box->x;
        $page->y = $box->y;
        $this->lastPageForCell = $page;
        $this->process( $cell );
        $page->x = $space->x;

        foreach ( $this->covered as $nr => $id )
        {
            $page->uncover( $id );
            unset( $this->covered[$nr] );
        }

        return array( $this->lastPageForCell->orderedId, $this->lastPageForCell->y );
    }

    /**
     * Render top border.
     *
     * Render the top border of the given space
     * 
     * @param array $styles 
     * @param ezcDocumentPdfBoundingBox $space 
     */
    protected function renderTopBorder( array $styles, ezcDocumentPdfBoundingBox $space )
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

        if ( $styles['border']->value['top']['width'] > 0 )
        {
            $this->driver->drawPolyline(
                array( $topLeft, $topRight ),
                $styles['border']->value['top']['color'],
                $styles['border']->value['top']['width']
            );
        }
    }

    /**
     * Set cell box covered.
     *
     * Mark rendered space as convered on the page.
     *
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfBoundingBox $space 
     * @param array $styles 
     */
    protected function setCellCovered( ezcDocumentPdfPage $page, ezcDocumentPdfBoundingBox $space, array $styles )
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
            $styles['border']->value['top']['width'] +
            $styles['margin']->value['top'];
        $page->setCovered( $space );
        $page->y += $space->height;
    }

    /**
     * Process to render the table into its boundings.
     * 
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfHyphenator $hyphenator 
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $block 
     * @param ezcDocumentPdfMainRenderer $mainRenderer 
     */
    protected function processTable( ezcDocumentPdfPage $page, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $block, ezcDocumentPdfMainRenderer $mainRenderer )
    {
        $tableColumnWidths = $this->options->tableColumnWidthCalculator->estimateWidths( $block );
        $styles            = $this->styles->inferenceFormattingRules( $block );
        $renderWidth       = $mainRenderer->calculateTextWidth( $page, $block );

        $space = $this->evaluateAvailableBoundingBox( $page, $styles, $renderWidth );
        $this->tableBox = array(
            'box'     => $box = clone $space,
            'styles'  => $styles,
        );
        $this->renderTopBorder( $styles, $box );

        $xpath     = new DOMXPath( $block->ownerDocument );
        $xPosition = $page->x;
        foreach ( $xpath->query( './*/*/*[local-name() = "row"] | ./*/*[local-name() = "row"]', $block ) as $rowNr => $row )
        {
            $xOffset         = 0;
            $this->cellBoxes = array();
            $positions       = array();
            $pageStartId     = $page->orderedId;
            $lastPage        = array();
            foreach ( $xpath->query( './*[local-name() = "entry"]', $row ) as $nr => $cell )
            {
                list( $pageId, $position ) = $this->renderCell( $page, $hyphenator, $tokenizer, $cell, $styles, $space, $xOffset, $tableColumnWidths[$nr] );
                $positions[$pageId][]      = $position;
                $xOffset                  += $tableColumnWidths[$nr];
                $lastPage[$pageId]         = $this->lastPageForCell;

                // Go back to page, where each cell in the row shoudl start the 
                // rendering
                $this->driver->selectPage( $pageStartId );
            }

            // Close borders
            foreach ( $this->additionalBorders as $page => $borders )
            {
                $this->driver->selectPage( $page );
                foreach ( $borders as $border )
                {
                    call_user_func_array( array( $this, 'renderBoxBorder' ), $border );
                }
            }
            $this->additionalBorders = array();

            $lastPageId = max( array_keys( $positions ) );
            $page       = $lastPage[$lastPageId];

            // Forward page to last page used during cell rendering
            $this->driver->selectPage( $lastPageId );

            $page->x = $xPosition;
            foreach ( $this->cellBoxes as $cell )
            {
                $cell['box']->height = max( $positions[$lastPageId] ) - $cell['box']->y;
                $this->renderBoxBorder( $cell['box'], $cell['styles'], false );
                $this->setCellCovered( $page, $cell['box'], $styles );
            }

            // Set page->y again, since setBoxCovered() increased it, which we 
            // do not want in this case.
            $page->y = max( $positions[$lastPageId] );

            $space->y = $page->y = $page->y +
                $cell['styles']['padding']->value['bottom'] +
                $cell['styles']['border']->value['bottom']['width'] +
                $cell['styles']['margin']->value['bottom'];
        }

        $box->height = $space->y - $box->y;
        $this->renderBoxBorder( $box, $styles, false );
    }
}

?>
