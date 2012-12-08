<?php
/**
 * File containing the ezcDocumentRst class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Document handler for RST text documents.
 *
 * RST (ReStructured Text) is a text based markup language developed inside the
 * docutils project, with a rather complete description of the markup language:
 *
 * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html
 *
 * The basic RST syntax can be extended with so called directives, which can
 * contain user specific markup blocks handled by external applications or
 * custom scripts. This class does not support all of the directives known from
 * docutils yet, but you may register custom additional ones.
 *
 * RST can be converted forth and back between Docbook and RST. Additionally
 * you may register cusom visitors for the abstract sysntax tree (AST) the RST
 * parser creates, to directly convert the AST into other languages then
 * Docbook. Two different visitors for XHTML are already implemented in the
 * component:
 *
 * - ezcDocumentRstXhtmlVisitor
 * - ezcDocumentRstXhtmlBodyVisitor
 *
 * A basic conversion from a RST document to a Docbook document looks like:
 *
 * <code>
 *  $document = new ezcDocumentRst();
 *  $document->loadFile( 'my_rst_doc.txt' );
 *  $docbook = $document->getAsDocbook();
 *  echo $docbook->save();
 * </code>
 *
 * Additional directives, which are implemented by extending from the
 * ezcDocumentRstDirective class, can be registerd before the conversion:
 *
 * <code>
 *  $document = new ezcDocumentRst();
 *  $document->registerDirective( 'address', 'myAddressDirective' );
 *  $document->loadString( <<<EORST
 *  Address example
 *  ===============
 *
 *  .. address:: John Doe
 *      :street: Some Lane 42
 *  EORST
 *  );
 *
 *  $docbook = $document->getAsDocbook();
 *  echo $docbook->save();
 * </code>
 *
 * This class can also read docbook documents (ezcDocumentDocbook) and convert
 * it back to RST, which then works like:
 *
 * <code>
 *  $docbook = new ezcDocumentDocbook();
 *  $docbook->loadFile( 'docbook.xml' );
 *
 *  $rst = new ezcDocumentRst();
 *  $rst->createFromDocbook( $docbook );
 *
 *  echo $rst->save();
 * </code>
 *
 * @package Document
 * @version 1.3.1
 * @mainclass
 */
class ezcDocumentRst extends ezcDocument implements ezcDocumentXhtmlConversion, ezcDocumentValidation, ezcDocumentErrorReporting
{
    /**
     * Registered directives
     *
     * Directives are special RST element, which are documented at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#directives
     *
     * Directives are the best entry point for custom rules, and you may
     * register custom directive classes using the class method
     * registerDirective().
     *
     * @var array
     */
    protected $directives = array(
        'include'   => 'ezcDocumentRstIncludeDirective',
        'contents'  => 'ezcDocumentRstContentsDirective',
        'image'     => 'ezcDocumentRstImageDirective',
        'figure'    => 'ezcDocumentRstFigureDirective',
        'attention' => 'ezcDocumentRstAttentionDirective',
        'warning'   => 'ezcDocumentRstWarningDirective',
        'danger'    => 'ezcDocumentRstDangerDirective',
        'notice'    => 'ezcDocumentRstNoticeDirective',
        'note'      => 'ezcDocumentRstNoteDirective',
    );

    /**
     * Registered interpreted text role handlers
     *
     * Interpreted text roles are special RST element, which are documented at:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#interpreted-text
     *
     * Interpreted text roles are the best entry point for custom rules for
     * inline markup. You can register custom text role using the class method
     * registerRole().
     *
     * @var array
     */
    protected $roles = array(
        'emphasis'        => 'ezcDocumentRstEmphasisTextRole',
        'literal'         => 'ezcDocumentRstLiteralTextRole',
        'strong'          => 'ezcDocumentRstStrongTextRole',
        'subscript'       => 'ezcDocumentRstSubscriptTextRole',
        'sub'             => 'ezcDocumentRstSubscriptTextRole',
        'superscript'     => 'ezcDocumentRstSuperscriptTextRole',
        'super'           => 'ezcDocumentRstSuperscriptTextRole',
        'sup'             => 'ezcDocumentRstSuperscriptTextRole',
        'title_reference' => 'ezcDocumentRstTitleReferenceTextRole',
        'title'           => 'ezcDocumentRstTitleReferenceTextRole',
        't'               => 'ezcDocumentRstTitleReferenceTextRole',
    );

    /**
     * Asbtract syntax tree.
     *
     * The internal representation of RST documents.
     *
     * @var ezcDocumentRstDocumentNode
     */
    protected $ast;

    /**
     * Plain RST contents as a string
     *
     * @var string
     */
    protected $contents;

    /**
     * Construct RST document.
     *
     * @ignore
     * @param ezcDocumentRstOptions $options
     * @return void
     */
    public function __construct( ezcDocumentRstOptions $options = null )
    {
        parent::__construct( $options === null ?
            new ezcDocumentRstOptions() :
            $options );
    }

    /**
     * Register directive handler
     *
     * Register a custom directive handler for special directives or overwrite
     * existing directive handlers. The directives are specified by its
     * (lowercase) name and the class name, which should handle the directive
     * and extend from ezcDocumentRstDirective.
     *
     * @param string $name
     * @param string $class
     * @return void
     */
    public function registerDirective( $name, $class )
    {
        $this->directives[strtolower( $name )] = (string) $class;
    }

