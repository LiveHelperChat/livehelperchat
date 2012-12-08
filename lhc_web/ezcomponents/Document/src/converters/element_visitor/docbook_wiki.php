<?php
/**
 * File containing the ezcDocumentDocbookToWikiConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Converter for docbook to Wiki with a PHP callback based mechanism, for fast
 * and easy PHP based extensible transformations.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToWikiConverter extends ezcDocumentElementVisitorConverter
{
    /**
     * Current indentation document.
     *
     * @var int
     */
    public static $indentation = 0;

    /**
     * Maximum number of characters per line.
     *
     * @var int
     */
    public static $wordWrap = 78;

    /**
     * Construct converter.
     *
     * Construct converter from XSLT file, which is used for the actual.
     *
     * @param ezcDocumentDocbookToWikiConverterOptions $options
     * @return void
     */
    public function __construct( ezcDocumentDocbookToWikiConverterOptions $options = null )
    {
        parent::__construct(
            $options === null ?
                new ezcDocumentDocbookToWikiConverterOptions() :
                $options
        );

        // Initlize common element handlers
        $this->visitorElementHandler = array(
            'docbook' => array(
                'article'           => $recurse = new ezcDocumentDocbookToWikiRecurseHandler(),
                'book'              => $recurse,
                'anchor'            => $recurse,
                'sect1info'         => $header = new ezcDocumentDocbookToWikiIgnoreHandler(),
                'sect2info'         => $header,
                'sect3info'         => $header,
                'sect4info'         => $header,
                'sect5info'         => $header,
                'sectioninfo'       => $header,
                'sect1'             => $section = new ezcDocumentDocbookToWikiSectionHandler(),
                'sect2'             => $section,
                'sect3'             => $section,
                'sect4'             => $section,
                'sect5'             => $section,
                'section'           => $section,
                'title'             => $section,
                'para'              => $para = new ezcDocumentDocbookToWikiParagraphHandler(),
                'emphasis'          => new ezcDocumentDocbookToWikiEmphasisHandler(),
                'ulink'             => new ezcDocumentDocbookToWikiExternalLinkHandler(),
                'link'              => new ezcDocumentDocbookToWikiInternalLinkHandler(),
                'literal'           => new ezcDocumentDocbookToWikiLiteralHandler(),
                'inlinemediaobject' => new ezcDocumentDocbookToWikiInlineMediaObjectHandler(),
                'mediaobject'       => new ezcDocumentDocbookToWikiMediaObjectHandler(),
                'itemizedlist'      => new ezcDocumentDocbookToWikiItemizedListHandler(),
                'orderedlist'       => new ezcDocumentDocbookToWikiOrderedListHandler(),
                'note'              => $para,
                'tip'               => $para,
                'warning'           => $para,
                'important'         => $para,
                'caution'           => $para,
                'literallayout'     => new ezcDocumentDocbookToWikiLiteralLayoutHandler(),
                'beginpage'         => new ezcDocumentDocbookToWikiBeginPageHandler(),
                'comment'           => new ezcDocumentDocbookToWikiIgnoreHandler(),
                'table'             => new ezcDocumentDocbookToWikiTableHandler(),
            // */
            )
        );
    }

    /**
     * Initialize destination document
     *
     * Initialize the structure which the destination document could be build
     * with. This may be an initial DOMDocument with some default elements, or
     * a string, or something else.
     *
     * @return mixed
     */
    protected function initializeDocument()
    {
        self::$indentation = 0;
        self::$wordWrap    = $this->options->wordWrap;
        return '';
    }

    /**
     * Create document from structure
     *
     * Build a ezcDocumentDocument object from the structure created during the
     * visiting process.
     *
     * @param mixed $content
     * @return ezcDocumentDocument
     */
    protected function createDocument( $content )
    {
        // Append footnotes and citations to document
        $content = $this->finishDocument( $content );

        // Create document object out of contents
        $rst = new ezcDocumentWiki();
        $rst->loadString( $content );
        return $rst;
    }

    /**
     * Wrap given text
     *
     * Wrap the given text to the line width specified in the converter
     * options, with an optional indentation.
     *
     * @param string $text
     * @param int $indentation
     * @return string
     */
    public static function wordWrap( $text, $indentation = 0 )
    {
        // Apply current global indentation
        $indentation += self::$indentation;

        $text = wordwrap( preg_replace( '(\s+)', ' ', $text ), self::$wordWrap - $indentation, "\n" );

        // Apply indentation to text
        $indentationString = str_repeat( ' ', $indentation );
        $text = $indentationString . str_replace( "\n", "\n" . $indentationString, $text );

        return $text;
    }

    /**
     * Escape RST text
     *
     * @param string $string
     * @return string
     */
    public static function escapeWikiText( $string )
    {
        // Equivalent to ezcDocumentWikiTokenizer::TEXT_END_CHARS, except
        // for the whitespace characters and the dot.
        return preg_replace( '([a-z]+://|[*/\\[\\]|{}]+)', '~\\0', $string );
    }

    /**
     * Visit text node.
     *
     * Visit a text node in the source document and transform it to the
     * destination result
     *
     * @param DOMText $node
     * @param mixed $root
        * @return mixed
     */
    protected function visitText( DOMText $node, $root )
    {
        if ( trim( $wholeText = $node->data ) !== '' )
        {
            $root .= self::escapeWikiText( $wholeText );
        }

        return $root;
    }

    /**
     * Finish document
     *
     * Append the footnotes and citations to the end of the document. The
     * footnotes are embedded directly in the text in docbook, aggregated
     * during the processing of the document, and displayed at the bottom
     * of the RST document.
     *
     * @param string $root
     * @return string
     */
    protected function finishDocument( $root )
    {
        // Normalize line breaks
        $root = str_replace( "\n", PHP_EOL, preg_replace( "(\n{2,})", "\n\n", trim( $root ) . "\n" ) );

        return $root;
    }
}

?>
