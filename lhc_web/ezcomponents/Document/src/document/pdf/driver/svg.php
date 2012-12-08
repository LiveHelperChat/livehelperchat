<?php
/**
 * File containing the ezcDocumentPdfSvgDriver class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * SVG renderer for PDF driver, useful for manual introspection and test
 * comparisions.
 *
 * ONLY FOR TESTING - especially, since the text width estimation does not 
 * work properly without using SVG glyph support. The generated XML files are 
 * therefore only used for easy comparision of the general rendering results.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfSvgDriver extends ezcDocumentPdfDriver
{
    /**
     * Svg Document instance
     *
     * @var DOMDocument
     */
    protected $document;

    /**
     * Node of SVG root element
     *
     * @var DOMElement
     */
    protected $svg;

    /**
     * Root node for page elements
     *
     * @var DOMElement
     */
    protected $pages;

    /**
     * Root node for metadata
     *
     * @var DOMElement
     */
    protected $metadata;

    /**
     * Root node of current page
     *
     * @var DOMElement
     */
    protected $currentpage;

    /**
     * Current inner document offset
     *
     * @var float
     */
    protected $offset = 0;

    /**
     * Next inner document offset after page creation
     *
     * @var float
     */
    protected $nextOffset = 0;

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
            self::FONT_PLAIN   => 'Bitstream Vera Sans',
            self::FONT_BOLD    => 'Bitstream Vera Sans',
            self::FONT_OBLIQUE => 'Bitstream Vera Sans',
            3                  => 'Bitstream Vera Sans',
        ),
        'serif' => array(
            self::FONT_PLAIN   => 'Bitstream Vera Serif',
            self::FONT_BOLD    => 'Bitstream Vera Serif',
            self::FONT_OBLIQUE => 'Bitstream Vera Serif',
            3                  => 'Bitstream Vera Serif',
        ),
        'monospace' => array(
            self::FONT_PLAIN   => 'Bitstream Vera Sans Mono',
            self::FONT_BOLD    => 'Bitstream Vera Sans Mono',
            self::FONT_OBLIQUE => 'Bitstream Vera Sans Mono',
            3                  => 'Bitstream Vera Sans Mono',
        ),
        'Symbol' => array(
            self::FONT_PLAIN   => 'Symbol',
        ),
        'ZapfDingbats' => array(
            self::FONT_PLAIN   => 'ZapfDingbats',
        ),
    );

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
        'color' => '#000000',
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

        $this->document = new DOMDocument( '1.0' );
        $this->document->formatOutput = true;

        $this->svg = $this->document->createElementNS( 'http://www.w3.org/2000/svg', 'svg' );
        $this->svg = $this->document->appendChild( $this->svg );

        $this->svg->setAttribute( 'version', '1.2' );
        $this->svg->setAttribute( 'streamable', 'true' );

        $this->pages = $this->document->createElement( 'g' );
        $this->pages = $this->svg->appendChild( $this->pages );
        $this->pages->setAttribute( 'id', 'pages' );

        $metadata = $this->document->createElement( 'metadata' );
        $metadata = $this->svg->appendChild( $metadata );

        $rdfNs = 'http://www.w3.org/1999/02/22-rdf-syntax-ns';
        $rdf = $this->document->createElementNS( $rdfNs, 'rdf:RDF' );
        $metadata->appendChild( $rdf );

        $this->metadata = $this->document->createElementNS( $rdfNs, 'rdf:Description' );
        $this->metadata = $rdf->appendChild( $this->metadata );
        $this->setMetaData( 'creator', 'eZ Components - Document 1.3.1' );
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
        $this->offset      = $this->nextOffset;
        $this->nextOffset += $width + 10;

        $this->currentPage = $this->document->createElement( 'g' );
        $this->currentPage = $this->pages->appendChild( $this->currentPage );

        // Render a containing box visually representing a page in the box
        $page = $this->document->createElement( 'rect' );
        $page = $this->currentPage->appendChild( $page );
        $page->setAttribute( 'x', $this->offset . 'mm' );
        $page->setAttribute( 'y', '0mm' );
        $page->setAttribute( 'width', $width . 'mm' );
        $page->setAttribute( 'height', $height . 'mm' );
        $page->setAttribute( 'style', 'fill: #ffffff; stroke: #000000; stroke-width: 1px; fill-opacity: 1; stroke-opacity: 1;' );
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
                    $this->currentFont['style'] |= self::FONT_OBLIQUE;
                }
                else
                {
                    $this->currentFont['style'] &= ~self::FONT_OBLIQUE;
                }
                break;

            case 'font-weight':
                if ( ( $value === 'bold' ) ||
                     ( $value === 'bolder' ) )
                {
                    $this->currentFont['style'] |= self::FONT_BOLD;
                }
                else
                {
                    $this->currentFont['style'] &= ~self::FONT_BOLD;
                }
                break;

            case 'font-family':
                if ( isset( $this->fonts[$value] ) )
                {
                    $this->currentFont['name'] = $value;
                }
                break;

            case 'font-size':
                $this->currentFont['size'] = ezcDocumentPcssMeasure::create( $value )->get( 'pt' );
                break;

            case 'color':
                $this->currentFont['color'] = sprintf( '#%02x%02x%02x',
                    $value['red'] * 255,
                    $value['green'] * 255,
                    $value['blue'] * 255
                );
                break;

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
        return ezcDocumentPcssMeasure::create(
            ( $this->currentFont['size'] * iconv_strlen( $word, 'UTF-8' ) * .43 ) . 'pt'
        )->get();
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
     * @param float $x
     * @param float $y
     * @param string $word
     * @return void
     */
    public function drawWord( $x, $y, $word )
    {
        $textNode = $this->document->createElement( 'text', htmlspecialchars( $word,  ENT_QUOTES, 'UTF-8' ) );
        $textNode->setAttribute( 'x', sprintf( '%.4Fmm', $x + $this->offset ) );
        $textNode->setAttribute( 'y', sprintf( '%.4Fmm', $y ) );
        $textNode->setAttribute(
            'style',
            sprintf(
                'font-size: %.2Fpt; font-family: %s; font-style: %s; font-weight: %s; stroke: none; fill: %s',
                $this->currentFont['size'],
                $this->fonts[$this->currentFont['name']][self::FONT_PLAIN],
                ( $this->currentFont['style'] & self::FONT_OBLIQUE ) ? 'oblique' : 'normal',
                ( $this->currentFont['style'] & self::FONT_BOLD )    ? 'bold'    : 'normal',
                $this->currentFont['color']
            )
        );
        $this->currentPage->appendChild( $textNode );
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
        $image = $this->document->createElement( 'image' );

        $image->setAttribute( 'x', sprintf( '%.4Fmm', $x + $this->offset ) );
        $image->setAttribute( 'y', sprintf( '%.4Fmm', $y ) );
        $image->setAttribute( 'width', sprintf( '%.4Fmm', $width ) );
        $image->setAttribute( 'height', sprintf( '%.4Fmm', $height ) );
        $image->setAttributeNS(
            'http://www.w3.org/1999/xlink',
            'xlink:href',
            sprintf( 'data:%s;base64,%s',
                $type,
                base64_encode( file_get_contents( $file ) )
            )
        );

        $this->currentPage->appendChild( $image );
    }

    /**
     * Get SVG path string
     *
     * Transform the points array into a SVG path string.
     * 
     * @param array $points 
     * @param bool $close 
     * @return string
     */
    protected function getPointString( array $points, $close = true )
    {
        $pointString = 'M ';
        foreach ( $points as $point )
        {
            $pointString .= sprintf( '%.4F,%.4F L ', 
                ezcDocumentPcssMeasure::create( $point[0] )->get( 'px', 90 ) +
                    ezcDocumentPcssMeasure::create( $this->offset )->get( 'px', 90 ),
                ezcDocumentPcssMeasure::create( $point[1] )->get( 'px', 90 )
            );
        }

        return substr( $pointString, 0, -3 ) . ( $close ? ' z ' : '' );
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
        $polygon = $this->document->createElement( 'path' );
        $polygon->setAttribute( 'd', $this->getPointString( $points ) );
        $polygon->setAttribute(
            'style',
            sprintf(
                'stroke: none; fill: #%02x%02x%02x;',
                $color['red'] * 255,
                $color['green'] * 255,
                $color['blue'] * 255
            )
        );
        $this->currentPage->appendChild( $polygon );
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

        $polygon = $this->document->createElement( 'path' );
        $polygon->setAttribute( 'd', $this->getPointString( $points, $close ) );
        $polygon->setAttribute(
            'style',
            sprintf(
                'stroke: #%02x%02x%02x; stroke-width: %.4Fmm; fill: none;',
                $color['red'] * 255,
                $color['green'] * 255,
                $color['blue'] * 255,
                $width
            )
        );
        $this->currentPage->appendChild( $polygon );
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
        // Not yet supported by SVG driver.
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
        // Not yet supported by SVG driver.
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
        // Not yet supported by SVG driver.
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
        // This is a bit stupid, but this is only a test driver anyways.
        $this->fonts[$name][$type] = "$name (" . pathinfo( $pathes[0], PATHINFO_FILENAME ) . ")";
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
        // We ignore dates here, because it would make stuff harder to test.
        $mapping = array(
            'title' => 'title',
            'author' => 'author',
            'subject' => 'subject',
            'creator' => 'creator',
        );

        if ( !isset( $mapping[$key] ) )
        {
            return;
        }

        $info = $this->document->createElementNS( 'http://purl.org/dc/elements/1.1/', 'dc:' . $mapping[$key], htmlspecialchars( $value ) );
        $this->metadata->appendChild( $info );
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
        return $this->document->saveXml();
    }
}
?>
