<?php
/**
 * File containing the ezcDocumentPdfWrappingTextBoxRenderer class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Renders an optionally wrapped text box
 *
 * Renders a single text box, like a paragraph, and applies wrapping, if the
 * text box does not fit the current page or column. Orphans and widows are
 * respected during this process.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfWrappingTextBoxRenderer extends ezcDocumentPdfTextBoxRenderer
{
    /**
     * Render a single text box
     *
     * All markup inside of the given string is considered inline markup (in
     * CSS terms). Inline markup should be given as common docbook inline
     * markup, like <emphasis>.
     *
     * Returns a boolean indicator whether the rendering of the full text
     * in the available space succeeded or not.
     *
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfHyphenator $hyphenator 
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $text 
     * @param ezcDocumentPdfMainRenderer $mainRenderer 
     * @return bool
     *
     * @todo This method does not respect changes in the available text width,
     *       if a paragraph is wrapped to the next page. This would require token
     *       reordering, which is not implemented yet.
     */
    public function renderNode( ezcDocumentPdfPage $page, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $text, ezcDocumentPdfMainRenderer $mainRenderer )
    {
        // Inference page styles
        $styles = $this->styles->inferenceFormattingRules( $text );
        $width  = $mainRenderer->calculateTextWidth( $page, $text );

        // Evaluate available space
        if ( ( $space = $this->evaluateAvailableBoundingBox( $page, $styles, $width ) ) === false )
        {
            return false;
        }

        // Iterate over tokens and try to fit them in the current line, use
        // hyphenator to split words.
        $tokens = $this->tokenize( $text, $tokenizer );
        $lines  = $this->fitTokensInLines( $tokens, $hyphenator, $space->width );

        // Transaction wrapping around temporary page creations
        $transaction = $this->driver->startTransaction();

        $lineCount = count( $lines );
        $current   = 0;
        $position  = $space->y;
        $pageNr    = 0;
        $wrap      = false;
        $pages     = array( $pageNr => array(
            'page'  => $page,
            'lines' => array(),
            'space' => $space,
        ) );
        for ( $line = 0; $line < $lineCount; ++$line )
        {
            // Render on current page, of there is still enough space
            if ( ( !$wrap ) &&
                 ( ( $position + $lines[$line]['height'] ) < ( $pages[$pageNr]['space']->y + $pages[$pageNr]['space']->height ) ) )
            {
                ++$current;

                // Check widows, if we are at the last line
                if ( ( $line === ( $lineCount - 1 ) ) &&
                     ( $current < $styles['widows']->value ) &&
                     ( $lineCount >= $styles['widows']->value ) )
                {
                    $difference = $styles['widows']->value - $current;
                    $pages[$pageNr - 1]['lines'] = array_slice( $pages[$pageNr - 1]['lines'], 0, -$difference, true );
                    $pages[$pageNr]['lines'] = array();
                    $line                    = $lineCount - $styles['widows']->value - 1;
                    $current                 = 0;
                    continue;
                }

                $pages[$pageNr]['lines'][] = array(
                    'position' => $position,
                    'line'     => $lines[$line],
                );
                $position += $lines[$line]['height'] * $styles['line-height']->value;
                continue;
            }

            // Shift to next page
            $pages[++$pageNr] = array(
                'page' => $tmpPage = $mainRenderer->getNextRenderingPosition(
                    ( $pWidth = $mainRenderer->calculateTextWidth( $page, $text ) ) +
                    $styles['text-column-spacing']->value,
                    $pWidth
                ),
                'lines' => array(),
                'space' => $this->evaluateAvailableBoundingBox( $tmpPage, $styles, $width ),
            );
            $position = $pages[$pageNr]['space']->y;
            $current  = 0;
            $wrap     = false;

            // Handle orphans
            if ( ( $line < $styles['orphans']->value ) &&
                 ( $line < $lineCount ) )
            {
                $pages[0]['lines'] = array();
                $line = -1;
                continue;
            }

            --$line;
        }

        $this->driver->revert( $transaction );

        // Render lines
        $lineNr = 0;
        foreach ( $pages as $nr => $content )
        {
            if ( $nr > 0 )
            {
                // Get next rendering position
                $page = $mainRenderer->getNextRenderingPosition(
                    ( $pWidth = $mainRenderer->calculateTextWidth( $page, $text ) ) +
                    $styles['text-column-spacing']->value,
                    $pWidth
                );
            }

            if ( !count( $content['lines'] ) )
            {
                continue;
            }

            // Render background & border
            $space         = $content['space'];
            $lastLine      = end( $content['lines'] );
            $space->height = $lastLine['position'] + $lastLine['line']['height'] - $space->y;

            $this->renderBoxBackground( $space, $styles );
            $this->renderBoxBorder( $space, $styles, $nr === 0, $nr + 1 >= count( $pages ) );
            $this->setBoxCovered( $page, $space, $styles );

            // Render actual text contents
            foreach ( $content['lines'] as $line )
            {
                $this->renderLine( $line['position'], $lineNr++, $line['line'], $space, $styles );
            }
        }

        return true;
    }
}
?>
