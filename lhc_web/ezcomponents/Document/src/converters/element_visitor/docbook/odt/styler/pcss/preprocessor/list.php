<?php
/**
 * File containing the ezcDocumentOdtPcssListStylePreprocessor class.
 *
 * @access private
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * List style pre-processor.
 *
 * Pre-processes list styles, since DocBook stores list bullet and numbering 
 * format in an attribute. An instance of this class creates custom PCSS 
 * properties for this information as follows:
 *
 * - list-type = "bullet" / "number"
 * - list-bullet = bullet character to use
 * - list-number = number representative format
 *
 * @access private
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentOdtPcssListStylePreprocessor
{
    /**
     * List bullet character guesser.
     * 
     * @var ezcDocumentListBulletGuesser
     */
    protected $bulletGuesser;

    /**
     * Mapping of CSS list-style-type values to representative numbers.
     * 
     * @var array(string=>string)
     */
    protected $cssNumberMap = array(
        'decimal'     => '1',
        'lower-roman' => 'i',
        'upper-roman' => 'I',
        'lower-latin' => 'a',
        'upper-latin' => 'A',
        // not supported
        'decimal-leading-zero' => '1',
        'lower-greek'          => 'a',
        'armenian'             => 'A',
        'georgian'             => 'A',
    );

    /**
     * Mapping of DocBook numeration values to representative numbers.
     * 
     * @var array(string=>string)
     */
    protected $docBookNumberMap = array(
        'arabic'     => '1',
        'loweralpha' => 'a',
        'lowerroman' => 'i',
        'upperalpha' => 'A',
        'upperroman' => 'I'
    );

    /**
     * Creates a new list style processor.
     */
    public function __construct()
    {
        $this->bulletGuesser = new ezcDocumentListBulletGuesser();
    }

    /**
     * Pre-process styles and return them.
     *
     * Performs some detection of list styles in the $docBookElement and its 
     * document and sets according PCSS properties in $styles.
     *
     * @param ezcDocumentOdtStyleInformation $styleInfo
     * @param DOMElement $docBookElement
     * @param DOMElement $odtElement 
     * @param array $styles 
     * @return array
     */
    public function process( ezcDocumentOdtStyleInformation $styleInfo, DOMElement $docBookElement, DOMElement $odtElement, array $styles )
    {
        switch ( $docBookElement->localName )
        {
            case 'itemizedlist':
                $styles['list-type'] = new ezcDocumentPcssStyleStringValue( 'bullet' );
                $styles = $this->processListBullet( $docBookElement, $styles );
                break;
            case 'orderedlist':
                $styles['list-type'] = new ezcDocumentPcssStyleStringValue( 'number' );
                $styles = $this->processListEnumeration( $docBookElement, $styles );
                break;
        }
        return $styles;
    }

    /**
     * Detects the list bullet to be used and applies a special PCSS setting 
     * for it.
     *
     * This method tries to detect the list bullet to be used for bullet-lists 
     * and sets the special "list-bullet" PCSS property. The new $styles array 
     * is returned. Note: "list-bullet" is not a standard CSS property and 
     * therefore not supported by any other application using CSS. It is also 
     * possible that this property name changes in future.
     * 
     * @param DOMElement $docBookElement 
     * @param array $styles 
     * @return array
     */
    protected function processListBullet( DOMElement $docBookElement, array $styles )
    {
        if ( !isset( $styles['list-bullet'] ) )
        {
            if ( $docBookElement->hasAttribute( 'mark' ) )
            {
                $styles['list-bullet'] = new ezcDocumentPcssStyleStringValue(
                    $this->bulletGuesser->markToChar(
                        $docBookElement->getAttribute( 'mark' )
                    )
                );
            }
            else if ( isset( $styles['list-style-type'] ) )
            {
                $styles['list-bullet'] = new ezcDocumentPcssStyleStringValue(
                    $this->bulletGuesser->markToChar(
                        $styles['list-style-type']->value
                    )
                );
            }
            else
            {
                $styles['list-bullet'] = new ezcDocumentPcssStyleStringValue(
                    '⚫'
                );
            }
        }
        return $styles;
    }

    /**
     * Detects the list numbering to use and applies a special PCSS setting for 
     * it.
     * 
     * @param DOMElement $docBookElement 
     * @param array $styles 
     * @return void
     */
    protected function processListEnumeration( DOMElement $docBookElement, array $styles )
    {
        if ( !isset( $styles['list-number'] ) )
        {
            if ( $docBookElement->hasAttribute( 'numeration' ) )
            {
                $styles['list-number'] = new ezcDocumentPcssStyleStringValue(
                    $this->docBookNumberMap[$docBookElement->getAttribute( 'numeration' )]
                );
            }
            else if ( isset( $styles['list-style-type'] ) )
            {
                $styles['list-number'] = new ezcDocumentPcssStyleStringValue(
                    $this->cssNumberMap[$styles['list-style-type']->value]
                );
            }
            else
            {
                $styles['list-number'] = new ezcDocumentPcssStyleStringValue(
                    '1'
                );
            }
        }
        return $styles;
    }
}

?>
