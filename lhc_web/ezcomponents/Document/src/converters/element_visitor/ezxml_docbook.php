<?php
/**
 * File containing the ezcDocumentEzXmlToDocbookConverter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Converter for docbook to XDocbook with a PHP callback based mechanism, for fast
 * and easy PHP based extensible transformations.
 *
 * This converter does not support the full docbook standard, but only a subset
 * commonly used in the document component. If you need to transform documents
 * using the full docbook you might prefer to use the
 * ezcDocumentEzXmlToDocbookXsltConverter with the default stylesheet from
 * Welsh.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentEzXmlToDocbookConverter extends ezcDocumentElementVisitorConverter
{
    /**
     * Deafult document namespace.
     *
     * If no namespace has been explicitely declared in the source document
     * assume this as the defalt namespace.
     *
     * @var string
     */
    protected $defaultNamespace = 'ezxml';

    /**
     * Construct converter
     *
     * Construct converter from XSLT file, which is used for the actual
     *
     * @param ezcDocumentEzXmlToDocbookConverterOptions $options
     * @return void
     */
    public function __construct( ezcDocumentEzXmlToDocbookConverterOptions $options = null )
    {
        parent::__construct(
            $options === null ?
                new ezcDocumentEzXmlToDocbookConverterOptions() :
                $options
        );

        // Initlize common element handlers
        $this->visitorElementHandler = array(
            'ezxml' => array(
                'section'          => $mapper = new ezcDocumentEzXmlToDocbookMappingHandler(),
                'header'           => new ezcDocumentEzXmlToDocbookHeaderHandler(),
                'paragraph'        => $mapper,
                'strong'           => $emphasis = new ezcDocumentEzXmlToDocbookEmphasisHandler(),
                'emphasize'        => $emphasis,
                'link'             => new ezcDocumentEzXmlToDocbookLinkHandler(),
                'anchor'           => new ezcDocumentEzXmlToDocbookAnchorHandler(),
                'ol'               => $list = new ezcDocumentEzXmlToDocbookListHandler(),
                'ul'               => $list,
                'li'               => $mapper,
                'literal'          => new ezcDocumentEzXmlToDocbookLiteralHandler(),
                'line'             => new ezcDocumentEzXmlToDocbookLineHandler(),
                'table'            => new ezcDocumentEzXmlToDocbookTableHandler(),
                'tr'               => new ezcDocumentEzXmlToDocbookTableRowHandler(),
                'td'               => new ezcDocumentEzXmlToDocbookTableCellHandler(),
                'th'               => new ezcDocumentEzXmlToDocbookTableCellHandler(),
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
        $dtd = $imp->createDocumentType( 'article', '-//OASIS//DTD DocBook XML V4.5//EN', 'http://www.oasis-open.org/docbook/xml/4.5/docbookx.dtd' );
        $docbook = $imp->createDocument( 'http://docbook.org/ns/docbook', '', $dtd );
        $docbook->formatOutput = true;

        $root = $docbook->createElementNs( 'http://docbook.org/ns/docbook', 'article' );
        $docbook->appendChild( $root );

        return $root;
    }

    /**
     * Create document from structure
     *
     * Build a ezcDocumentDocument object from the structure created during the
     * visiting process.
     *
     * @param mixed $content
     * @return ezcDocumentDocbook
     */
    protected function createDocument( $content )
    {
        $document = $content->ownerDocument;

        $ezxml = new ezcDocumentDocbook();
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
}

?>