    /**
     * Register text role handler
     *
     * Register a custom text role handler for special text roles or overwrite
     * existing text role handlers. The text roles are specified by its
     * (lowercase) name and the class name, which should handle the text role
     * and extend from ezcDocumentRstTextRole.
     *
     * @param string $name
     * @param string $class
     * @return void
     */
    public function registerRole( $name, $class )
    {
        $this->roles[strtolower( $name )] = (string) $class;
    }

    /**
     * Get directive handler
     *
     * Get directive handler class name for the specified name.
     *
     * @param string $name
     * @return string
     */
    public function getDirectiveHandler( $name )
    {
        $name = strtolower( $name );
        if ( !isset( $this->directives[$name] ) )
        {
            throw new ezcDocumentRstMissingDirectiveHandlerException( $name );
        }

        return $this->directives[$name];
    }

    /**
     * Get text role handler
     *
     * Get text role handler class name for the specified name.
     *
     * @param string $name
     * @return string
     */
    public function getRoleHandler( $name )
    {
        $name = strtolower( $name );
        if ( !isset( $this->roles[$name] ) )
        {
            throw new ezcDocumentRstMissingTextRoleHandlerException( $name );
        }

        return $this->roles[$name];
    }

    /**
     * Create document from input string
     *
     * Create a document of the current type handler class and parse it into a
     * usable internal structure.
     *
     * @param string $string
     * @return void
     */
    public function loadString( $string )
    {
        $this->contents = $string;
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
        $tokenizer = new ezcDocumentRstTokenizer();
        $parser    = new ezcDocumentRstParser();
        $parser->options->errorReporting = $this->options->errorReporting;

        $this->ast = $parser->parse( $tokenizer->tokenizeString( $this->contents ) );

        $document = new ezcDocumentDocbook();

        $visitorClass = $this->options->docbookVisitor;
        $visitor = new $visitorClass( $this, $this->path );
        $document->setDomDocument(
            $visitor->visit( $this->ast, $this->path )
        );
        $document->setPath( $this->path );

        // Merge errors from converter
        $this->errors = array_merge(
            $this->errors,
            $parser->getErrors(),
            $visitor->getErrors()
        );

        return $document;
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

        $converter = new ezcDocumentDocbookToRstConverter();
        $converter->options->errorReporting = $this->options->errorReporting;
        $this->contents = $converter->convert( $document )->save();
    }

    /**
     * Return document compiled to the HTML format
     *
     * The internal document structure is compiled to the HTML format and the
     * resulting HTML document is returned.
     *
     * This is an optional interface for document markup languages which
     * support a direct transformation to HTML as a shortcut.
     *
     * @return ezcDocumentXhtml
     */
    public function getAsXhtml()
    {
        $tokenizer = new ezcDocumentRstTokenizer();
        $parser    = new ezcDocumentRstParser();
        $parser->options->errorReporting = $this->options->errorReporting;

        $this->ast = $parser->parse( $tokenizer->tokenizeString( $this->contents ) );

        $document = new ezcDocumentXhtml();

        $visitorClass = $this->options->xhtmlVisitor;
        $visitor = new $visitorClass( $this, $this->path );
        $visitor->options = $this->options->xhtmlVisitorOptions;

        $document->setDomDocument(
            $visitor->visit( $this->ast, $this->path )
        );

        return $document;
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
        return $this->validateString( file_get_contents( $file ) );
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
        $tokenizer = new ezcDocumentRstTokenizer();
        $parser    = new ezcDocumentRstParser();
        // Only halt on parse errors, and collect all other errors.
        $parser->options->errorReporting = E_PARSE;

        $errors = array();
        $ast    = null;
        try
        {
            // Try to parse the document and keep the parse tree for evetual
            // checking for decoration errors
            $ast = $parser->parse( $tokenizer->tokenizeString( $string ) );
        }
        catch ( ezcDocumentParserException $e )
        {
            $errors[] = $e;
        }

        // Get errors and notices from parsed document
        $errors = array_merge( $errors, $parser->errors );

        // If we had no parse error until now, we also try to decorate the
        // document, which may leed to another class of errors.
        if ( $ast !== null )
        {
            $oldErrorReporting = $this->options->errorReporting;
            $this->options->errorReporting = E_PARSE;
            try
            {
                $visitor = new ezcDocumentRstDocbookVisitor( $this, $this->path );
                $visitor->visit( $ast, $this->path );

                // Get errors and notices from parsed document
                $errors = array_merge( $errors, $visitor->getErrors() );
            }
            catch ( ezcDocumentVisitException $e )
            {
                $errors[] = $e;
            }

            // Reset error reporting
            $this->options->errorReporting = $oldErrorReporting;
        }

        if ( count( $errors ) === 0 )
        {
            // If no problem could be found, jsut return true
            return true;
        }
        else
        {
            // Transform aggregated errors into validation errors
            foreach ( $errors as $nr => $error )
            {
                $errors[$nr] = ezcDocumentValidationError::createFromException( $error );
            }
            return $errors;
        }
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
        return $this->contents;
    }
}

?>
