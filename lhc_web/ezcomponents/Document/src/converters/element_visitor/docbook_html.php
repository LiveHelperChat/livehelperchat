<?php
/**
 * File containing the ezcDocumentDocbookToHtmlConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Converter for docbook to XHtml with a PHP callback based mechanism, for fast
 * and easy PHP based extensible transformations.
 *
 * This converter does not support the full docbook standard, but only a subset
 * commonly used in the document component. If you need to transform documents
 * using the full docbook you might prefer to use the
 * ezcDocumentDocbookToHtmlXsltConverter with the default stylesheet from
 * Welsh.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToHtmlConverter extends ezcDocumentElementVisitorConverter
{
    /**
     * Reference to the HTML header section
     *
     * @var DOMElement
     */
    protected $head;

    /**
     * Array for footnotes aggregated during the processing of the document.
     * Will be rendered at the end of the HTML document.
     *
     * @var array
     */
    protected $footnotes = array();

    /**
     * Autoincrementing number for footnotes.
     *
     * @var int
     */
    protected $footnoteNumber = 0;

    /**
     * Construct converter
     *
     * Construct converter from XSLT file, which is used for the actual
     *
     * @param ezcDocumentDocbookToHtmlConverterOptions $options
     * @return void
     */
    public function __construct( ezcDocumentDocbookToHtmlConverterOptions $options = null )
    {
        parent::__construct(
            $options === null ?
                new ezcDocumentDocbookToHtmlConverterOptions() :
                $options
        );

        // Initlize common element handlers
        $this->visitorElementHandler = array(
            'docbook' => array(
                'article'           => $mapper = new ezcDocumentDocbookToHtmlMappingHandler(),
                'book'              => $mapper,
                'sect1info'         => $header = new ezcDocumentDocbookToHtmlHeadHandler(),
                'sect2info'         => $header,
                'sect3info'         => $header,
                'sect4info'         => $header,
                'sect5info'         => $header,
                'sectioninfo'       => $header,
                'sect1'             => $section = new ezcDocumentDocbookToHtmlSectionHandler(),
                'sect2'             => $section,
                'sect3'             => $section,
                'sect4'             => $section,
                'sect5'             => $section,
                'section'           => $section,
                'title'             => $section,
                'para'              => new ezcDocumentDocbookToHtmlParagraphHandler(),
                'emphasis'          => new ezcDocumentDocbookToHtmlEmphasisHandler(),
                'literal'           => $mapper,
                'ulink'             => new ezcDocumentDocbookToHtmlExternalLinkHandler(),
                'link'              => new ezcDocumentDocbookToHtmlInternalLinkHandler(),
                'anchor'            => new ezcDocumentDocbookToHtmlAnchorHandler(),
                'inlinemediaobject' => $media = new ezcDocumentDocbookToHtmlMediaObjectHandler(),
                'mediaobject'       => $media,
                'blockquote'        => new ezcDocumentDocbookToHtmlBlockquoteHandler(),
                'itemizedlist'      => $mapper,
                'orderedlist'       => $mapper,
                'listitem'          => $mapper,
                'note'              => $special = new ezcDocumentDocbookToHtmlSpecialParagraphHandler(),
                'tip'               => $special,
                'warning'           => $special,
                'important'         => $special,
                'caution'           => $special,
                'literallayout'     => new ezcDocumentDocbookToHtmlLiteralLayoutHandler(),
                'footnote'          => new ezcDocumentDocbookToHtmlFootnoteHandler(),
                'comment'           => new ezcDocumentDocbookToHtmlCommentHandler(),
                'beginpage'         => $mapper,
                'variablelist'      => $mapper,
                'varlistentry'      => new ezcDocumentDocbookToHtmlDefinitionListEntryHandler(),
                'entry'             => new ezcDocumentDocbookToHtmlTableCellHandler(),
                'table'             => $mapper,
                'tbody'             => $mapper,
                'thead'             => $mapper,
                'row'               => $mapper,
                'tgroup'            => $ignore = new ezcDocumentDocbookToHtmlIgnoreHandler(),
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
        $imp = new DOMImplementation();
        $dtd = $imp->createDocumentType( 'html', '-//W3C//DTD XHTML 1.0 Transitional//EN', 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd' );
        $html = $imp->createDocument( 'http://www.w3.org/1999/xhtml', '', $dtd );
        $html->formatOutput = true;

        $root = $html->createElementNs( 'http://www.w3.org/1999/xhtml', 'html' );
        $html->appendChild( $root );

        $this->head = $html->createElement( 'head' );
        $root->appendChild( $this->head );

        // Append generator
        $generator = $html->createElement( 'meta' );
        $generator->setAttribute( 'name', 'generator' );
        $generator->setAttribute( 'content', 'eZ Components; http://ezcomponents.org' );
        $this->head->appendChild( $generator );

        // Set content type and encoding
        $type = $html->createElement( 'meta' );
        $type->setAttribute( 'http-equiv', 'Content-Type' );
        $type->setAttribute( 'content', 'text/html; charset=utf-8' );
        $this->head->appendChild( $type );

        $this->addStylesheets( $this->head );

        return $root;
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
        $document = $content->ownerDocument;
        $this->appendFootnotes( $content );

        // Ensure a title is set in the document header, as this is required by
        // XHtml
        $xpath = new DOMXPath( $document );
        $title = $xpath->query( '/*[local-name() = "html"]/*[local-name() = "head"]/*[local-name() = "title"]' );
        if ( $title->length < 1 )
        {
            $title = $document->createElement( 'title', 'Empty document' );
            $this->head->appendChild( $title );
        }

        $html = new ezcDocumentXhtml();
        $html->setDomDocument( $document );
        return $html;
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
            $text = new DOMText( $wholeText );
            $root->appendChild( $text );
        }

        return $root;
    }

    /**
     * Add stylesheets to header
     *
     * @param DOMElement $head
     * @return void
     */
    protected function addStylesheets( DOMElement $head )
    {
        if ( $this->options->styleSheets !== null )
        {
            foreach ( $this->options->styleSheets as $styleSheet )
            {
                $link = $head->ownerDocument->createElement( 'link' );
                $link->setAttribute( 'rel', 'Stylesheet' );
                $link->setAttribute( 'type', 'text/css' );
                $link->setAttribute( 'href', htmlspecialchars( $styleSheet ) );
                $head->appendChild( $link );
            }
        }
        else
        {
            $style = $head->ownerDocument->createElement( 'style' );
            $style->setAttribute( 'type', 'text/css' );
            $head->appendChild( $style );

            $cdata = $head->ownerDocument->createCDATASection( $this->options->styleSheet );
            $style->appendChild( $cdata );
        }
    }

    /**
     * Append footnotes
     *
     * Append the footnotes to the end of the document. The footnotes are
     * embedded directly in the text in docbook, aggregated during the
     * processing of the document, and displayed at the bottom of the HTML
     * document.
     *
     * @param DOMElement $root
     * @return void
     */
    protected function appendFootnotes( DOMElement $root )
    {
        if ( !count( $this->footnotes ) )
        {
            // Do not do anything, if there aren't any footnotes.
            return;
        }

        $body = $root->getElementsByTagName( 'body' )->item( 0 );

        $footnoteContainer = $root->ownerDocument->createElement( 'ul' );
        $footnoteContainer->setAttribute( 'class', 'footnotes' );
        $body->appendChild( $footnoteContainer );

        foreach ( $this->footnotes as $nr => $element )
        {
            $li = $root->ownerDocument->createElement( 'li' );
            $footnoteContainer->appendChild( $li );

            $reference = $root->ownerDocument->createElement( 'a', $nr );
            $reference->setAttribute( 'name', 'footnote_' . $nr );
            $li->appendChild( $reference );

            // Visit actual footnote contents and append to the footnote.
            $li = $this->visitChildren( $element, $li );
        }
    }

    /**
     * Append footnote
     *
     * Append a footnote to the document, which then will be visited at the end
     * of the document processing. Returns a numeric identifier for the
     * footnote.
     *
     * @param DOMElement $node
     * @return int
     */
    public function appendFootnote( DOMElement $node )
    {
        $this->footnotes[++$this->footnoteNumber] = $node;
        return $this->footnoteNumber;
    }
}

?>
