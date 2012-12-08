<?php
/**
 * File containing the ezcDocumentPdfTextBlockRenderer class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Text block renderer
 *
 * Renders a text into a text block.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfTextBlockRenderer extends ezcDocumentPdfTextBoxRenderer
{
    /**
     * Estimate height
     *
     * Estimate required height to render the given text node.
     *
     * @param float $width
     * @param ezcDocumentPdfHyphenator $hyphenator
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $text
     * @return float
     */
    public function estimateHeight( $width, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $text )
    {
        // Inference page styles
        $styles = $this->styles->inferenceFormattingRules( $text );

        // @todo: Apply: Margin, border, padding

        // Iterate over tokens and try to fit them in the current line, use
        // hyphenator to split words.
        $tokens = $this->tokenize( $text, $tokenizer );
        $lines  = $this->fitTokensInLines( $tokens, $hyphenator, $width );

        // Aggregate total height
        $height = 0;
        foreach ( $lines as $nr => $line )
        {
            $height += $line['height'];
        }

        return $height;
    }

    /**
     * Render a single text block into the given area
     *
     * All markup inside of the given string is considered inline markup (in
     * CSS terms). Inline markup should be given as common docbook inline
     * markup, like <emphasis>.
     *
     * Returns a boolean indicator whether the rendering of the full text
     * in the available space succeeded or not.
     *
     * @param ezcDocumentPdfBoundingBox $space
     * @param ezcDocumentPdfHyphenator $hyphenator
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $text
     * @return void
     */
    public function renderBlock( ezcDocumentPdfBoundingBox $space, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $text )
    {
        // Inference page styles
        $styles = $this->styles->inferenceFormattingRules( $text );

        // Iterate over tokens and try to fit them in the current line, use
        // hyphenator to split words.
        $tokens = $this->tokenize( $text, $tokenizer );
        $lines  = $this->fitTokensInLines( $tokens, $hyphenator, $space->width );

        // Try to render text into evaluated box
        return $this->renderTextBox( $lines, $space, $styles );
    }
}
?>
