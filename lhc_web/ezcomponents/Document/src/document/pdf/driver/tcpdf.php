<?php
/**
 * File containing the ezcDocumentPdfTcpdfDriver class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Pdf driver based on TCPDF
 * 
 * TCPDF is a PHP based PDF renderer, originally based on FPDF and available at
 * http://tcpdf.org.
 *
 * The TCPDF class has to be loaded before this driver can be used. TCPDF has
 * some bad coding practices, like:
 *  - Throws lots of warnings and notices, which you might want to silence by
 *    temporarily changing the error reporting level
 *  - Reads and writes several global variables, which might or might not
 *    interfere with your application code
 *  - Uses eval() in several places, which results in non-cacheable OP-Codes.
 *
 * On the other hand TCPDF can handle UTF-8 just fine, and therefore supports a 
 * braod range of unicode characters.
 *
 * The driver can be used by setting the respective option on the
 * PDF document wrapper, you need to download and load the TCPDF implementation 
 * yourself.
 *
 * <code>
 *  // Load the docbook document and create a PDF from it
 *  $pdf = new ezcDocumentPdf();
 *
 *  include '/path/to/tcpdf.php';
 *  $pdf->options->driver = new ezcDocumentPdfTcpdfDriver();
 *  $pdf->createFromDocbook( $docbook );
 *  file_put_contents( __FILE__ . '.pdf', $pdf );
 * </code>
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentPdfTcpdfDriver extends ezcDocumentPdfDriver
{
    /**
     * Tcpdf Document instance
     *
     * @var Tcpdf
     */
    protected $document;

    /**
     * Page instances, given as an array, indexed by their page number starting
     * with 0.
     *
     * @var array
     */
    protected $pages;

    /**
     * Internal targets
     *
     * Target objects for all rendered internal targets, to be used when
     * rendering the internal links at the end of the processing.
     * 
     * @var array
     */
    protected $internalTargets = array();

    /**
     * Internal targets
     *
     * Link identifiers groupd by target names for all links in the document,
     * which should be associated with the target at the end of the document
     * rendering.
     * 
     * @var array
     */
    protected $internalLinkSources = array();

    /**
     * Array with fonts, and their equivalents for bold and italic markup. This
     * array will be extended when loading new fonts, but contains the builtin
     * fonts by default.
     *
     * The fourth value for each font is bold + oblique, the index is the
     * bitwise and combination of the repective combinations. Each font MUST
     * have at least a value for FONT_PLAIN assigned.
     *
     * @var array
     */
    protected $fonts = array(
        'sans-serif' => array(
            self::FONT_PLAIN   => 'helvetica',
            self::FONT_BOLD    => 'helveticab',
            self::FONT_OBLIQUE => 'helveticai',
            3                  => 'helveticabi',
        ),
        'serif' => array(
            self::FONT_PLAIN   => 'times',
            self::FONT_BOLD    => 'timesb',
            self::FONT_OBLIQUE => 'timesi',
            3                  => 'timesbi',
        ),
        'monospace' => array(
            self::FONT_PLAIN   => 'courier',
            self::FONT_BOLD    => 'courierb',
            self::FONT_OBLIQUE => 'courieri',
            3                  => 'courierbi',
        ),
        'Symbol' => array(
            self::FONT_PLAIN   => 'symbol',
        ),
        'ZapfDingbats' => array(
            self::FONT_PLAIN   => 'zapfdingbats',
        ),
    );

    /**
     * Reference to the page currently rendered on
     *
     * @var haruPage
     */
    protected $currentpage;

    /**
     * Mapping of native permission flags, to Haru permission flags
     * 
     * @var array
     */
    protected $permissionMapping = array(
        ezcDocumentPdfOptions::EDIT      => 'annot-forms',
        ezcDocumentPdfOptions::PRINTABLE => 'print',
        ezcDocumentPdfOptions::COPY      => 'copy',
        ezcDocumentPdfOptions::MODIFY    => 'modify',
    );

    /**
     * Name and style of default font / currently used font
     *
     * @var array
     */
    protected $currentFont = array(
        'name'  => 'sans-serif',
        'style' => self::FONT_PLAIN,
        'size'  => 12,
    );

    /**
     * Construct driver
     *
     * Creates a new document instance maintaining all document context.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Do nothing in here, we can only instantiate the document on first
        // page creation, because we do not know about the page format
        // beforehand.
        $this->pages    = array();
        $this->document = null;
    }

    /**
     * Initialize haru documents
     * 
     * @return void
     */
    protected function initialize()
    {
        // Sorry for this, but we need it to prevent from warnings in TCPDF:
        $GLOBALS['utf8tolatin'] = array();

        // Create the basic document, which dimensions should not matter, since
        // we specify this at each page creation separetely.
        $this->document = new TCPDF(
            'P',  // Portrait size, which should notter sinc we provide the exact size
            'mm', // Units used for all values, except font sizes
            array( 1000, 1000 ),
            true,   // Use unicode
            'UTF-8'
        );

        // We do this ourselves
        $this->document->setAutoPageBreak( false );
        $this->document->setMargins( 0, 0 );
        $this->document->setCreator( 'eZ Components - Document 1.3.1' );
        $this->document->setPrintHeader( false );
        $this->document->setPrintFooter( false );
        $this->document->setCompression( $this->options->compress );

        if ( $this->options->ownerPassword !== null ) 
        {
            $this->setPermissions( $this->options );
        }

        $this->document->setFont(
            $this->fonts[$this->currentFont['name']][self::FONT_PLAIN],
            '', // Style
            $this->currentFont['size']
        );
    }

    /**
     * Set permissions for PDF document
     * 
     * @param int $permissions 
     * @return void
     */
    protected function setPermissions( ezcDocumentPdfOptions $options )
    {
        $flag = array();
        foreach ( $this->permissionMapping as $own => $tcpdf )
        {
            if ( $options->permissions & $own )
            {
                $flag[] = $tcpdf;
            }
        }
        $this->document->setProtection( $flag, $this->options->userPassword, $this->options->ownerPassword );
    }

    /**
     * Create a new page
     *
     * Create a new page in the PDF document with the given width and height.
     *
     * @param float $width
     * @param float $height
     * @return void
     */
    public function createPage( $width, $height )
    {
        if ( $this->document === null )
        {
            $this->initialize();
        }

        // Create a new page, and create a reference in the pages array
        $this->document->AddPage( 'P', array( $width, $height ) );
        $this->pages[] = $this->document->getPage();
    }

    /**
     * Try to set font
     *
     * Stays with the old font, if the newly specified font is not available.
     *
     * If the font does not support the given style, it falls back to the style
     * used beforehand, and if this is also not support the plain style will be
     * used.
     *
     * @param string $name
     * @param int $style
     * @return void
     */
    public function trySetFont( $name, $style )
    {
        if ( $this->document === null )
        {
            $this->initialize();
        }

        // Just du no use new font, if it is unknown
        if ( !isset( $this->fonts[$name] ) )
        {
            throw new ezcDocumentInvalidFontException( $name );
        }

        // Style fallback
        if ( !isset( $this->fonts[$name][$style] ) )
        {
            $style = isset( $this->fonts[$name][$this->currentFont['style']] ) ? $this->currentFont['style'] : self::FONT_PLAIN;
        }

        // Create and use font on current page
        $this->document->setFont(
            $this->fonts[$name][self::FONT_PLAIN],
            ( $style & self::FONT_BOLD      ? 'B' : '' ) .
            ( $style & self::FONT_OBLIQUE   ? 'I' : '' )
        );

        $this->currentFont = array(
            'name'  => $name,
            'style' => $style,
            'size'  => $this->currentFont['size'],
        );
    }

    /**
     * Set text formatting option
     *
     * Set a text formatting option. The names of the options are the same used
     * in the PCSS files and need to be translated by the driver to the proper
     * backend calls.
     *
     *
     * @param string $type
     * @param mixed $value
     * @return void
     */
    public function setTextFormatting( $type, $value )
    {
        switch ( $type )
        {
            case 'font-style':
                if ( ( $value === 'oblique' ) ||
                     ( $value === 'italic' ) )
                {
                    $this->trySetFont(
                        $this->currentFont['name'],
                        $this->currentFont['style'] | self::FONT_OBLIQUE
                    );
                }
                else
                {
                    $this->trySetFont(
                        $this->currentFont['name'],
                        $this->currentFont['style'] & ~self::FONT_OBLIQUE
                    );
                }
                break;

            case 'font-weight':
                if ( ( $value === 'bold' ) ||
                     ( $value === 'bolder' ) )
                {
                    $this->trySetFont(
                        $this->currentFont['name'],
                        $this->currentFont['style'] | self::FONT_BOLD
                    );
                }
                else
                {
                    $this->trySetFont(
                        $this->currentFont['name'],
                        $this->currentFont['style'] & ~self::FONT_BOLD
                    );
                }
                break;

            case 'font-family':
                $this->trySetFont( $value, $this->currentFont['style'] );
                break;

            case 'font-size':
                $this->currentFont['size'] = ezcDocumentPcssMeasure::create( $value )->get( 'pt' );
                $this->document->setFontSize( $this->currentFont['size'] );
                break;

            case 'color':
                $this->document->setTextColor(
                    $value['red'] * 255,
                    $value['green'] * 255,
                    $value['blue'] * 255
                );

            default:
                // @todo: Error reporting.
        }
    }

    /**
     * Calculate the rendered width of the current word
     *
     * Calculate the width of the passed word, using the currently set text
     * formatting options.
     *
     * @param string $word
     * @return float
     */
    public function calculateWordWidth( $word )
    {
        if ( $this->document === null )
        {
            $this->initialize();
        }

        return $this->document->GetStringWidth( $word );
    }

    /**
     * Get current line height
     *
     * Return the current line height in millimeter based on the current font
     * and text rendering settings.
     *
     * @return float
     */
    public function getCurrentLineHeight()
    {
        return ezcDocumentPcssMeasure::create( $this->currentFont['size'] . 'pt' )->get();
    }

    /**
     * Draw word at given position
     *
     * Draw the given word at the given position using the currently set text
     * formatting options.
     *
     * The coordinate specifies the left bottom edge of the words bounding box.
     *
     * @param float $x
     * @param float $y
     * @param string $word
     * @return void
     */
    public function drawWord( $x, $y, $word )
    {
        $size = ezcDocumentPcssMeasure::create( $this->currentFont['size'] . 'pt' )->get();
        $this->document->setXY( $x, (float) round( $y - $size, 4 ) );
        $this->document->Write( $size, $word );
    }

    /**
     * Draw image
     *
     * Draw image at the defined position. The first parameter is the
     * (absolute) path to the image file, and the second defines the type of
     * the image. If the driver cannot handle this aprticular image type, it
     * should throw an exception.
     *
     * The further parameters define the location where the image should be
     * rendered and the dimensions of the image in the rendered output. The
     * dimensions do not neccesarily match the real image dimensions, and might
     * require some kind of scaling inside the driver depending on the used
     * backend.
     *
     * @param string $file
     * @param string $type
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @return void
     */
    public function drawImage( $file, $type, $x, $y, $width, $height )
    {
        switch ( $type )
        {
            case 'image/png':
                $type = 'PNG';
                break;
            case 'image/jpeg':
                $type = 'JPEG';
                break;
            default:
                throw new ezcBaseFunctionalityNotSupportedException( $type, 'Image type not supported by TCPDF' );
        }

        $this->document->Image( $file, $x, $y, $width, $height, $type );
    }

    /**
     * Transform points array into a TCPDF points array.
     * 
     * @param array $points 
     * @return array
     */
    protected function getPointsArray( array $points )
    {
        $tPoints = array();
        foreach ( $points as $point )
        {
            $tPoints[] = $point[0];
            $tPoints[] = $point[1];
        }

        return $tPoints;
    }

    /**
     * Draw a fileld polygon
     *
     * Draw any filled polygon, filled using the defined color. The color
     * should be passed as an array with the keys "red", "green", "blue" and
     * optionally "alpha". Each key should have a value between 0 and 1
     * associated.
     *
     * The polygon itself is specified as an array of two-tuples, specifying
     * the x and y coordinate of the point.
     * 
     * @param array $points 
     * @param array $color 
     * @return void
     */
    public function drawPolygon( array $points, array $color )
    {
        $this->document->polygon(
            $this->getPointsArray( $points ),
            'F', // Only filled polygon, no border
            array(), // Line style
            array(
                'r' => $color['red'] * 255,
                'g' => $color['green'] * 255,
                'b' => $color['blue'] * 255,
            )
        );
    }

    /**
     * Draw a polyline
     *
     * Draw any non-filled polygon, filled using the defined color. The color
     * should be passed as an array with the keys "red", "green", "blue" and
     * optionally "alpha". Each key should have a value between 0 and 1
     * associated.
     *
     * The polyline itself is specified as an array of two-tuples, specifying
     * the x and y coordinate of the point.
     *
     * The thrid parameter defines the width of the border and the last
     * parameter may optionally be set to false to not close the polygon (draw
     * another line from the last point to the first one).
     * 
     * @param array $points 
     * @param array $color 
     * @param float $width 
     * @param bool $close 
     * @return void
     */
    public function drawPolyline( array $points, array $color, $width, $close = true )
    {
        $style = array(
            'width' => $width,
            'color' => array(
                'r' => $color['red'] * 255,
                'g' => $color['green'] * 255,
                'b' => $color['blue'] * 255,
            ),
        );

        // Draw all lines of the polygon. We cannot use the "polygon()" method
        // in TCPDF, because it _always_ closes the polygon.
        $last = null;
        foreach ( $points as $point )
        {
            if ( $last !== null )
            {
                $this->document->line( $last[0], $last[1], $point[0], $point[1], $style );
            }

            $last = $point;
        }

        // Draw closing line in polygon
        if ( $close )
        {
            $first = reset( $points );
            $this->document->line( $last[0], $last[1], $first[0], $first[1], $style );
        }
    }

    /**
     * Add an external link
     *
     * Add an external link to the rectangle specified by its top-left
     * position, width and height. The last parameter is the actual URL to link
     * to.
     * 
     * @param float $x 
     * @param float $y 
     * @param float $width 
     * @param float $height 
     * @param string $url 
     * @return void
     */
    public function addExternalLink( $x, $y, $width, $height, $url )
    {
        $this->document->link( $x, $y, $width, $height, $url );
    }

    /**
     * Add an internal link
     *
     * Add an internal link to the rectangle specified by its top-left
     * position, width and height. The last parameter is the target identifier
     * to link to.
     * 
     * @param float $x 
     * @param float $y 
     * @param float $width 
     * @param float $height 
     * @param string $target 
     * @return void
     */
    public function addInternalLink( $x, $y, $width, $height, $target )
    {
        $this->document->link( $x, $y, $width, $height, $link = $this->document->addLink() );
        
        if ( !isset( $this->internalLinkSources[$target] ) )
        {
            $this->internalLinkSources[$target] = array( $link );
        }
        else
        {
            $this->internalLinkSources[$target][] = $link;
        }
    }

    /**
     * Add an internal link target
     *
     * Add an internal link to the current page. The last parameter
     * is the target identifier.
     * 
     * @param string $id 
     * @return void
     */
    public function addInternalLinkTarget( $id )
    {
        $this->internalTargets[$id] = $this->document->getPage();
    }

    /**
     * Render internal links
     *
     * Link identifiers groupd by target names for all links in the document,
     * which should be associated with the target at the end of the document
     * rendering.
     * 
     * @return void
     */
    protected function renderInternalLinks()
    {
        foreach ( $this->internalLinkSources as $target => $links )
        {
            if ( !isset( $this->internalTargets[$target] ) )
            {
                // No target defined for these links
                continue;
            }

            foreach ( $links as $link )
            {
                $this->document->setLink( $link, 0, $this->internalTargets[$target] );
            }
        }
    }

    /**
     * Register a font
     *
     * Registers a font, which can be used by its name later in the driver. The 
     * given type is either self::FONT_PLAIN or a bitwise combination of self::FONT_BOLD 
     * and self::FONT_OBLIQUE.
     *
     * The third paramater specifies an array of pathes with references to font 
     * definition files. Multiple pathes may be specified to provide the same 
     * font using different types, because not all drivers may process all font 
     * types.
     * 
     * @param string $name 
     * @param int $type 
     * @param array $pathes 
     * @return void
     */
    public function registerFont( $name, $type, array $pathes )
    {
        throw new ezcBaseFunctionalityNotSupportedException( 'Loading fonts', 'TCPDF does not support foreign fonts.' );
    }

    /**
     * Set metadata
     *
     * Set document meta data. The meta data types are identified by a list of 
     * keys, common to PDF, like: title, author, subject, created, modified.
     *
     * The values are passed like embedded in the docbook document and might 
     * need to be reformatted.
     * 
     * @param string $key 
     * @param string $value 
     * @return void
     */
    public function setMetaData( $key, $value )
    {
        if ( $this->document === null )
        {
            $this->initialize();
        }

        switch ( $key )
        {
            case 'title':
                $this->document->setTitle( $value );
                break;
            case 'author':
                $this->document->setAuthor( $value );
                break;
            case 'subject':
                $this->document->setSubject( $value );
                break;
            case 'created':
            case 'modified':
                // Date information cannot be set with TCPDF.
                break;
        }
    }

    /**
     * Generate and return PDF
     *
     * Return the generated binary PDF content as a string.
     *
     * @return string
     */
    public function save()
    {
        $this->renderInternalLinks();

        return $this->document->Output( 'ignored', 'S' );
    }
}
?>
