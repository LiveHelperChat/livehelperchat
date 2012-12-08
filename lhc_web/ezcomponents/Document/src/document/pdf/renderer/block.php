<?php
/**
 * File containing the ezcDocumentPdfBlockRenderer class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * General block renderer.
 *
 * Implements the methods to render the border for block level elements, like 
 * list items. Also applies margin and padding to the rendered item. For custom 
 * rendering implementations inside of the rendered block you may overwrite the 
 * process() method.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfBlockRenderer extends ezcDocumentPdfRenderer
{
    /**
     * Render a block level element.
     *
     * Renders a block level element by applzing margin and padding and
     * recursing to all nested elements.
     *
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfHyphenator $hyphenator 
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $block 
     * @param ezcDocumentPdfMainRenderer $mainRenderer 
     * @return bool
     */
    public function renderNode( ezcDocumentPdfPage $page, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $block, ezcDocumentPdfMainRenderer $mainRenderer )
    {
        // @todo: Render border and background. This can be quite hard to
        // estimate, though.
        $styles         = $this->styles->inferenceFormattingRules( $block );
        $page->y       += $styles['padding']->value['top'] +
                          $styles['margin']->value['top'];
        $page->xOffset += $styles['padding']->value['left'] +
                          $styles['margin']->value['left'];
        $page->xReduce += $styles['padding']->value['right'] +
                          $styles['margin']->value['right'];

        $this->process( $page, $hyphenator, $tokenizer, $block, $mainRenderer );

        $page->y       += $styles['padding']->value['bottom'] +
                          $styles['margin']->value['bottom'];
        $page->xOffset -= $styles['padding']->value['left'] +
                          $styles['margin']->value['left'];
        $page->xReduce -= $styles['padding']->value['right'] +
                          $styles['margin']->value['right'];
        return true;
    }

    /**
     * Process to render block contents.
     * 
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfHyphenator $hyphenator 
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $block 
     * @param ezcDocumentPdfMainRenderer $mainRenderer 
     */
    protected function process( ezcDocumentPdfPage $page, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $block, ezcDocumentPdfMainRenderer $mainRenderer )
    {
        $mainRenderer->process( $block );
    }
}

?>
