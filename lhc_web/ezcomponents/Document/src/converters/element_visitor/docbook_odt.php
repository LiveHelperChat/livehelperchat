<?php
/**
 * File containing the ezcDocumentDocbookToOdtConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Converter for docbook to ODT with a PHP callback based mechanism, for fast
 * and easy PHP based extensible transformations.
 *
 * This converter does not support the full docbook standard, but only a subset
 * commonly used in the document component.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToOdtConverter extends ezcDocumentElementVisitorConverter
{
    /**
     * Text node processor.
     * 
     * @var ezcDocumentOdtTextProcessor
     */
    protected $textProcessor;

    /**
     * Stores the base dir to be used during a conversion process.
     *
     * This is either the base dir of the document converted, if set, or the 
     * current working dir, if a document from string is processed.
     * 
     * @var string
     */
    private $docBaseDir;

    /**
     * Image locator object.
     *
     * Updated on each conversion. 
     * 
     * @var ezcDocumentOdtImageLocator
     */
    private $imageLocator;

    /**
     * Meta data generator. 
     * 
     * @var ezcDocumentOdtMetaGenerator
     */
    private $metaGenerator;

    /**
     * Construct converter
     *
     * Construct converter from XSLT file, which is used for the actual
     *
     * @param ezcDocumentDocbookToOdtConverterOptions $options
     * @return void
     */
    public function __construct( ezcDocumentDocbookToOdtConverterOptions $options = null )
    {
        parent::__construct(
            $options === null ?
                new ezcDocumentDocbookToOdtConverterOptions() :
                $options
        );

        $this->textProcessor = new ezcDocumentOdtTextProcessor();
        $this->metaGenerator = new ezcDocumentOdtMetaGenerator();

        $styler = $this->options->styler;

        // Initlize common element handlers
        $this->visitorElementHandler = array(
            'docbook' => array(
                'article'           => $ignore = new ezcDocumentDocbookToOdtIgnoreHandler( $styler ),
                'book'              => $ignore,
                'section'           => $section = new ezcDocumentDocbookToOdtSectionHandler( $styler ),
                // @todo: Need to find a way to handle the meta data.
                'sectioninfo'       => $section,
                'title'             => $section,
                'para'              => $paragraph = new ezcDocumentDocbookToOdtParagraphHandler( $styler ),
                'emphasis'          => $inline = new ezcDocumentDocbookToOdtInlineHandler( $styler ),
                'literal'           => $inline,
                'ulink'             => new ezcDocumentDocbookToOdtUlinkHandler( $styler ),
                'link'              => new ezcDocumentDocbookToOdtLinkHandler( $styler ),
                'anchor'            => new ezcDocumentDocbookToOdtAnchorHandler( $styler ),
                'inlinemediaobject' => $media = new ezcDocumentDocbookToOdtMediaObjectHandler( $styler ),
                'mediaobject'       => $media,
                'itemizedlist'      => $list = new ezcDocumentDocbookToOdtListHandler( $styler ),
                'orderedlist'       => $list,
                'listitem'          => $mapper = new ezcDocumentDocbookToOdtMappingHandler( $styler ),
                'note'              => $paragraph,
                'tip'               => $paragraph,
                'warning'           => $paragraph,
                'important'         => $paragraph,
                'caution'           => $paragraph,
                'literallayout'     => new ezcDocumentDocbookToOdtLiteralLayoutHandler( $styler ),
                'footnote'          => new ezcDocumentDocbookToOdtFootnoteHandler( $styler ),
                'comment'           => new ezcDocumentDocbookToOdtCommentHandler( $styler ),
                'beginpage'         => new ezcDocumentDocbookToOdtPageBreakHandler( $styler ),
                'entry'             => $table = new ezcDocumentDocbookToOdtTableHandler( $styler ),
                'table'             => $table,
                'tbody'             => $table,
                'thead'             => $table,
                'caption'           => $table,
                'tr'                => $table,
                'td'                => $table,
                'row'               => $table,
                'tgroup'            => $ignore,
                // @todo: Need to handle these in a way
                'blockquote'        => $ignore,
                'attribution'       => $ignore,
                'variablelist'      => $deepIgnore = new ezcDocumentDocbookToOdtIgnoreHandler( $styler, true ),
                'varlistentry'      => $deepIgnore,
            )
        );
    }

    /**
     * Converts the given DocBook $source to an ODT document.
     *
     * This method receives a DocBook $source document and returns the 
     * converters ODT document.
     *
     * @param ezcDocumentDocbook $source
     * @return ezcDocumentOdt
     */
    public function convert( $source )
    {
        $destination = $this->initializeDocument();

        $docBookDom = $this->makeLocateable( $source->getDomDocument() );

        $this->options->styler->init( $destination->ownerDocument );

        $this->imageLocator = new ezcDocumentOdtImageLocator( $source );

        $destination = $this->visitChildren(
            $docBookDom,
            $destination
        );

        return $this->createDocument( $destination );
    }

    /**
     * Returns the image locator for the current conversion.
     * 
     * @return ezcDocumentOdtImageLocator
     * @access private
     */
    public function getImageLocator()
    {
        return $this->imageLocator;
    }

    /**
     * Reloads the DOMDocument of the given DocBook to make its elements 
     * locateable.
     * 
     * @param DOMDocument $docBook 
     * @return DOMDocument
     */
    private function makeLocateable( DOMDocument $docBook )
    {
        // Reload the XML document to a DOMDocument with a custom element
        // class. Just registering it on the existing document seems not to
        // work in all cases.
        $reloaded = new DOMDocument();
        $reloaded->registerNodeClass( 'DOMElement', 'ezcDocumentLocateableDomElement' );
        $reloaded->loadXml( $docBook->saveXml() );

        return $reloaded;
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
        $odt = new DOMDocument();
        $odt->preserveWhiteSpace = false;
        $odt->formatOutput = true;
        $odt->load( $this->options->template );

        $this->generateMetaData( $odt );

        $rootElements = $odt->getElementsByTagNameNS(
            ezcDocumentOdt::NS_ODT_OFFICE,
            'text'
        );

        if ( $rootElements->length !== 1 )
        {
            throw new ezcDocumentInvalidOdtException(
                $rootElements,
                "Broken ODT template '{$this->options->template}'. Missing or duplicate body element."
            );
        }
        $root = $rootElements->item( 0 );

        return $root;
    }

    /**
     * Generates standard ODT meta data into the given $odt.
     *
     * Extracts the <office:meta/> element from the given $odt or creates a new 
     * one, if it does not exsist, and generates standard meta data into it.
     * 
     * @param DOMDocument $odt 
     */
    private function generateMetaData( DOMDocument $odt )
    {
        $metaSections = $odt->getElementsByTagnameNS(
            ezcDocumentOdt::NS_ODT_OFFICE,
            'meta'
        );
        
        if ( $metaSections->length === 0 )
        {
            $metaSection = $odt->documentElement->appendChild(
                $odt->createElementNS(
                    ezcDocumentOdt::NS_ODT_OFFICE,
                    'meta'
                )
            );
        }
        else
        {
            $metaSection = $metaSections->item( 0 );
        }

        $this->metaGenerator->generateMetaData( $metaSection );
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

        $odt = new ezcDocumentOdt();
        $odt->setDomDocument( $document );

        return $odt;
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
        $resNodes = $this->textProcessor->processText( $node, $root );

        foreach ( $resNodes as $resNode )
        {
            $root->appendChild( $resNode );
        }

        return $root;
    }
}

?>
