<?php
/**
 * File containing the ezcDocumentOdt class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The document handler for Open Document Text (ODT) documents.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentOdt extends ezcDocumentXmlBase /* implements ezcDocumentValidation */
{
    const NS_ODT_CONFIG  = 'urn:oasis:names:tc:opendocument:xmlns:config:1.0';
    const NS_ODT_DRAWING = 'urn:oasis:names:tc:opendocument:xmlns:drawing:1.0';
    const NS_ODT_FO      = 'urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0';
    const NS_ODT_META    = 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0';
    const NS_ODT_NUMBER  = 'urn:oasis:names:tc:opendocument:xmlns:data style:1.0';
    const NS_ODT_OFFICE  = 'urn:oasis:names:tc:opendocument:xmlns:office:1.0';
    const NS_ODT_SCRIPT  = 'urn:oasis:names:tc:opendocument:xmlns:script:1.0';
    const NS_ODT_STYLE   = 'urn:oasis:names:tc:opendocument:xmlns:style:1.0';
    const NS_ODT_SVG     = 'urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0';
    const NS_ODT_TABLE   = 'urn:oasis:names:tc:opendocument:xmlns:table:1.0';
    const NS_ODT_TEXT    = 'urn:oasis:names:tc:opendocument:xmlns:text:1.0';

    const NS_XLINK = 'http://www.w3.org/1999/xlink';

    const NS_EZC = 'http://ezcomponents.org/Document/Odt';

    const NS_XML = 'http://www.w3.org/XML/1998/namespace';

    const NS_DC = 'http://purl.org/dc/elements/1.1/';

    /**
     * Array with filter objects for the input ODT document.
     *
     * @var array(ezcDocumentOdtFilter)
     */
    protected $filters;

    /**
     * Construct ODT document.
     *
     * @ignore
     * @param ezcDocumentOdtOptions $options
     * @return void
     */
    public function __construct( ezcDocumentOdtOptions $options = null )
    {
        parent::__construct( $options === null ?
            new ezcDocumentOdtOptions() :
            $options );

        $this->filters = array(
            new ezcDocumentOdtImageFilter( $this->options ),
            new ezcDocumentOdtElementFilter(),
            new ezcDocumentOdtStyleFilter(),
        );
    }

    /**
     * Create document from input string.
     *
     * Create a document of the current type handler class and parse it into a
     * usable internal structure.
     *
     * @param string $string
     * @return void
     */
    public function loadString( $string )
    {
        // Use internal error handling to handle XML errors manually.
        $oldXmlErrorHandling = libxml_use_internal_errors( true );
        libxml_clear_errors();

        // Load XML document
        $this->document = new DOMDocument();
        $this->document->registerNodeClass( 'DOMElement', 'ezcDocumentPropertyContainerDomElement' );

        $this->document->loadXml( $string );

        $errors = ( $this->options->failOnError ?
            libxml_get_errors() :
            null );

        libxml_clear_errors();
        libxml_use_internal_errors( $oldXmlErrorHandling );

        // If there are errors and the error handling is activated throw an
        // exception with the occured errors.
        if ( $errors )
        {
            throw new ezcDocumentErroneousXmlException( $errors );
        }
    }

    /**
     * Set filters
     *
     * Set an array with filter objects, which extract the sematic
     * information from the given ODT document.
     *
     * @param array $filters
     * @return void
     */
    public function setFilters( array $filters )
    {
        $this->filters = $filters;
    }

    /**
     * Build docbook document out of annotated ODT document
     *
     * @param DOMDocument $document
     * @return DOMDocument
     */
    protected function buildDocbookDocument( DOMDocument $document )
    {
        $docbook = new DOMDocument( '1.0', 'utf-8' );
        $docbook->preserveWhiteSpace = false;
        $docbook->formatOutput = true;

        $root = $docbook->createElementNs( 'http://docbook.org/ns/docbook', 'article' );
        $docbook->appendChild( $root );

        $xpath = new DOMXPath( $document );
        $xpath->registerNamespace( 'office', self::NS_ODT_OFFICE );
        // @todo: Process meta data
        $body = $xpath->query( '//office:body' )->item( 0 );
        $this->transformToDocbook( $body, $root );

        return $docbook;
    }

    /**
     * Recursively transform annotated ODT elements to docbook
     *
     * @param DOMElement $odt
     * @param DOMElement $docbook
     * @param bool $significantWhitespace
     * @return void
     */
    protected function transformToDocbook( DOMElement $odt, DOMElement $docbook, $significantWhitespace = false )
    {
        if ( ( $spaces = $odt->getProperty( 'spaces' ) ) !== false )
        {
            $docbook->appendChild(
                new DOMText( $spaces )
            );
        }

        if ( ( $tagName = $odt->getProperty( 'type' ) ) !== false )
        {
            $node = new DOMElement( $tagName );
            $docbook->appendChild( $node );
            $docbook = $node;

            if ( ( $attributes = $odt->getProperty( 'attributes' ) ) !== false )
            {
                foreach ( $attributes as $name => $value )
                {
                    $node->setAttribute( $name, $value );
                }
            }
        }

        $numChildren = $odt->childNodes->length;
        for ( $i = 0; $i < $numChildren; ++$i )
        {
            $child = $odt->childNodes->item( $i );
            switch ( $child->nodeType )
            {
                case XML_ELEMENT_NODE:
                    $this->transformToDocbook(
                        $child,
                        $docbook,
                        $significantWhitespace || $odt->getProperty( 'whitespace' ) === 'significant'
                    );
                    break;

                case XML_TEXT_NODE:
                    $docbook->appendChild(
                        new DOMText( $child->data )
                    );
                    break;

                case XML_CDATA_SECTION_NODE:
                    $docbook->appendChild(
                        $docbook->ownerDocument->createCDATASection(
                            $child->data
                        )
                    );
                    break;

                // case XML_ENTITY_NODE:
                    // Seems not required, as entities in the source document
                    // are automatically transformed back to their text
                    // targets.
                    // break;

                case XML_COMMENT_NODE:
                    $comment = new DOMElement( 'comment', $child->data );
                    $docbook->appendChild( $comment );
                    break;
            }
        }
    }

    /**
     * Return document compiled to the docbook format
     *
     * The internal document structure is compiled to the docbook format and
     * the resulting docbook document is returned.
     *
     * This method is required for all formats to have one central format, so
     * that each format can be compiled into each other format using docbook as
     * an intermediate format.
     *
     * You may of course just call an existing converter for this conversion.
     *
     * @return ezcDocumentDocbook
     */
    public function getAsDocbook()
    {
        foreach ( $this->filters as $filter )
        {
            $filter->filter( $this->document );
        }

        $docbook = new ezcDocumentDocbook();
        $docbook->setDomDocument(
            $this->buildDocbookDocument( $this->document )
        );
        $docbook->setPath( $this->path );
        return $docbook;
    }

    /**
     * Create document from docbook document
     *
     * A document of the docbook format is provided and the internal document
     * structure should be created out of this.
     *
     * This method is required for all formats to have one central format, so
     * that each format can be compiled into each other format using docbook as
     * an intermediate format.
     *
     * You may of course just call an existing converter for this conversion.
     *
     * @param ezcDocumentDocbook $document
     * @return void
     */
    public function createFromDocbook( ezcDocumentDocbook $document )
    {
        if ( $this->options->validate &&
             $document->validateString( $document ) !== true )
        {
            $this->triggerError( E_WARNING, "You try to convert an invalid docbook document. This may lead to invalid output." );
        }

        $this->path = $document->getPath();

        $converter = new ezcDocumentDocbookToOdtConverter();
        $converter->options->errorReporting = $this->options->errorReporting;
        $doc = $converter->convert( $document );
        $this->document = $doc->getDomDocument();
    }

    /**
     * Return document as string
     *
     * Serialize the document to a string an return it.
     *
     * @return string
     */
    public function save()
    {
        $source = $this->document->saveXml( $this->document );
        return $source;
    }

    /**
     * Validate the input file
     *
     * Validate the input file against the specification of the current
     * document format.
     *
     * Returns true, if the validation succeded, and an array with
     * ezcDocumentValidationError objects otherwise.
     *
     * @param string $file
     * @return mixed
     */
    public function validateFile( $file )
    {
        $oldSetting = libxml_use_internal_errors( true );
        libxml_clear_errors();

        $document = new DOMDocument();
        $document->load( $file );
        $res = $this->performValidation( $document );

        libxml_use_internal_errors( $oldSetting );

        return $res;
    }

    /**
     * Performs the actual validation on the given $document.
     *
     * Returns true on success, an array of errors otherwise.
     * 
     * @param DOMDocument $document 
     * @return array(ezcDocumentValidationError)|true
     */
    private function performValidation( DOMDocument $document )
    {
        $document->relaxNGValidate(
            dirname( __FILE__ ) . '/odt/data/odf_1.2.rng'
        );

        // Get all errors
        $xmlErrors = libxml_get_errors();
        $errors = array();
        foreach ( $xmlErrors as $error )
        {
            $errors[] = ezcDocumentValidationError::createFromLibXmlError(
                $error
            );
        }
        libxml_clear_errors();

        return ( count( $errors ) ? $errors : true );
    }

    /**
     * Validate the input string
     *
     * Validate the input string against the specification of the current
     * document format.
     *
     * Returns true, if the validation succeded, and an array with
     * ezcDocumentValidationError objects otherwise.
     *
     * @param string $string
     * @return mixed
     */
    public function validateString( $string )
    {
        $oldSetting = libxml_use_internal_errors( true );
        libxml_clear_errors();

        $document = new DOMDocument();
        $document->loadXml( $string );
        $res = $this->performValidation( $document );

        libxml_use_internal_errors( $oldSetting );

        return $res;
    }
}

?>
