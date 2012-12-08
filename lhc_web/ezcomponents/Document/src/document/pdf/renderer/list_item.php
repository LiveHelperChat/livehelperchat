<?php
/**
 * File containing the ezcDocumentPdfListItemRenderer class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Renders a list item.
 *
 * Tries to render a list item into the available space, and aborts if
 * not possible.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfListItemRenderer extends ezcDocumentPdfBlockRenderer
{
    /**
     * Item generator used for this list.
     * 
     * @var ezcDocumentListItemGenerator
     */
    protected $generator;

    /**
     * Item number of current item in list.
     * 
     * @var int
     */
    protected $item;

    /**
     * Construct from item number.
     * 
     * @param ezcDocumentPdfDriver $driver
     * @param ezcDocumentPcssStyleInferencer $styles
     * @param ezcDocumentListItemGenerator $generator 
     * @param int $item 
     * @return void
     */
    public function __construct( ezcDocumentPdfDriver $driver, ezcDocumentPcssStyleInferencer $styles, ezcDocumentListItemGenerator $generator, $item )
    {
        parent::__construct( $driver, $styles );
        $this->generator = $generator;
        $this->item      = $item;
    }

    /**
     * Process to render block contents
     * 
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfHyphenator $hyphenator 
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $block 
     * @param ezcDocumentPdfMainRenderer $mainRenderer 
     * @return void
     */
    protected function process( ezcDocumentPdfPage $page, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $block, ezcDocumentPdfMainRenderer $mainRenderer )
    {
        // Render list item
        if ( ( $listItem = $this->generator->getListItem( $this->item ) ) !== '' )
        {
            $styles = $this->styles->inferenceFormattingRules( $block );
            $this->driver->drawWord(
                $page->x + $page->xOffset - $styles['padding']->value['left'],
                $page->y + $styles['font-size']->value,
                $listItem
            );
        }

        // Render list contents
        $mainRenderer->process( $block );
    }
}

?>
