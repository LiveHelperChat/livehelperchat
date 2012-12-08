<?php
/**
 * File containing ezcDocumentDocbookToEzXmlConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Converter for docbook to XEzXml with a PHP callback based mechanism, for fast
 * and easy PHP based extensible transformations.
 *
 * This converter does not support the full docbook standard, but only a subset
 * commonly used in the document component. If you need to transform documents
 * using the full docbook you might prefer to use the
 * ezcDocumentDocbookToEzXmlXsltConverter with the default stylesheet from
 * Welsh.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToEzXmlConverter extends ezcDocumentElementVisitorConverter
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
     * @param ezcDocumentDocbookToEzXmlConverterOptions $options
     * @return void
     */
    public function __construct( ezcDocumentDocbookToEzXmlConverterOptions $options = null )
    {
        parent::__construct(
            $options === null ?
                new ezcDocumentDocbookToEzXmlConverterOptions() :
                $options
        );

        // Initlize common element handlers
        $this->visitorElementHandler = array(
            'docbook' => array(
                'article'           => $recurse = new ezcDocumentDocbookToEzXmlRecurseHandler(),
                'book'              => $recurse,
                'sect1info'         => $ignore = new ezcDocumentDocbookToEzXmlIgnoreHandler(),
                'sect2info'         => $ignore,
                'sect3info'         => $ignore,
                'sect4info'         => $ignore,
                'sect5info'         => $ignore,
                'sectioninfo'       => $ignore,
                'sect1'             => $section = new ezcDocumentDocbookToEzXmlSectionHandler(),
                'sect2'             => $section,
                'sect3'             => $section,
                'sect4'             => $section,
                'sect5'             => $section,
                'section'           => $section,
                'title'             => new ezcDocumentDocbookToEzXmlTitleHandler(),
                'para'              => new ezcDocumentDocbookToEzXmlParagraphHandler(),
                'emphasis'          => new ezcDocumentDocbookToEzXmlEmphasisHandler(),
                'literal'           => $mapper =  new ezcDocumentDocbookToEzXmlMappingHandler(),
                'ulink'             => new ezcDocumentDocbookToEzXmlExternalLinkHandler(),
                'link'              => new ezcDocumentDocbookToEzXmlInternalLinkHandler(),
                'anchor'            => new ezcDocumentDocbookToEzXmlAnchorHandler(),
                'itemizedlist'      => new ezcDocumentDocbookToEzXmlItemizedListHandler(),
                'orderedlist'       => new ezcDocumentDocbookToEzXmlOrderedListHandler(),
                'listitem'          => $mapper,
                'literallayout'     => new ezcDocumentDocbookToEzXmlLiteralLayoutHandler(),
                'footnote'          => new ezcDocumentDocbookToEzXmlFootnoteHandler(),
                'comment'           => new ezcDocumentDocbookToEzXmlCommentHandler(),
                'beginpage'         => $ignore,
                'entry'             => new ezcDocumentDocbookToEzXmlTableCellHandler(),
                'table'             => new ezcDocumentDocbookToEzXmlTableHandler(),
                'tbody'             => $recurse,
                'thead'             => $recurse,
                'row'               => $mapper,
                'tgroup'            => $recurse,
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
        $ezxml = new DOMDocument();
        $ezxml->formatOutput = true;

        $root = $ezxml->createElementNs( 'http://ez.no/namespaces/ezpublish3', 'section' );
        $root->setAttribute( 'xmlns:image',  'http://ez.no/namespaces/ezpublish3/image/' );
        $root->setAttribute( 'xmlns:xhtml',  'http://ez.no/namespaces/ezpublish3/xhtml/' );
        $root->setAttribute( 'xmlns:custom', 'http://ez.no/namespaces/ezpublish3/custom/' );
        $ezxml->appendChild( $root );

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

        $ezxml = new ezcDocumentEzXml();
        $ezxml->setDomDocument( $document );
        return $ezxml;
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

        $body = $root->getElementsByTagName( 'section' )->item( 0 );

        $paragraph = $root->ownerDocument->createElement( 'paragraph' );
        $body->appendChild( $paragraph );

        $footnoteContainer = $root->ownerDocument->createElement( 'ul' );
        $footnoteContainer->setAttribute( 'class', 'footnotes' );
        $paragraph->appendChild( $footnoteContainer );

        foreach ( $this->footnotes as $nr => $element )
        {
            $li = $root->ownerDocument->createElement( 'li' );
            $footnoteContainer->appendChild( $li );

            $paragraph = $root->ownerDocument->createElement( 'paragraph' );
            $li->appendChild( $paragraph );

            $reference = $root->ownerDocument->createElement( 'anchor', $nr );
            $reference->setAttribute( 'name', '__footnote_' . $nr );
            $paragraph->appendChild( $reference );

            // Visit actual footnote contents and append to the footnote.
            $paragraph = $this->visitChildren( $element, $paragraph );
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
