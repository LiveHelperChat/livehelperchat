<?php
/**
 * File containing the ezcDocumentPdfPage class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * PDF page class
 *
 * Class containing context information about a single rendered page.
 *
 * It especially encodes information about already covered / blocked areas on
 * one PDF page, and offers methods to check if a new content block fits on the
 * page an, where it does fit on the page.
 *
 * The testing for new boxes, where they fit on the page and in which 
 * dimensions they fit, is implemented in the testFitRectangle() method. The 
 * method implementation is optimized for speed, since it is called *a lot* 
 * during the rendering process.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfPage implements ezcDocumentLocateable
{
    /**
     * Already covered areas, given as an arrays of ezcDocumentPdfBoundingBox
     * objects.
     *
     * @var array
     */
    protected $covered = array();

    /**
     * Current transaction
     *
     * @var mixed
     */
    protected $transaction = 0;

    /**
     * Stored drawing positions for each transaction.
     *
     * @var array
     */
    protected $storedPositions = array();

    /**
     * Page number
     *
     * @var int
     */
    protected $pageNumber;

    /**
     * Current horizontal rendering position on page
     *
     * @var float
     */
    public $x;

    /**
     * Current vertical rendering position on page
     *
     * @var float
     */
    public $y;

    /**
     * Horizontal offset in a column
     * 
     * @var float
     */
    public $xOffset;

    /**
     * Horizontal width reduction in a column
     * 
     * @var float
     */
    public $xReduce;

    /**
     * X coordinate of rendering start position
     *
     * @var float
     */
    public $startX;

    /**
     * Y coordinate of rendering start position
     *
     * @var float
     */
    public $startY;

    /**
     * Width of current page - given in millimeters
     *
     * @var float
     */
    protected $width;

    /**
     * Height of current page - given in millimeters
     *
     * @var float
     */
    protected $height;

    /**
     * Inner width of current page - given in millimeters
     *
     * @var float
     */
    protected $innerWidth;

    /**
     * Inner height of current page - given in millimeters
     *
     * @var float
     */
    protected $innerHeight;

    /**
     * ID of the page.
     *
     * This ID defines an order on the pages. It is *not* sequential, there
     * might always be holes in the sequence.
     *
     * But a page creted later in the rendering process will always have a 
     * higher number then the pages before.
     * 
     * @var int
     */
    protected $orderedId;

    /**
     * Global static ID provider to dertermine page creation order. This is 
     * required for the $orderedId property.
     *
     * @var int
     */
    static protected $idCounter = 1;

    /**
     * Array of pages sizes
     *
     * Associates known page size identifiers the actual size in millimeters.
     *
     * @var array
     */
    protected static $pageSizes = array(
        'A0'        => array( 841, 1189 ),
        'A1'        => array( 594, 841 ),
        'A2'        => array( 420, 594 ),
        'A3'        => array( 297, 420 ),
        'A4'        => array( 210, 297 ),
        'A5'        => array( 148, 210 ),
        'A6'        => array( 105, 148 ),
        'A7'        => array( 74, 105 ),
        'A8'        => array( 52, 74 ),
        'A9'        => array( 37, 52 ),
        'A10'       => array( 26, 37 ),
        'B0'        => array( 1000, 1414 ),
        'B1'        => array( 707, 1000 ),
        'B2'        => array( 500, 707 ),
        'B3'        => array( 353, 500 ),
        'B4'        => array( 250, 353 ),
        'B5'        => array( 176, 250 ),
        'B6'        => array( 125, 176 ),
        'B7'        => array( 88, 125 ),
        'B8'        => array( 62, 88 ),
        'B9'        => array( 44, 62 ),
        'B10'       => array( 31, 44 ),
        'C0'        => array( 917, 1297 ),
        'C1'        => array( 648, 917 ),
        'C2'        => array( 458, 648 ),
        'C3'        => array( 324, 458 ),
        'C4'        => array( 229, 324 ),
        'C5'        => array( 162, 229 ),
        'C6'        => array( 114, 162 ),
        'C7'        => array( 81, 114 ),
        'C8'        => array( 57, 81 ),
        'C9'        => array( 40, 57 ),
        'C10'       => array( 28, 40 ),
        'RA0'       => array( 860, 1220 ),
        'RA1'       => array( 610, 860 ),
        'RA2'       => array( 430, 610 ),
        'RA3'       => array( 305, 430 ),
        'RA4'       => array( 215, 305 ),
        'SRA0'      => array( 900, 1280 ),
        'SRA1'      => array( 640, 900 ),
        'SRA2'      => array( 450, 640 ),
        'SRA3'      => array( 320, 450 ),
        'SRA4'      => array( 225, 320 ),
        'LETTER'    => array( 215.9, 279.4 ),
        'LEGAL'     => array( 215.9, 355.6 ),
        'EXECUTIVE' => array( 184.1, 266.7 ),
        'FOLIO'     => array( 215.9, 330.2 ),
        'TEST'      => array( 100, 100 ),
    );

    /**
     * Construct new fresh page from its dimensions
     *
     * @param int $pageNumber
     * @param float $width
     * @param float $height
     * @param mixed $innerWidth
     * @param mixed $innerHeight
     * @return void
     */
    public function __construct( $pageNumber, $width, $height, $innerWidth = null, $innerHeight = null )
    {
        $this->pageNumber  = (int) $pageNumber;
        $this->width       = (float) $width;
        $this->height      = (float) $height;
        $this->innerWidth  = $innerWidth === null ? $this->width : (float) $innerWidth;
        $this->innerHeight = $innerHeight === null ? $this->height : (float) $innerHeight;
        $this->orderedId   = ++self::$idCounter;
    }

    /**
     * Create from user readable soze specification
     *
     * Create page from common page size abbreviations, like "A4" and page
     * orientation.
     *
     * @param int $pageNumber
     * @param mixed $size
     * @param mixed $orientation
     * @param array $margin
     * @param array $padding
     * @return ezcDocumentPdfPage
     */
    public static function createFromSpecification( $pageNumber, $size, $orientation, array $margin, array $padding )
    {
        if ( !isset( self::$pageSizes[$size] ) )
        {
            throw new ezcBaseValueException( "page-size", $size, implode( ', ', self::$pageSizes ) );
        }

        // Calculate border sizes, depending on assigned margin and
        // padding
        $topBorder    = $margin['top'] + $padding['top'];
        $leftBorder   = $margin['left'] + $padding['left'];
        $bottomBorder = $margin['bottom'] + $padding['bottom'];
        $rightBorder  = $margin['right'] + $padding['right'];

        switch ( $orientation )
        {
            case 'landscape':
                $page = new ezcDocumentPdfPage(
                    $pageNumber,
                    $width  = self::$pageSizes[$size][1] + $margin['left'] + $margin['right'],
                    $height = self::$pageSizes[$size][0] + $margin['top'] + $margin['bottom'],
                    $width - $leftBorder - $rightBorder,
                    $height - $topBorder - $bottomBorder
                );
                break;
            case 'portrait':
                $page = new ezcDocumentPdfPage(
                    $pageNumber,
                    $width  = self::$pageSizes[$size][0] + $margin['left'] + $margin['right'],
                    $height = self::$pageSizes[$size][1] + $margin['top'] + $margin['bottom'],
                    $width - $leftBorder - $rightBorder,
                    $height - $topBorder - $bottomBorder
                );
                break;
            default:
                throw new ezcBaseValueException( "page-orientation", $orientation, 'landscape or portrait' );
        }

        // Set cover boxes for areas covered by padding and margin
        $page->setCovered( new ezcDocumentPdfBoundingBox( 0, 0, $width, $topBorder ) );
        $page->setCovered( new ezcDocumentPdfBoundingBox( 0, 0, $leftBorder, $height ) );
        $page->setCovered( new ezcDocumentPdfBoundingBox( 0, $height - $bottomBorder, $width, $bottomBorder ) );
        $page->setCovered( new ezcDocumentPdfBoundingBox( $width - $rightBorder, 0, $rightBorder, $height ) );

        // Update rendering start position
        $page->x = $page->startX = $leftBorder;
        $page->y = $page->startY = $topBorder;

        return $page;
    }

    /**
     * Wrapper for virtual property access
     *
     * @param string $property
     * @return mixed
     */
    public function __get( $property )
    {
        switch ( $property )
        {
            case 'number':
                return $this->pageNumber;
            case 'startX':
                return $this->startX;
            case 'startY':
                return $this->startY;
            case 'width':
                return $this->width;
            case 'height':
                return $this->height;
            case 'innerWidth':
                return $this->innerWidth;
            case 'innerHeight':
                return $this->innerHeight;
            case 'orderedId':
                return $this->orderedId;
        }
    }

    /**
     * Start a new transaction sequence
     *
     * Start a new transaction, which will group all covered areas, until the
     * next transaction is started. This methods takes and returns an
     * identifier for this transaction, which can be used to commit this
     * transaction, or revert everything since (including) this this
     * transaction.
     *
     * @param mixed $transaction
     * @return mixed
     */
    public function startTransaction( $transaction )
    {
        $this->covered[$this->transaction = $transaction] = array();
        $this->storedPositions[$this->transaction] = array( $this->x, $this->y );
        return $this->transaction;
    }

    /**
     * Revert transaction
     *
     * Revert all transactions after the specified (including the specified)
     * transaction.
     *
     * @param mixed $transaction
     * @return void
     */
    public function revert( $transaction )
    {
        if ( !isset( $this->covered[$transaction] ) )
        {
            return false;
        }

        $remove = false;
        foreach ( $this->covered as $id => $areas )
        {
            if ( !$remove &&
                 ( $id !== $transaction ) )
            {
                continue;
            }

            $remove = true;
            unset( $this->covered[$id] );
        }

        list( $this->x, $this->y ) = $this->storedPositions[$transaction];

        return true;
    }

    /**
     * Set space covered
     *
     * Append a rectangle of already covered space. This space will then not be
     * reused for any other objects on the page.
     *
     * There is no check for overlapping of covered areas in here, so that you
     * can add bounding boxes wrapping multiple already existing rectangles.
     *
     * Returns an array specifying the transaction and ID of the cover action. 
     * This tupel may be used later to call the uncover() method, to remove 
     * this coverage area again.
     *
     * @param ezcDocumentPdfBoundingBox $rectangle
     * @param mixed $id
     * @return array
     */
    public function setCovered( ezcDocumentPdfBoundingBox $rectangle, $id = null )
    {
        $this->covered[$this->transaction][] = $rectangle;
        return array( $this->transaction, count( $this->covered[$this->transaction] ) - 1 );
    }

    /**
     * Uncover area
     *
     * Uncover the area specified by the ID returned by the setCovered() 
     * method.
     *
     * Will return false, if the given ID is unknown in the transaction.
     * 
     * @param array $id 
     * @return bool
     */
    public function uncover( array $id )
    {
        if ( isset( $this->covered[$id[0]] ) &&
             isset( $this->covered[$id[0]][$id[1]] ) )
        {
            unset( $this->covered[$id[0]][$id[1]] );
            return true;
        }

        return false;
    }

    /**
     * Try to fit specified rectangle on page
     *
     * Try to find place for the specified rectangle on the curernt page. Each
     * of the parameters may be set to null, which means that this parameter
     * can be varied in dimension or value.
     *
     * If all parameters are set to a fixed value, either false will be
     * returned, if the location is already (partly) covered, or a rectangle
     * will be returned if that space is still available.
     *
     * If, for example, the yPos parameter is set to null, but all other
     * parameters are set, the box will be moved down the page, until a
     * available location could be found.
     *
     * @param mixed $xPos
     * @param mixed $yPos
     * @param mixed $width
     * @param mixed $height
     * @return mixed
     */
    public function testFitRectangle( $xPos = null, $yPos = null, $width = null, $height = null )
    {
        // Ensure requested area is within the page boundings
        if ( ( $xPos < 0 ) ||
             ( $yPos < 0 ) ||
             ( ( $xPos + $this->xOffset + $width ) > $this->width ) ||
             ( ( $yPos + $height ) > $this->height ) )
        {
            return false;
        }

        // Store aspects of passed parameters
        $moveX        = ( $xPos === null );
        $moveY        = ( $yPos === null );
        $adjustWidth  = ( $width === null );
        $adjustHeight = ( $height === null );
        $boundings    = new ezcDocumentPdfBoundingBox( $xPos, $yPos, $width, $height );

        // We do not support moving and extending in the same direction yet,
        // since this would require some sort of backtracking.
        if ( ( $moveX && $adjustWidth ) ||
             ( $moveY && $adjustHeight ) )
        {
            throw new ezcBaseFunctionalityNotSupportedException(
                'Moving and extensions ins same direction',
                'Backtracking would be required'
            );
        }

        // Start width adjusting with full page width, will be reduced later
        // based on found boxes.
        if ( $adjustWidth )
        {
            $boundings->width = $this->width - $boundings->x - $this->xOffset - $this->xReduce;
        }

        // Start height adjusting with full page height, will be reduced later
        // based on found boxes.
        if ( $adjustHeight )
        {
            $boundings->height = $this->height - $boundings->y;
        }

        // Test all covered areas for intersections with the given bounding box
        foreach ( $this->covered as $transaction => $areas )
        {
            foreach ( $areas as $covered )
            {
                // These variables indicate which bounding box checks evaluated to
                // true, so we can handle bounding box modififactions according to
                // this.
                $xOut = 0;
                $yOut = 0;
                // Do NOT change the test order.
                if ( ( // Test for left coordinate in covering boundings
                       ( $xOut |= ( ( $boundings->x > $covered->x ) &&
                                    ( $boundings->x < ( $covered->x + $covered->width ) ) ) << 1 ) ||
                       // Test for right coordinate in covering boundings
                       ( $xOut |= ( ( ( $boundings->x + $boundings->width ) > $covered->x ) &&
                                    ( ( $boundings->x + $boundings->width ) < ( $covered->x + $covered->width ) ) ) << 2 ) ||
                       // Test if coordinates outer wrap coverings
                       ( $xOut |= ( ( $boundings->x <= $covered->x ) &&
                                    ( ( $boundings->x + $boundings->width ) >= ( $covered->x + $covered->width ) ) ) << 3 )
                     ) &&
                     ( // Test for top coordinate in covering boundings
                       ( $yOut |= ( ( $boundings->y > $covered->y ) &&
                                    ( $boundings->y < ( $covered->y + $covered->height ) ) ) << 1 ) ||
                       // Test for bottom coordinate in covering boundings
                       ( $yOut |= ( ( ( $boundings->y + $boundings->height ) > $covered->y ) &&
                                    ( ( $boundings->y + $boundings->height ) < ( $covered->y + $covered->height ) ) ) << 2 ) ||
                       // Test if coordinates outer wrap coverings
                       ( $yOut |= ( ( $boundings->y <= $covered->y ) &&
                                    ( ( $boundings->y + $boundings->height ) >= ( $covered->y + $covered->height ) ) ) << 3 )
                     ) )
                {
                    // Adjust bounding box width, if only the right coordinate hit
                    // the covered area.
                    if ( $adjustWidth &&
                         ( $xOut & 12 ) )
                    {
                        $boundings->width = $covered->x - $boundings->x;
                    }

                    // Adjust bounding box width, if only the right coordinate hit
                    // the covered area.
                    if ( $adjustHeight &&
                         ( $yOut & 12 ) )
                    {
                        $boundings->height = $covered->y - $boundings->y;
                    }

                    // If the width or height has been adjusted, we did not hit any
                    // covered area with the starting coordinates because of the
                    // test order in the if statement above. We can safely continue
                    // to check the next covering area. We cannot do the continue
                    // in one of the blocks above, because we might need to modify
                    // both.
                    if ( ( $adjustWidth &&
                           ( $xOut & 12 ) ) ||
                         ( $adjustHeight &&
                           ( $yOut & 12 ) ) )
                    {
                        continue;
                    }

                    if ( !$moveX && !$moveY )
                    {
                        // We hit something and may not move or adjust the box -
                        // break.
                        return false;
                    }
                    elseif ( $moveX && $moveY )
                    {
                        // Move in the direction where less movement is required.
                        // This might be imporved by additionally checking already
                        // reached page boundings...
                        $xMovement = ( $covered->x + $covered->width  ) - $boundings->x;
                        $yMovement = ( $covered->y + $covered->height ) - $boundings->y;
                        $boundings->x += $xMovement > $yMovement ? 0 : $xMovement;
                        $boundings->y += $yMovement > $xMovement ? 0 : $yMovement;
                    }
                    elseif ( $moveX )
                    {
                        $boundings->x = $covered->x + $covered->width;
                    }
                    elseif ( $moveY )
                    {
                        $boundings->y = $covered->y + $covered->height;
                    }
                }
            }
        }

        // Recheck moved bounding box, to check if it still fits page
        // boundings, and has not been moved into any covered areas at the
        // bottom right side of the page.
        if ( $moveX || $moveY )
        {
            return $this->testFitRectangle( $boundings->x, $boundings->y, $boundings->width, $boundings->height );
        }

        $boundings->x += $this->xOffset;
        return $boundings;
    }

    /**
     * Get elements location ID
     *
     * Return the elements location ID, based on the factors described in the
     * class header.
     *
     * @return string
     */
    public function getLocationId()
    {
        return '/page' .
            '.' . ( $this->pageNumber % 2 ? 'left' : 'right' ) .
            '#page_' . $this->pageNumber;
    }
}
?>
