<?php
/**
 * File containing the ezcDocumentPdfMainRenderer class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Main PDF renderer class, dispatching to sub renderer, maintaining page
 * contexts and transactions.
 *
 * The basic principles behind the used stacked backtracking rendering
 * algorithm are explained below.
 *
 * The basics
 * ==========
 *
 * The rendering size of a single block (paragraph, image) cannot be guessed 
 * properly beforehand, because the dimensions depend on the associated styles 
 * and the driver which needs to render the styles. Because of different fonts 
 * the size of a single simple string may notably vary. The renderer do not 
 * know about font properties, but only the drivers do.
 *
 * Because of that the renderers (like the paragraph renderer) can only 
 * request the used dimensions for each word, or word part from the current 
 * driver and try to fit that word into the currently available space.
 *
 * Some general constraints, like the handling of orphans and widows, require 
 * the renderer to backtrack. If a orphans constraint could not be fulfilled 
 * with the first rendering try, the renderer needs to decide to render less 
 * lines on the prior page and therefore needs to revert all local rendering 
 * steps and retry the rendering with the additional knowledge.
 *
 * For widow constraints this may mean, that a full paragraph is moved to the 
 * next page, which could mean, that the title before that paragraph might 
 * also be relocated to the following page, which would mean to revert 
 * multiple renderers and elements. To make this possible the renderer wraps 
 * the driver in a transactional driver wrapper.
 *
 * The transactional driver wrapper
 * --------------------------------
 *
 * Like the last paragraph explained it might be necessary to revert (large) 
 * amounts of rendering operations. Once the rendering operations (like 
 * drawWord) hit the driver they are immediately serialized into the
 * respective output format (PDF), and could not be reverted anymore.
 *
 * So an additional layer has been implemented in the class 
 * ezcDocumentPdfTransactionalDriverWrapper, which implements the same 
 * interface as all the other drivers, as well as some additional methods to 
 * handle "transactions".
 *
 * A renderer, like the paragraph renderer, may start a transaction, receives 
 * an ID identifying the started transaction, and may then start its rendering 
 * operations. If the rendering reached a dead end, it may revert everything 
 * using the initially given transaction ID. The revert will affect all 
 * operations since the original call to startTransaction(), even if other 
 * sub-renderers also started transactions in the meantime.
 *
 * The logged calls to the driver are passed up to the real driver once save() 
 * is called explicitly for the given transaction ID, or the main renderer 
 * attempts to write the PDF into a file.
 *
 * Depending on the type of the call the driver wrapper logs and / or passes 
 * the call directly up to the actual driver.
 *
 * Calls which are logged only:
 *  - Everything performing actual rendering, like drawLine(), drawWord(), ...
 *
 * Calls which are logged and passed:
 *  - Everything setting the current style configuration, which might also be 
 *    relevant for font width estimation, especially: setStyle()
 *
 * Calls which are not logged, but passed:
 *  - Everything, which only requests properties, but does not change the 
 *    driver state, like getTextWidth()
 *
 * The stacked renderers
 * ---------------------
 *
 * The main renderer, which is defined in this class, is responsible for 
 * managing the pages, the available horizontal space on the current page and 
 * calling the sub renderers for the distinct parts in the Docbook document.
 *
 * For each part there is a specialized renderer, which is only responsible 
 * for rendering such a part, like a list renderer, a list item renderer or a 
 * paragraph renderer. The main renderer traverses the Docbook document and calls 
 * the appropriate renderer. You may register additional renderers with the 
 * main renderer, for your custom elements, or overwrite the defined default 
 * renderers.
 *
 * The main renderer also handles special page elements, like headers and 
 * footers for each page.
 *
 * The sub renderers ask the main renderer for new space, if they exceeded the 
 * available space in the current column / on the current page. This is 
 * implemented in the method getNextRenderingPosition(). This method might 
 * request a new page from the driver.
 *
 * The sub renderer may as well call other sub renderer, for stacked element 
 * definitions or may request rendering for all those elements by the main 
 * renderer calling back to the process() method.
 *
 * The table sub renderer
 * ----------------------
 *
 * The table renderer is a special sub renderer, since the common space 
 * estimation does not apply here. Tables are structured into cells and the 
 * elements contained in one cell may only use the space defined by the cell. 
 * The table renderer therefore mimics (and extends) the main renderer. So 
 * when the contents of one cell are rendered the sub renderers for the cell 
 * contents (paragraphs, lists, ...) receive an instance of the table renderer 
 * as their "new" main renderer. The table renderer overwrites the methods 
 * like process() and getNextRenderingPosition(), so the sub renderers render 
 * their stuff at the correct positions in the cell.
 *
 * The table renderer itself again dispatches to its main renderer, when, for 
 * example, allocating new pages. In case of a stacked table, the main 
 * renderer of a table renderer may again be a table renderer, which then 
 * dispatches to the original main renderer.
 *
 * Style inheritance
 * -----------------
 *
 * The definition of styles works just like CSS with HTML. Each element 
 * inherits the styles from its parent element, which are then overwritten by 
 * the defined styles in the (P)CSS file.
 *
 * The inferring of the styles for a given element is implemented in the 
 * ezcDocumentPcssStyleInferencer class. An instance of this class containing 
 * the currently defined styles is available during the whole rendering 
 * process and will provide the styles for any element, which is passed to the 
 * object.
 *
 * Hyphenation
 * -----------
 *
 * Hyphenation is a critical task for proper text rendering. A custom 
 * hyphenator may be defined and passed to the renderer. Each text renderer 
 * will the ask the hyphenator to split words, if the whole word does not fit 
 * into one line any more. It would be sensible to implement a hyphenator 
 * based on some available dictionary files.
 *
 * Tokenizer
 * ---------
 *
 * For some languages it might be necessary to implement a different text 
 * tokenizer, which does not just split words at whitespaces. To accomplish 
 * that you may implement and pass a custom tokenizer, which is the 
 * responsible for splitting texts.
 *
 * Some renderers, like the literal box renderer, may already use custom 
 * tokenizers, to implement special rendering tasks.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfMainRenderer extends ezcDocumentPdfRenderer implements ezcDocumentErrorReporting
{
    /**
     * Hyphenator used to split up words
     *
     * @var ezcDocumentPdfHyphenator
     */
    protected $hyphenator;

    /**
     * Tokenizer used to split up strings into words
     *
     * @var ezcDocumentPdfTokenizer
     */
    protected $tokenizer;

    /**
     * Document to render
     *
     * @var ezcDocumentDocbook
     */
    protected $document;

    /**
     * Last transactions started before rendering a new title. This is used to
     * determine, if a title is positioned as a single item in a column or on a
     * page and switch it to the next page in this case.
     *
     * @var mixed
     */
    protected $titleTransaction = null;

    /**
     * Indicator to restart rendering with an earlier item on the same level in
     * the DOM document tree.
     *
     * @var mixed
     */
    protected $restart = false;

    /**
     * Errors occured during the conversion process
     * 
     * @var array
     */
    protected $errors = array();

    /**
     * Maps document elements to handler functions
     *
     * Maps each document element of the associated namespace to its handler
     * method in the current class.
     *
     * @var array
     */
    protected $handlerMapping = array(
        'http://docbook.org/ns/docbook' => array(
            'article'       => 'initializeDocument',
            'section'       => 'renderBlock',
            'sectioninfo'   => 'appendMetaData',

            'para'          => 'renderParagraph',
            'title'         => 'renderTitle',

            'mediaobject'   => 'renderMediaObject',

            'literallayout' => 'renderLiteralLayout',

            'blockquote'    => 'renderBlockquote',

            'table'         => 'renderTable',

            'itemizedlist'  => 'renderList',
            'orderedlist'   => 'renderList',
            'variablelist'  => 'renderBlock',
            'varlistentry'  => 'renderBlock',
            'listitem'      => 'renderListItem',
            'term'          => 'renderTitle',
        ),
    );

    /**
     * Additional PDF parts.
     *
     * @var array
     */
    protected $parts = array();

    /**
     * Error reporting level
     * 
     * @var int
     */
    protected $errorReporting = 15;

    /**
     * PDF renderer options
     * 
     * @var ezcDocumentPdfOptions
     */
    protected $options;

    /**
     * Construct renderer from driver to use
     *
     * @param ezcDocumentPdfDriver $driver 
     * @param ezcDocumentPcssStyleInferencer $styles 
     * @param ezcDocumentPdfOptions $options 
     * @return void
     */
    public function __construct( ezcDocumentPdfDriver $driver, ezcDocumentPcssStyleInferencer $styles, ezcDocumentPdfOptions $options = null )
    {
        $this->driver = new ezcDocumentPdfTransactionalDriverWrapper();
        $this->driver->setDriver( $driver );
        $this->styles         = $styles;
        $this->options        = $options;
        $this->errorReporting = $options !== null ? $options->errorReporting : 15;
    }

    /**
     * Trigger visitor error
     *
     * Emit a vistitor error, and convert it to an exception depending on the
     * error reporting settings.
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $position
     * @return void
     */
    public function triggerError( $level, $message, $file = null, $line = null, $position = null )
    {
        if ( $level & $this->errorReporting )
        {
            throw new ezcDocumentVisitException( $level, $message, $file, $line, $position );
        }
        else
        {
            // If the error should not been reported, we aggregate it to maybe
            // display it later.
            $this->errors[] = new ezcDocumentVisitException( $level, $message, $file, $line, $position );
        }
    }

    /**
     * Return list of errors occured during visiting the document.
     *
     * May be an empty array, if on errors occured, or a list of
     * ezcDocumentVisitException objects.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Tries to locate a file
     *
     * Tries to locate a file, referenced in a docbook document. If available
     * the document path is used a base for relative paths.
     *
     * @param string $file
     * @return string
     */
    public function locateFile( $file )
    {
        if ( !ezcBaseFile::isAbsolutePath( $file ) )
        {
            $file = $this->document->getPath() . $file;
        }

        if ( !is_file( $file ) )
        {
            throw new ezcBaseFileNotFoundException( $file );
        }

        return $file;
    }

    /**
     * Register an additional PDF part
     *
     * Register additional parts, like footnotes, headers or title pages.
     *
     * @param ezcDocumentPdfPart $part
     * @return void
     */
    public function registerPdfPart( ezcDocumentPdfPart $part )
    {
        $this->parts[] = $part;
        $part->registerContext( $this, $this->driver, $this->styles );
    }

    /**
     * Render given document
     *
     * Returns the rendered PDF as string
     *
     * @param ezcDocumentDocbook $document
     * @param ezcDocumentPdfHyphenator $hyphenator
     * @param ezcDocumentPdfTokenizer $tokenizer
     * @return string
     */
    public function render( ezcDocumentDocbook $document, ezcDocumentPdfHyphenator $hyphenator = null, ezcDocumentPdfTokenizer $tokenizer = null )
    {
        $this->hyphenator = $hyphenator !== null ? $hyphenator : new ezcDocumentPdfDefaultHyphenator();
        $this->tokenizer  = $tokenizer !== null ? $tokenizer : new ezcDocumentPdfDefaultTokenizer();
        $this->document   = $document;

        // Register custom fonts in driver
        $this->registerFonts();

        // Inject custom element class, for style inferencing
        $dom = $document->getDomDocument();

        // Reload the XML document with to a DOMDocument with a custom element
        // class. Just registering it on the existing document seems not to
        // work in all cases.
        $reloaded = new DOMDocument();
        $reloaded->registerNodeClass( 'DOMElement', 'ezcDocumentLocateableDomElement' );
        $reloaded->loadXml( $dom->saveXml() );

        $this->process( $reloaded );
        return $this->driver->save();
    }

    /**
     * Register fonts in driver
     *
     * Register the font classes specified in the styles with the driver, so 
     * the driver can use the fonts during the rendering.
     * 
     * @return void
     */
    protected function registerFonts()
    {
        foreach ( $this->styles->getDefinitions( 'font-face' ) as $font )
        {
            if ( !isset( $font->formats['font-family'] ) )
            {
                $this->triggerError( E_WARNING, "Missing font-family declaration in @font-face specification.", $font->file, $font->line );
                continue;
            }
            $name = $font->formats['font-family']->value;

            if ( !isset( $font->formats['src'] ) )
            {
                $this->triggerError( E_WARNING, "Missing src declaration in @font-face specification.", $font->file, $font->line );
                continue;
            }
            $pathes = $font->formats['src']->value;

            $style = ezcDocumentPdfDriver::FONT_PLAIN;
            if ( isset( $font->formats['font-style'] ) &&
                 ( ( $font->formats['font-style']->value === 'oblique' ) ||
                   ( $font->formats['font-style']->value === 'italic' ) ) )
            {
                $style |= ezcDocumentPdfDriver::FONT_OBLIQUE;
            }

            if ( isset( $font->formats['font-weight'] ) &&
                 ( ( $font->formats['font-weight']->value === 'bold' ) ||
                   ( $font->formats['font-weight']->value === 'bolder' ) ) )
            {
                $style |= ezcDocumentPdfDriver::FONT_BOLD;
            }

            $this->driver->registerFont( $name, $style, $pathes );
        }
    }

    /**
     * Check column or page skip prerequisite
     *
     * If no content has been rendered any more in the current column, this
     * method should be called to check prerequisite for the skip, which is
     * especially important for already rendered items, which impose
     * assumptions on following contents.
     *
     * One example for this are titles, which should always be followed by at
     * least some content in the same column.
     *
     * Returns false, if prerequisite are not fulfileld and rendering should be
     * aborted.
     *
     * @param float $move
     * @param float $width
     * @return bool
     */
    public function checkSkipPrerequisites( $move, $width )
    {
        // Ensure the paragraph is on the same page / in the same column
        // like a title, of it is the first paragraph
        if ( $this->titleTransaction === null )
        {
            return true;
        }

        $this->driver->revert( $this->titleTransaction['transaction'] );

        // The rendering should now start again with the title on the
        // next column / page.
        $this->getNextRenderingPosition( $move, $width );
        $this->restart = $this->titleTransaction['position'] - 1;

        $this->titleTransaction = null;
        return false;
    }

    /**
     * Calculate text width
     *
     * Calculate the available horizontal space for texts depending on the
     * page layout settings.
     *
     * @param ezcDocumentPdfPage $page
     * @param ezcDocumentLocateableDomElement $text
     * @return float
     */
    public function calculateTextWidth( ezcDocumentPdfPage $page, ezcDocumentLocateableDomElement $text )
    {
        // Inference page styles
        $rules = $this->styles->inferenceFormattingRules( $text );

        return ( $page->innerWidth -
                ( $rules['text-column-spacing']->value * ( $rules['text-columns']->value - 1 ) )
            ) / $rules['text-columns']->value
            - $page->xOffset - $page->xReduce;
    }

    /**
     * Get next rendering position
     *
     * If the current space has been exceeded this method calculates
     * a new rendering position, optionally creates a new page for
     * this, or switches to the next column. The new rendering
     * position is set on the returned page object.
     *
     * As the parameter you need to pass the required width for the object to
     * place on the page.
     *
     * @param float $move
     * @param float $width
     * @return ezcDocumentPdfPage
     */
    public function getNextRenderingPosition( $move, $width )
    {
        // Then move paragraph into next column / page;
        $trans = $this->driver->startTransaction();
        $page  = $this->driver->currentPage();
        if ( ( ( $newX = $page->x + $move ) < ( $page->startX + $page->innerWidth ) ) &&
             ( ( $space = $page->testFitRectangle( $newX, null, $width, 2 ) ) !== false ) )
        {
            // Another column fits on the current page, find starting Y
            // position
            $page->x = $space->x;
            $page->y = $space->y;

            return $page;
        }

        // If there is no space for a new column, create a new page
        $oldPage = $page;
        $page = $this->driver->appendPage( $this->styles );
        $page->xOffset = $oldPage->xOffset;
        $page->xReduce = $oldPage->xReduce;
        foreach ( $this->parts as $part )
        {
            $part->hookPageCreation( $page );
        }
        return $page;
    }

    /**
     * Process a single element with the registered renderers.
     * 
     * @param DOMElement $element 
     * @param int $number
     * @return int
     */
    public function processNode( DOMElement $element, $number = 0 )
    {
        // Default to docbook namespace, if no namespace is defined
        $namespace = $element->namespaceURI === null ? 'http://docbook.org/ns/docbook' : $element->namespaceURI;

        if ( !isset( $this->handlerMapping[$namespace] ) ||
             !isset( $this->handlerMapping[$namespace][$element->tagName] ) )
        {
            $this->triggerError(
                E_NOTICE,
                "Unknown and unhandled element: {$namespace}:{$element->tagName}."
            );
            return $number;
        }

        $method = $this->handlerMapping[$namespace][$element->tagName];
        $this->$method( $element, $number );

        // Check if the rendering process should be restarted at an earlier
        // point
        if ( $this->restart !== false )
        {
            $number = $this->restart;
            $this->restart = false;
            return $number;
        }

        return $number;
    }

    /**
     * Recurse into DOMDocument tree and call appropriate element handlers
     *
     * @param DOMNode $element
     * @return void
     */
    public function process( DOMNode $element )
    {
        $childNodes = $element->childNodes;
        $nodeCount  = $childNodes->length;

        for ( $i = 0; $i < $nodeCount; ++$i )
        {
            $child = $childNodes->item( $i );
            if ( $child->nodeType !== XML_ELEMENT_NODE )
            {
                continue;
            }

            $i = $this->processNode( $child, $i );
        }
    }

    /**
     * Ignore elements, which should not be rendered
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function ignore( ezcDocumentLocateableDomElement $element )
    {
        // Just do nothing.
    }

    /**
     * Initialize document according to detected root node
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function initializeDocument( ezcDocumentLocateableDomElement $element )
    {
        // Call hooks for started document
        foreach ( $this->parts as $part )
        {
            $part->hookDocumentCreation( $element );
        }

        $page = $this->driver->appendPage( $this->styles );
        // Call hooks for fresh new first page
        foreach ( $this->parts as $part )
        {
            $part->hookPageCreation( $page );
        }

        // Continue processing sub nodes
        $this->process( $element );

        // Call hooks for finished document
        foreach ( $this->parts as $part )
        {
            $part->hookDocumentRendering( $element );
        }
    }

    /**
     * Append document metadata
     * 
     * @param ezcDocumentLocateableDomElement $element 
     * @return void
     */
    private function appendMetaData( ezcDocumentLocateableDomElement $element )
    {
        $childNodes = $element->childNodes;
        $nodeCount  = $childNodes->length;

        // Default metadata values
        $metadata = array(
            'created'  => date( 'r' ),
            'modified' => date( 'r' ),
        );

        // Fields mapped to metadata identifiers
        $fields = array(
            'http://docbook.org/ns/docbook' => array(
                'title'    => 'title',
                'author'   => 'author',
                'authors'  => 'author',
                'subtitle' => 'subject',
                'pubdate'  => 'created',
                'date'     => 'modified',
            ),
        );

        for ( $i = 0; $i < $nodeCount; ++$i )
        {
            $child = $childNodes->item( $i );
            if ( $child->nodeType !== XML_ELEMENT_NODE )
            {
                continue;
            }

            $namespace = $element->namespaceURI === null ? 'http://docbook.org/ns/docbook' : $element->namespaceURI;
            if ( isset( $fields[$namespace] ) &&
                 isset( $fields[$namespace][$child->tagName] ) )
            {
                $metadata[$fields[$namespace][$child->tagName]] = $child->textContent;
            }
        }

        foreach ( $metadata as $key => $value )
        {
            $this->driver->setMetaData( $key, $value );
        }
    }

    /**
     * Handle calls to block element renderer
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function renderBlock( ezcDocumentLocateableDomElement $element )
    {
        $renderer = new ezcDocumentPdfBlockRenderer( $this->driver, $this->styles );
        $page     = $this->driver->currentPage();
        return $renderer->renderNode( $page, $this->hyphenator, $this->tokenizer, $element, $this );
    }

    /**
     * Handle calls to block element renderer
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function renderBlockquote( ezcDocumentLocateableDomElement $element )
    {
        $renderer = new ezcDocumentPdfBlockquoteRenderer( $this->driver, $this->styles );
        $page     = $this->driver->currentPage();
        return $renderer->renderNode( $page, $this->hyphenator, $this->tokenizer, $element, $this );
    }

    /**
     * Handle calls to table element renderer
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function renderTable( ezcDocumentLocateableDomElement $element )
    {
        $renderer = new ezcDocumentPdfTableRenderer( $this->driver, $this->styles, $this->options );
        $page     = $this->driver->currentPage();
        return $renderer->renderNode( $page, $this->hyphenator, $this->tokenizer, $element, $this );
    }

    /**
     * Handle calls to List element renderer
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function renderList( ezcDocumentLocateableDomElement $element )
    {
        $renderer = new ezcDocumentPdfListRenderer( $this->driver, $this->styles );
        $page     = $this->driver->currentPage();
        return $renderer->renderNode( $page, $this->hyphenator, $this->tokenizer, $element, $this );
    }

    /**
     * Handle calls to list item element renderer
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function renderListItem( ezcDocumentLocateableDomElement $element )
    {
        $renderer = new ezcDocumentPdfListItemRenderer( $this->driver, $this->styles, new ezcDocumentNoListItemGenerator(), 0 );
        $page     = $this->driver->currentPage();
        return $renderer->renderNode( $page, $this->hyphenator, $this->tokenizer, $element, $this );
    }

    /**
     * Handle calls to paragraph renderer
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function renderParagraph( ezcDocumentLocateableDomElement $element )
    {
        $renderer = new ezcDocumentPdfWrappingTextBoxRenderer( $this->driver, $this->styles );
        $page     = $this->driver->currentPage();
        $styles   = $this->styles->inferenceFormattingRules( $element );

        // Just try to render at current position first
        $trans = $this->driver->startTransaction();
        if ( $renderer->renderNode( $page, $this->hyphenator, $this->tokenizer, $element, $this ) )
        {
            $this->titleTransaction = null;
            $this->handleAnchors( $element );
            return true;
        }

        // Check if something requested a rendering restart at a prior point,
        // only continue otherwise.
        if ( ( $this->restart !== false ) ||
             ( !$this->checkSkipPrerequisites(
                    ( $pWidth = $this->calculateTextWidth( $page, $element ) ) +
                    $styles['text-column-spacing']->value,
                    $pWidth
                ) ) )
        {
            return false;
        }

        // If that did not work, switch to the next possible location and start
        // there.
        $this->driver->revert( $trans );
        $this->getNextRenderingPosition(
            ( $pWidth = $this->calculateTextWidth( $page, $element ) ) +
            $styles['text-column-spacing']->value,
            $pWidth
        );
        return $this->renderParagraph( $element );
    }

    /**
     * Handle calls to title renderer
     *
     * @param ezcDocumentLocateableDomElement $element
     * @param int $position
     */
    private function renderTitle( ezcDocumentLocateableDomElement $element, $position )
    {
        $styles   = $this->styles->inferenceFormattingRules( $element );
        $renderer = new ezcDocumentPdfTitleRenderer( $this->driver, $this->styles );
        $page     = $this->driver->currentPage();

        // Just try to render at current position first
        $this->titleTransaction = array(
            'transaction' => $this->driver->startTransaction(),
            'page'        => $page,
            'xPos'        => $page->x,
            'position'    => $position,
        );
        if ( $renderer->renderNode( $page, $this->hyphenator, $this->tokenizer, $element, $this ) )
        {
            $this->handleAnchors( $element );
            return true;
        }
        $this->driver->revert( $this->titleTransaction['transaction'] );

        $this->getNextRenderingPosition(
            ( $pWidth = $this->calculateTextWidth( $page, $element ) ) +
            $styles['text-column-spacing']->value,
            $pWidth
        );
        return $this->renderTitle( $element, $position );
    }

    /**
     * Handle calls to media object renderer
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function renderMediaObject( ezcDocumentLocateableDomElement $element )
    {
        $renderer = new ezcDocumentPdfMediaObjectRenderer( $this->driver, $this->styles );
        $page     = $this->driver->currentPage();

        // Just try to render at current position first
        $trans = $this->driver->startTransaction();
        $renderer->renderNode( $page, $this->hyphenator, $this->tokenizer, $element, $this );
        $this->handleAnchors( $element );
    }

    /**
     * Handle calls to paragraph renderer
     *
     * @param ezcDocumentLocateableDomElement $element
     * @return void
     */
    private function renderLiteralLayout( ezcDocumentLocateableDomElement $element )
    {
        $renderer = new ezcDocumentPdfLiteralBlockRenderer( $this->driver, $this->styles );
        $page     = $this->driver->currentPage();
        $styles   = $this->styles->inferenceFormattingRules( $element );

        // Just try to render at current position first
        $trans = $this->driver->startTransaction();
        if ( $renderer->renderNode( $page, $this->hyphenator, $this->tokenizer, $element, $this ) )
        {
            $this->titleTransaction = null;
            $this->handleAnchors( $element );
            return true;
        }

        // Check if something requested a rendering restart at a prior point,
        // only continue otherwise.
        if ( ( $this->restart !== false ) ||
             ( !$this->checkSkipPrerequisites(
                    ( $pWidth = $this->calculateTextWidth( $page, $element ) ) +
                    $styles['text-column-spacing']->value,
                    $pWidth
                ) ) )
        {
            return false;
        }

        // If that did not work, switch to the next possible location and start
        // there.
        $this->driver->revert( $trans );
        $this->getNextRenderingPosition(
            ( $pWidth = $this->calculateTextWidth( $page, $element ) ) +
            $styles['text-column-spacing']->value,
            $pWidth
        );
        return $this->renderParagraph( $element );
    }

    /**
     * Handle all anchors inside the current element
     *
     * Finds all anchors somewhere in the current element and adds reference
     * targets for them.
     * 
     * @param ezcDocumentLocateableDomElement $element 
     * @return void
     */
    private function handleAnchors( ezcDocumentLocateableDomElement $element )
    {
        $xpath = new DOMXPath( $element->ownerDocument );
        $xpath->registerNamespace( 'doc', 'http://docbook.org/ns/docbook' );
        foreach ( $xpath->query( './/doc:anchor', $element ) as $anchor )
        {
            $this->driver->addInternalLinkTarget( $anchor->getAttribute( 'id' ) );
        }
    }
}

?>
