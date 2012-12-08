<?php
/**
 * File containing the ezcDocumentDocbookToRstConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Converter for docbook to Rst with a PHP callback based mechanism, for fast
 * and easy PHP based extensible transformations.
 *
 * This converter does not support the full docbook standard, but only a subset
 * commonly used in the document component. If you need to transform documents
 * using the full docbook you might prefer to use the
 * ezcDocumentDocbookToRstXsltConverter with the default stylesheet from
 * Welsh.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToRstConverter extends ezcDocumentElementVisitorConverter
{
    /**
     * Aggregated links
     *
     * @var array
     */
    protected $links = array();

    /**
     * Aggregated footnotes.
     *
     * @var array
     */
    protected $footnotes = array();

    /**
     * Aggregated citations.
     *
     * @var array
     */
    protected $citations = array();

    /**
     * Aggregated directives.
     *
     * @var array
     */
    protected $directives = array();

    /**
     * Current indentation document.
     *
     * @var int
     */
    public static $indentation = 0;

    /**
     * Maximum number of characters per line
     *
     * @var int
     */
    public static $wordWrap = 78;

    /**
     * Flag indicating whether to skip the paragraph post processing decoration 
     * with links and foornotes. Should be disabled during visiting 
     * sub-elements like footnotes.
     * 
     * @var bool
     */
    protected $skipPostDecoration = false;

    /**
     * Construct converter
     *
     * Construct converter from XSLT file, which is used for the actual
     *
     * @param ezcDocumentDocbookToRstConverterOptions $options
     * @return void
     */
    public function __construct( ezcDocumentDocbookToRstConverterOptions $options = null )
    {
        parent::__construct(
            $options === null ?
                new ezcDocumentDocbookToRstConverterOptions() :
                $options
        );

        // Initlize common element handlers
        $this->visitorElementHandler = array(
            'docbook' => array(
                'article'           => $recurse = new ezcDocumentDocbookToRstRecurseHandler(),
                'book'              => $recurse,
                'sect1info'         => $header = new ezcDocumentDocbookToRstHeadHandler(),
                'sect2info'         => $header,
                'sect3info'         => $header,
                'sect4info'         => $header,
                'sect5info'         => $header,
                'sectioninfo'       => $header,
                'sect1'             => $section = new ezcDocumentDocbookToRstSectionHandler(),
                'sect2'             => $section,
                'sect3'             => $section,
                'sect4'             => $section,
                'sect5'             => $section,
                'section'           => $section,
                'title'             => $section,
                'para'              => new ezcDocumentDocbookToRstParagraphHandler(),
                'emphasis'          => new ezcDocumentDocbookToRstEmphasisHandler(),
                'ulink'             => new ezcDocumentDocbookToRstExternalLinkHandler(),
                'link'              => new ezcDocumentDocbookToRstInternalLinkHandler(),
                'literal'           => new ezcDocumentDocbookToRstLiteralHandler(),
                'inlinemediaobject' => new ezcDocumentDocbookToRstInlineMediaObjectHandler(),
                'mediaobject'       => new ezcDocumentDocbookToRstMediaObjectHandler(),
                'blockquote'        => new ezcDocumentDocbookToRstBlockquoteHandler(),
                'itemizedlist'      => new ezcDocumentDocbookToRstItemizedListHandler(),
                'orderedlist'       => new ezcDocumentDocbookToRstOrderedListHandler(),
                'note'              => $special = new ezcDocumentDocbookToRstSpecialParagraphHandler(),
                'tip'               => $special,
                'warning'           => $special,
                'important'         => $special,
                'caution'           => $special,
                'literallayout'     => new ezcDocumentDocbookToRstLiteralLayoutHandler(),
                'beginpage'         => new ezcDocumentDocbookToRstBeginPageHandler(),
                'footnote'          => new ezcDocumentDocbookToRstFootnoteHandler(),
                'citation'          => new ezcDocumentDocbookToRstCitationHandler(),
                'comment'           => new ezcDocumentDocbookToRstCommentHandler(),
                'variablelist'      => new ezcDocumentDocbookToRstVariableListHandler(),
                'table'             => new ezcDocumentDocbookToRstTableHandler(),
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
        $rst = new ezcDocumentRst();
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
    public static function escapeRstText( $string )
    {
        // Equivalent to ezcDocumentRstTokenizer::TEXT_END_CHARS, except
        // for the whitespace characters and the dot.
        $textEndCharrs = '`*_\\\\[\\]|()"\':';
        return preg_replace( '([' . $textEndCharrs . '])', '\\\\\\0', $string );
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
            $root .= self::escapeRstText( $wholeText );
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
        $root = $this->finishParagraph( $root );

        foreach ( $this->footnotes as $element )
        {
            $root .= '.. [#] ' . trim( self::wordWrap( $element, 3 ) ) . "\n\n";
        }

        foreach ( $this->citations as $nr => $element )
        {
            $root .= sprintf( '.. [CIT%03d] ', $nr + 1 ) . trim( self::wordWrap( $element, 3 ) ) . "\n\n";
        }

        // Normalize line breaks
        $root = str_replace( "\n", PHP_EOL, preg_replace( "(\n{2,})", "\n\n", trim( $root ) . "\n" ) );

        return $root;
    }

    /**
     * Append footnote
     *
     * Append a footnote to the document, which then will be visited at the end
     * of the document processing. Returns a numeric identifier for the
     * footnote.
     *
     * @param string $footnote
     * @return int
     */
    public function appendFootnote( $footnote )
    {
        $this->footnotes[] = $footnote;
        return count( $this->footnotes );
    }

    /**
     * Append citation
     *
     * Append a citation to the document, which then will be visited at the end
     * of the document processing. Returns a numeric identifier for the
     * citation.
     *
     * @param string $citation
     * @return int
     */
    public function appendCitation( $citation )
    {
        $this->citations[] = $citation;
        return count( $this->citations );
    }

    /**
     *Set skip post processing
     *
     * Flag indicating whether to skip the paragraph post processing decoration 
     * with links and foornotes. Should be disabled during visiting 
     * sub-elements like footnotes.
     * 
     * @param bool $flag
     * @return void
     */
    public function setSkipPostDecoration( $flag )
    {
        $this->skipPostDecoration = (bool) $flag;
    }

    /**
     * Append all remaining links at the bottom of the last element.
     *
     * @param string $root
     * @return string
     */
    public function finishParagraph( $root )
    {
        // Only finish paragraph, if there is no current indentation, otherwise
        // this might break a single list into multiple lists
        if ( $this->skipPostDecoration ||
             ( self::$indentation > 0 ) )
        {
            return $root;
        }

        $appended = false;

        // Append links to paragraph
        foreach ( $this->links as $link )
        {
            $root .= '__ ' . $link . "\n";
            $appended = true;
        }
        $this->links = array();

        // Append directive targets to paragraph
        foreach ( $this->directives as $directive )
        {
            $root .= $directive;
            $appended = true;
        }
        $this->directives = array();

        return $root . ( $appended ? "\n" : '' );
    }

    /**
     * Append link
     *
     * Append link, which should be rendered below the paragraph.
     *
     * @param string $link
     * @return void
     */
    public function appendLink( $link )
    {
        $this->links[] = $link;
    }

    /**
     * Append directive
     *
     * Append a directive, which are normally rendered right below the
     * paragraph.
     *
     * @param string $directive
     * @return void
     */
    public function appendDirective( $directive )
    {
        $this->directives[] = $directive;
    }
}

?>
