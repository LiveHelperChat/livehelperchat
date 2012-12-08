<?php
/**
 * File containing the ezcDocumentOdtPcssStyler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * PCSS based ODT styler.
 *
 * This styler is based on the PCSS (simplified CSS rules) styling mechanism.  
 * You can use the {@link addStylesheetFile()} and {@link addStylesheet()} 
 * methods to add custom PCSS styles to it. It is used as the default in the 
 * {@link ezcDocumentDocbookToOdtConverterOptions}.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentOdtPcssStyler implements ezcDocumentOdtStyler
{
    /**
     * Style converter manager. 
     * 
     * @var ezcDocumentOdtPcssConverterManager
     */
    private $styleConverters;

    /**
     * Set of style generators to use. 
     * 
     * @var array(ezcDocumentOdtStyleGenerator)
     */
    private $styleGenerators;

    /**
     * Style sections for the current ODT document. 
     * 
     * @var ezcDocumentOdtStyleInformation
     */
    private $styleInfo;

    /**
     * Style inferencer on DocBook source. 
     * 
     * @var ezcDocumentPcssStyleInferencer
     */
    private $styleInferencer;

    /**
     * Style pre-processors. 
     * 
     * @var array(ezcDocumentOdtPcssPreprocessor)
     */
    private $stylePreProcessors = array();

    /**
     * PCSS parser. 
     * 
     * @var ezcDocumentPcssParser
     */
    private $styleParser;

    /**
     * Creates a new ODT document styler.
     *
     * Creates a new styler. Note that {@link init()} must be 
     * called before {@link applyStyles()} can be used. Otherwise an exception 
     * is thrown.
     */
    public function __construct()
    {
        // @todo: Make configurable
        $this->styleConverters   = new ezcDocumentOdtPcssConverterManager();
        $this->styleInferencer   = new ezcDocumentPcssStyleInferencer();

        // @todo: Make configurable
        $this->styleGenerators[] = new ezcDocumentOdtParagraphStyleGenerator(
            $this->styleConverters
        );
        $this->styleGenerators[] = new ezcDocumentOdtTextStyleGenerator(
            $this->styleConverters
        );
        $this->styleGenerators[] = new ezcDocumentOdtListStyleGenerator(
            $this->styleConverters
        );
        $this->styleGenerators[] = new ezcDocumentOdtTableStyleGenerator(
            $this->styleConverters
        );
        $this->styleGenerators[] = new ezcDocumentOdtTableRowStyleGenerator(
            $this->styleConverters
        );
        $this->styleGenerators[] = new ezcDocumentOdtTableCellStyleGenerator(
            $this->styleConverters
        );

        // @todo: Make configurable
        $this->stylePreProcessors[] = new ezcDocumentOdtPcssListStylePreprocessor();
        $this->stylePreProcessors[] = new ezcDocumentOdtPcssFontStylePreprocessor();
        $this->stylePreProcessors[] = new ezcDocumentOdtPcssParagraphStylePreprocessor();
    }

    /**
     * Initialize the styler with the given $styleInfo.
     *
     * This method *must* be called *before* {@link applyStyles()} is called 
     * at all. Otherwise an exception will be thrown.
     * 
     * @param DOMDocument $odtDocument
     * @access private
     */
    public function init( DOMDocument $odtDocument )
    {
        $this->styleInfo = new ezcDocumentOdtStyleInformation(
            $odtDocument->getElementsByTagNameNS(
                ezcDocumentOdt::NS_ODT_OFFICE,
                'styles'
            )->item( 0 ),
            $odtDocument->getElementsByTagNameNS(
                ezcDocumentOdt::NS_ODT_OFFICE,
                'automatic-styles'
            )->item( 0 ),
            $odtDocument->getElementsByTagNameNS(
                ezcDocumentOdt::NS_ODT_OFFICE,
                'font-face-decls'
            )->item( 0 )
        );
    }

    /**
     * Applies the given $style to the $odtElement.
     *
     * $style is an array of style information as produced by {@link 
     * ezcDocumentPcssStyleInferencer::inferenceFormattingRules()}. The styling 
     * information given in this array is applied to the $odtElement by 
     * creating a new anonymous style in the ODT style section and applying the 
     * corresponding attributes to reference this style.
     * 
     * @param ezcDocumentLocateable $docBookElement
     * @param DOMElement $odtElement
     * @throws ezcDocumentOdtStylerNotInitializedException
     * @access private
     */
    public function applyStyles( ezcDocumentLocateable $docBookElement, DOMElement $odtElement )
    {
        $styles = $this->preProcessStyles(
            $docBookElement,
            $odtElement,
            $this->styleInferencer->inferenceFormattingRules( $docBookElement )
        );

        foreach ( $this->styleGenerators as $generator )
        {
            if ( $generator->handles( $odtElement ) )
            {
                $generator->createStyle( $this->styleInfo, $odtElement, $styles );
            }
        }
    }

    /**
     * Pre-process styles using $stylePreProcessors.
     * 
     * @param DOMElement $docBookElement 
     * @param DOMElement $odtElement 
     * @param array $styles 
     * @return array
     */
    private function preProcessStyles( DOMElement $docBookElement, DOMElement $odtElement, array $styles )
    {
        foreach ( $this->stylePreProcessors as $preProcessor )
        {
            $styles = $preProcessor->process(
                $this->styleInfo,
                $docBookElement,
                $odtElement,
                $styles
            );
        }
        return $styles;
    }

    /**
     * Adds the given PCSS $stylesheet definitions.
     *
     * Adds the PCSS styles given as a string in $stylesheet to the styler.
     * 
     * @param string $stylesheet 
     */
    public function addStylesheet( $stylesheet )
    {
        $parser = $this->createStyleParser();
        $this->styleInferencer->appendStyleDirectives(
            $parser->parseString( $stylesheet )
        );
    }

    /**
     * Adds a PCSS stylesheet from the given file.
     *
     * Reads the given PCSS $file and adds the contained stylesheets to the 
     * styler.
     * 
     * @param string $file 
     */
    public function addStylesheetFile( $file )
    {
        $parser = $this->createStyleParser();
        
        if ( !file_exists( $file ) )
        {
            throw new ezcBaseFileNotFoundException( $file, 'PCSS' );
        }
        if ( !is_readable( $file ) )
        {
            throw new ezcBaseFilePermissionException( $file, ezcBaseFileException::READ );
        }

        $this->styleInferencer->appendStyleDirectives(
            $parser->parseFile( $file )
        );
    }

    /**
     * Returns a PCSS style parser instance.
     *
     * Initializes the $styleParser, if it has not been initialized, yet. Returns 
     * the instance of the style parser to use.
     */
    private function createStyleParser()
    {
        if ( !isset( $this->styleParser ) )
        {
            $this->styleParser = new ezcDocumentPcssParser();
        }
        return $this->styleParser;
    }
}

?>
