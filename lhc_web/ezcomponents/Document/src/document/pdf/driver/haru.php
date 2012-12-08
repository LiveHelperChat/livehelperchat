<?php
/**
 * File containing the ezcDocumentPdfHaruDriver class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Pdf driver based on pecl/haru
 *
 * Haru is a pecl extension for PDF rendering, based on libahru, available at
 * http://libharu.org. The haru library does not yet implement support for any 
 * unicode encodings, so there will be issues with non-ASCII characters 
 * occuring in the passed texts. On the other hand it is the fastest driver 
 * currently available for PDF rendering.
 *
 * The extension can be installed using the pear command:
 *
 * <code>
 *  pear install pecl/haru
 * </code>
 *
 * The driver is currently the default driver, but can be explicitely set
 * using:
 *
 * <code>
 *  // Load the docbook document and create a PDF from it
 *  $pdf = new ezcDocumentPdf();
 *  $pdf->options->driver = new ezcDocumentPdfHaruDriver();
 *  $pdf->createFromDocbook( $docbook );
 *  file_put_contents( __FILE__ . '.pdf', $pdf );
 * </code>
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentPdfHaruDriver extends ezcDocumentPdfDriver
{
    /**
     * Haru Document instance
     *
     * @var HaruDoc
     */
    protected $document;

    /**
     * Dummy document to provide font width estimations, before we actually
     * know what kind of pages will be rendered.
     *
     * @var HaruDoc
     */
    protected $dummyDoc;

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
     * List of internal links.
     *
     * Internal links can only be rendered at the very last items in a PDF,
     * because *all* internal targets must already be known.
     * 
     * @var array
     */
    protected $pendingInternalLinks = array();

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
            self::FONT_PLAIN   => 'Helvetica',
            self::FONT_BOLD    => 'Helvetica-Bold',
            self::FONT_OBLIQUE => 'Helvetica-Oblique',
            3                  => 'Helvetica-BoldOblique',
        ),
        'serif' => array(
            self::FONT_PLAIN   => 'Times-Roman',
            self::FONT_BOLD    => 'Times-Bold',
            self::FONT_OBLIQUE => 'Times-Oblique',
            3                  => 'Times-BoldOblique',
        ),
        'monospace' => array(
            self::FONT_PLAIN   => 'Courier',
            self::FONT_BOLD    => 'Courier-Bold',
            self::FONT_OBLIQUE => 'Courier-Oblique',
            3                  => 'Courier-BoldOblique',
        ),
        'Symbol' => array(
            self::FONT_PLAIN   => 'Symbol',
        ),
        'ZapfDingbats' => array(
            self::FONT_PLAIN   => 'ZapfDingbats',
        ),
    );

    /**
     * Encodings known by libharu.
     *
     * Libharu sadly does not know any encoding which is capable of
     * representing the full unicode charset. It only knows about several
     * encodings representing subsets of it. This is a list of all available
     * encodings which will just be tried to use for input strings, mapped to
     * their iconv equivalents.
     *
     * @var array
     */
    protected $encodings = array(
        'StandardEncoding' => 'ISO_8859-1', // Assumption
        'MacRomanEncoding' => 'MAC',
        'WinAnsiEncoding'  => 'ISO_8859-1',
        'ISO8859-2'        => 'ISO_8859-2',
        'ISO8859-3'        => 'ISO_8859-3',
        'ISO8859-4'        => 'ISO_8859-4',
        'ISO8859-5'        => 'ISO_8859-5',
        'ISO8859-6'        => 'ISO_8859-6',
        'ISO8859-7'        => 'ISO_8859-7',
        'ISO8859-8'        => 'ISO_8859-8',
        'ISO8859-9'        => 'ISO_8859-9',
        'ISO8859-10'       => 'ISO_8859-10',
        'ISO8859-11'       => 'ISO_8859-11',
        'ISO8859-13'       => 'ISO_8859-12',
        'ISO8859-14'       => 'ISO_8859-13',
        'ISO8859-15'       => 'ISO_8859-14',
        'ISO8859-16'       => 'ISO_8859-16',
        'CP1250'           => 'CP1250',
        'CP1251'           => 'CP1251',
        'CP1252'           => 'CP1252',
        'CP1253'           => 'CP1253',
        'CP1254'           => 'CP1254',
        'CP1255'           => 'CP1255',
        'CP1256'           => 'CP1256',
        'CP1257'           => 'CP1257',
        'CP1258'           => 'CP1258',
        'KOI8-R'           => 'KOI8-R',
        /*
         * @todo: Find out how about the respective equivalents in inconv
         * encoding notation.
        'GB-EUC-H'         => '',
        'GB-EUC-V'         => '',
        'GBK-EUC-H'        => '',
        'GBK-EUC-V'        => '',
        'ETen-B5-H'        => '',
        'ETen-B5-V'        => '',
        '90ms-RKSJ-H'      => '',
        '90ms-RKSJ-V'      => '',
        '90msp-RKSJ-H'     => '',
        'EUC-H'            => '',
        'EUC-V'            => '',
        'KSC-EUC-H'        => '',
        'KSC-EUC-V'        => '',
        'KSCms-UHC-H'      => '',
        'KSCms-UHC-HW-H'   => '',
        'KSCms-UHC-HW-V'   => '',
         */
    );

    /**
     * Reference to the page currently rendered on
     *
     * @var haruPage
     */
    protected $currentPage;

    /**
     * Name and style of default font / currently used font
     *
     * @var array
     */
    protected $currentFont = array(
        'name'  => 'sans-serif',
        'style' => self::FONT_PLAIN,
        'size'  => 28.5,
        'font'  => null,
    );

    /**
     * Mapping of native permission flags, to Haru permission flags
     * 
     * @var array
     */
    protected $permissionMapping = array(
        ezcDocumentPdfOptions::EDIT      => HaruDoc::ENABLE_EDIT,
        ezcDocumentPdfOptions::PRINTABLE => HaruDoc::ENABLE_PRINT,
        ezcDocumentPdfOptions::COPY      => HaruDoc::ENABLE_COPY,
        ezcDocumentPdfOptions::MODIFY    => HaruDoc::ENABLE_EDIT_ALL,
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

        $this->document = null;
        $this->pages    = array();
        $this->dummyDoc = null;
    }

    /**
     * Initialize haru documents
     * 
     * @return void
     */
    protected function initialize()
    {
        $this->document = new HaruDoc();
        $this->document->setPageMode( HaruDoc::PAGE_MODE_USE_THUMBS );
        $this->document->setInfoAttr( HaruDoc::INFO_CREATOR, 'eZ Components - Document 1.3.1' );
        $this->pages = array();

        if ( $this->options->compress )
        {
            $this->document->setCompressionMode( HaruDoc::COMP_ALL );
        }

        if ( $this->options->ownerPassword !== null )
        {
            $this->document->setPassword( $this->options->ownerPassword, $this->options->userPassword );
            $this->setPermissions( $this->options->permissions );
        }

        $this->dummyDoc = new HaruDoc();
        $this->dummyDoc->addPage();
    }

    /**
     * Set permissions for PDF document
     * 
     * @param int $permissions 
     * @return void
     */
    protected function setPermissions( $permissions )
    {
        $flag = HaruDoc::ENABLE_READ;
        foreach ( $this->permissionMapping as $own => $haru )
        {
            if ( $permissions & $own )
            {
                $flag |= $haru;
            }
        }
        $this->document->setPermission( $flag );
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

        $this->pages[] = $this->currentPage = $this->document->addPage();

        $this->currentPage->setWidth( ezcDocumentPcssMeasure::create( $width )->get( 'pt' ) );
        $this->currentPage->setHeight( ezcDocumentPcssMeasure::create( $height )->get( 'pt' ) );
        $this->currentPage->setTextRenderingMode( HaruPage::FILL );

        // The current font might need to be recreated for the new page.
        $this->currentFont['font'] = null;
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
        if ( $this->currentPage )
        {
            $font = $this->document->getFont( $this->fonts[$name][$style] );
            $this->currentPage->setFontAndSize( $font, $this->currentFont['size'] );
        }
        else
        {
            $font = $this->dummyDoc->getFont( $this->fonts[$name][$style] );
            $this->dummyDoc->getCurrentPage()->setFontAndSize( $font, $this->currentFont['size'] );
        }

        $this->currentFont = array(
            'name'  => $name,
            'style' => $style,
            'size'  => $this->currentFont['size'],
            'font'  => $font,
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
        if ( $this->document === null )
        {
            $this->initialize();
        }

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

                if ( $this->currentFont['font'] !== null )
                {
                    if ( $this->currentPage )
                    {
                        $this->currentPage->setFontAndSize(
                            $this->currentFont['font'],
                            $this->currentFont['size']
                        );
                    }
                    else
                    {
                        $this->dummyDoc->getCurrentPage()->setFontAndSize(
                            $this->currentFont['font'],
                            $this->currentFont['size']
                        );
                    }
                }
                break;

            case 'color':
                if ( $this->currentPage )
                {
                    $this->currentPage->setRGBFill(
                        $value['red'],
                        $value['green'],
                        $value['blue']
                    );
                }

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

        // @todo: This removes a lot of valid characters, obviously. Haru 
        // cannot handle any Unicode encode, so we need to transform our input 
        // string in some single-byte-encoding. We use ISO-8859-1 for now, 
        // since it is common. We can either make this configurable (not kiss), 
        // or add support for Unicode in haru.
        $word = iconv( 'UTF-8', 'iso-8859-1//TRANSLIT', $word );

        // Ensure font is initialized
        if ( $this->currentFont['font'] === null )
        {
            $this->trySetFont( $this->currentFont['name'], $this->currentFont['style'] );
        }

        if ( $this->currentPage )
        {
            return ezcDocumentPcssMeasure::create( $this->currentPage->getTextWidth( $word ) . 'pt' )->get();
        }
        else
        {
            return ezcDocumentPcssMeasure::create( $this->dummyDoc->getCurrentPage()->getTextWidth( $word ) . 'pt' )->get();
        }
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
        // Ensure font is initialized
        if ( $this->currentFont['font'] === null )
        {
            $this->trySetFont( $this->currentFont['name'], $this->currentFont['style'] );
        }

        // @todo: This removes a lot of valid characters, obviously. Haru 
        // cannot handle any Unicode encode, so we need to transform our input 
        // string in some single-byte-encoding. We use ISO-8859-1 for now, 
        // since it is common. We can either make this configurable (not kiss), 
        // or add support for Unicode in haru.
        $word = iconv( 'UTF-8', 'iso-8859-1//TRANSLIT', $word );

        $this->currentPage->beginText();
        $this->currentPage->textOut(
            ezcDocumentPcssMeasure::create( $x )->get( 'pt' ),
            $this->currentPage->getHeight() - ezcDocumentPcssMeasure::create( $y )->get( 'pt' ),
            $word
        );
        $this->currentPage->endText();
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
                $image = $this->document->loadPNG( $file );
                break;
            case 'image/jpeg':
                $image = $this->document->loadJPEG( $file );
                break;
            default:
                throw new ezcBaseFunctionalityNotSupportedException( $type, 'Image type not supported by pecl/haru' );
        }

        $this->currentPage->drawImage(
            $image,
            ezcDocumentPcssMeasure::create( $x )->get( 'pt' ),
            $this->currentPage->getHeight() - ezcDocumentPcssMeasure::create( $y )->get( 'pt' ) -
                ( $height = ezcDocumentPcssMeasure::create( $height )->get( 'pt' ) ),
            ezcDocumentPcssMeasure::create( $width )->get( 'pt' ),
            $height
        );
    }

    /**
     * Draw path specified by the given points array
     * 
     * @param array $points 
     * @return void
     */
    protected function drawPath( array $points )
    {
        $first = true;
        foreach ( $points as $point )
        {
            if ( $first )
            {
                $this->currentPage->moveTo(
                    ezcDocumentPcssMeasure::create( $point[0] )->get( 'pt' ),
                    $this->currentPage->getHeight() -
                        ezcDocumentPcssMeasure::create( $point[1] )->get( 'pt' )
                );
            }
            else
            {
                $this->currentPage->lineTo(
                    ezcDocumentPcssMeasure::create( $point[0] )->get( 'pt' ),
                    $this->currentPage->getHeight() -
                        ezcDocumentPcssMeasure::create( $point[1] )->get( 'pt' )
                );
            }
            $first = false;
        }
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
        $this->currentPage->setRgbFill( $color['red'], $color['green'], $color['blue'] );
        $this->drawPath( $points );
        $this->currentPage->fill();
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
        $this->currentPage->setRgbStroke( $color['red'], $color['green'], $color['blue'] );
        $this->currentPage->setLineWidth( ezcDocumentPcssMeasure::create( $width )->get( 'pt' ) );
        $this->drawPath( $points );
        $this->currentPage->stroke( $close );
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
        $this->currentPage->createURLAnnotation(
            array(
                ezcDocumentPcssMeasure::create( $x )->get( 'pt' ),
                $this->currentPage->getHeight() -
                    ezcDocumentPcssMeasure::create( $y + $height )->get( 'pt' ),
                ezcDocumentPcssMeasure::create( $x + $width )->get( 'pt' ),
                $this->currentPage->getHeight() -
                    ezcDocumentPcssMeasure::create( $y )->get( 'pt' ),
            ),
            $url
        );
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
        $this->pendingInternalLinks[] = array( $this->currentPage, $x, $y, $width, $height, $target );
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
        $this->internalTargets[$id] = $this->currentPage->createDestination();
    }

    /**
     * Render internal links
     *
     * Internal links can only be rendered at the very last items in a PDF,
     * because *all* internal targets must already be known.
     * 
     * @return void
     */
    protected function renderInternalLinks()
    {
        foreach ( $this->pendingInternalLinks as $link )
        {
            list( $page, $x, $y, $width, $height, $target ) = $link;
            if ( !isset( $this->internalTargets[$target] ) )
            {
                // Link reference without any target
                continue;
            }

            $page->createLinkAnnotation(
                array(
                    ezcDocumentPcssMeasure::create( $x )->get( 'pt' ),
                    $this->currentPage->getHeight() -
                        ezcDocumentPcssMeasure::create( $y + $height )->get( 'pt' ),
                    ezcDocumentPcssMeasure::create( $x + $width )->get( 'pt' ),
                    $this->currentPage->getHeight() -
                        ezcDocumentPcssMeasure::create( $y )->get( 'pt' ),
                ),
                $this->internalTargets[$target]
            );
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
        if ( $this->document === null )
        {
            $this->initialize();
        }

        foreach ( $pathes as $path )
        {
            switch ( strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) )
            {
                case 'ttf':
                    $this->fonts[$name][$type] = $this->document->loadTTF( $path, true );
                    $this->dummyDoc->loadTTF( $path, true );
                    return;
            }
        }

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
                $this->document->setInfoAttr( HaruDoc::INFO_TITLE, $value );
                break;
            case 'author':
                $this->document->setInfoAttr( HaruDoc::INFO_AUTHOR, $value );
                break;
            case 'subject':
                $this->document->setInfoAttr( HaruDoc::INFO_SUBJECT, $value );
                break;
            case 'created':
                $date = new DateTime( $value, new DateTimeZone( 'UTC' ) );
                $this->document->setInfoDateAttr( HaruDoc::INFO_CREATION_DATE,
                    $date->format( 'Y' ),
                    $date->format( 'm' ),
                    $date->format( 'd' ),
                    $date->format( 'H' ),
                    $date->format( 'i' ),
                    $date->format( 's' ),
                    "", 0, 0
                );
                break;
            case 'modified':
                $date = new DateTime( $value, new DateTimeZone( 'UTC' ) );
                $this->document->setInfoDateAttr( HaruDoc::INFO_MOD_DATE,
                    $date->format( 'Y' ),
                    $date->format( 'm' ),
                    $date->format( 'd' ),
                    $date->format( 'H' ),
                    $date->format( 'i' ),
                    $date->format( 's' ),
                    "", 0, 0
                );
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

        $this->document->saveToStream();
        return $this->document->readFromStream( $this->document->getStreamSize() );
    }
}
?>
