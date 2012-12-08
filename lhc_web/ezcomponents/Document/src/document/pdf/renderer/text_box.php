<?php
/**
 * File containing the ezcDocumentPdfTextBoxRenderer class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Renders a single text box
 *
 * Tries to render a single text box into the available space, and aborts if
 * not possible.
 *
 * Implements the basic methods for tokenizing the text, style based text 
 * fitting into lines, etc. Should be extended by all classes implementing more 
 * specific text rendering algorithms, since those base methods are implemented 
 * generally enough to be reused.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfTextBoxRenderer extends ezcDocumentPdfBlockRenderer
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
     */
    public function renderNode( ezcDocumentPdfPage $page, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $text, ezcDocumentPdfMainRenderer $mainRenderer )
    {
        // Inference page styles
        $styles = $this->styles->inferenceFormattingRules( $text );
        $width  = $page->innerWidth / $styles['text-columns']->value -
            ( $styles['text-column-spacing']->value * ( $styles['text-columns']->value - 1 ) );

        // Evaluate available space
        if ( ( $space = $this->evaluateAvailableBoundingBox( $page, $styles, $width ) ) === false )
        {
            return false;
        }

        // Iterate over tokens and try to fit them in the current line, use
        // hyphenator to split words.
        $tokens = $this->tokenize( $text, $tokenizer );
        $lines  = $this->fitTokensInLines( $tokens, $hyphenator, $space->width );

        // Evaluate required space by text box
        $required = 0;
        foreach ( $lines as $line )
        {
            $required += $line['height'] * $styles['line-height']->value;
        }

        // Check that enough space is available to render text box
        if ( $required > $space->height )
        {
            return false;
        }
        $space->height = $required;

        $this->renderBoxBackground( $space, $styles );
        $this->renderBoxBorder( $space, $styles );
        $this->renderTextBox( $lines, $space, $styles );
        $this->setBoxCovered( $page, $space, $styles );
        return true;
    }

    /**
     * Render text box
     *
     * Render a single text box, specified by the given lines array,
     * containing tokens and their styles, the available space and
     * the styles array for the currently rendered element.
     *
     * Returns false, if the box size was not sufficant for the
     * given text, and the covered vertical area otherwise.
     *
     * @param array $lines
     * @param ezcDocumentPdfBoundingBox $space
     * @param array $styles
     * @return boolean
     */
    protected function renderTextBox( array $lines, ezcDocumentPdfBoundingBox $space, array $styles )
    {
        $yPos = $space->y;
        foreach ( $lines as $nr => $line )
        {
            $yPos += $this->renderLine( $yPos, $nr, $line, $space, $styles ) * $styles['line-height']->value;

            // Check if we run out of vertical space
            if ( $yPos > ( $space->y + $space->height ) )
            {
                return false;
            }
        }

        return $yPos - $space->y;
    }

    /**
     * Reverses a string
     *
     * Similar to PHPs strrev() function, but also works for UTF-8 strings.
     * 
     * @param string $string 
     * @return string
     */
    protected function strrev( $string )
    {
        if ( !is_string( $string ) || empty( $string ) )
        {
            return $string;
        }

        if ( strlen( $string ) === ( $length = iconv_strlen( $string, 'UTF-8' ) ) )
        {
            // String only contains of single-byte characters
            return strrev( $string );
        }

        $reverted = '';
        for ( $c = $length; $c > 0; --$c )
        {
            $reverted .= iconv_substr( $string, $c - 1, 1, 'UTF-8' );
        }
        return $reverted;
    }

    /**
     * Render a single line and return the used height
     *
     * @param float $position
     * @param int $number
     * @param array $line
     * @param ezcDocumentPdfBoundingBox $space
     * @param array $styles
     * @return void
     */
    protected function renderLine( $position, $number, array $line, ezcDocumentPdfBoundingBox $space, array $styles )
    {
        $spaceWidth = $this->driver->calculateWordWidth( ' ' );
        $lineWidth = 0;
        foreach ( $line['tokens'] as $token )
        {
            if ( !is_int( $token['word'] ) )
            {
                $lineWidth += $token['width'];
            }
        }

        // Reverse alignement, if direction is set to "right-to-left"
        $align = $styles['text-align']->value;
        if ( $styles['direction']->value === 'rtl' )
        {
            switch ( $align )
            {
                case 'right':
                    $align = 'left';
                default:
                    $align = 'right';
            }
        }

        switch ( $align )
        {
            case 'center':
                $offset = ( $space->width - $lineWidth - ( $line['spaces'] * $spaceWidth ) ) / 2;
                break;
            case 'right':
                $offset = $space->width - $lineWidth - ( $line['spaces'] * $spaceWidth );
                break;
            case 'justify':
                $offset = 0;
                switch ( true )
                {
                    case $number === $line['words']:
                        // Just use common space width in last line of a
                        // paragraph
                        $spaceWidth = $this->driver->calculateWordWidth( ' ' );
                        break;
                    case $line['words'] <= 1:
                        // Space width is irrelevant, if only one token is
                        // in the line
                        break;
                    default:
                        $spaceWidth = ( $space->width - $lineWidth ) / ( $line['spaces'] - 1 );
                }
            default:
                $offset = 0;
        }

        // Reverse tokens and words, if direction is set to "right-to-left"
        $tokens = $line['tokens'];
        if ( $styles['direction']->value === 'rtl' )
        {
            $tokens = array_reverse( $tokens );
            foreach ( $tokens as $nr => $token )
            {
                if ( is_string( $token['word'] ) )
                {
                    $tokens[$nr]['word'] = $this->strrev( $token['word'] );
                }
            }
        }

        // Default to left alignement
        $xPos = $space->x + $offset;
        foreach ( $tokens as $token )
        {
            if ( $token['word'] === ezcDocumentPdfTokenizer::SPACE )
            {
                $this->renderTextDecoration( $token['style'], $xPos, $position, $spaceWidth, $line['height'] );
                $this->handleLinks( $token, $xPos, $position, $spaceWidth, $line['height'] );

                $xPos += $spaceWidth;
            }
            else if ( is_string( $token['word'] ) )
            {
                $this->renderTextDecoration( $token['style'], $xPos, $position, $token['width'], $line['height'] );

                // Apply current styles
                foreach ( $token['style'] as $style => $value )
                {
                    $this->driver->setTextFormatting( $style, $value->value );
                }

                // Render word
                $this->driver->drawWord( $xPos, $position + $line['height'], $token['word'] );
                $this->handleLinks( $token, $xPos, $position, $token['width'], $line['height'] );
                $xPos += $token['width'];
            }
        }

        return $line['height'];
    }

    /**
     * Handle links
     *
     * Handle embedded link markup for current token and perform the
     * appropriate calls to the driver.
     * 
     * @param array $token 
     * @param float $x 
     * @param float $y 
     * @param float $width 
     * @param float $height 
     * @return void
     */
    protected function handleLinks( array $token, $x, $y, $width, $height )
    {
        if ( $token['url'] !== null )
        {
            $this->driver->addExternalLink( $x, $y, $width, $height * 1.1, $token['url'] );
        }

        if ( $token['target'] !== null )
        {
            $this->driver->addInternalLink( $x, $y, $width, $height * 1.1, $token['target'] );
        }
    }

    /**
     * Render text decoration
     *
     * Render text decoration, like by a assigned text-decoration setting, or
     * background-colors, and similar.
     * 
     * @param array $styles 
     * @param float $x 
     * @param float $y 
     * @param float $width 
     * @param float $height 
     * @return void
     */
    protected function renderTextDecoration( array $styles, $x, $y, $width, $height )
    {
        // Directly exit, if there are no decorations to render
        if ( ( $styles['text-decoration']->value === 'none' ) &&
             ( !isset( $styles['background-color'] ) ||
               ( $styles['background-color']->value['alpha'] >= 1 ) ) )
        {
            return;
        }

        if ( isset( $styles['background-color'] ) &&
             ( $styles['background-color']->value['alpha'] < 1 ) )
        {
            $this->driver->drawPolygon(
                array(
                    array( $x, $y ),
                    array( $x + $width, $y ),
                    array( $x + $width, $y + $height * $styles['line-height']->value ),
                    array( $x, $y + $height * $styles['line-height']->value ),
                ),
                $styles['background-color']->value
            );
        }

        if ( strpos( $styles['text-decoration'], 'line-through' ) !== false )
        {
            $this->driver->drawPolyline(
                array(
                    array( $x, $y + $height - $styles['font-size']->value / 3 ),
                    array( $x + $width, $y + $height - $styles['font-size']->value / 3 ),
                ),
                $styles['color']->value,
                // @todo: How thick should line-throughs be?
                ezcDocumentPcssMeasure::create( '1px' )->get(),
                false
            );
        }

        if ( strpos( $styles['text-decoration'], 'overline' ) !== false )
        {
            $this->driver->drawPolyline(
                array(
                    array( $x, $y ),
                    array( $x + $width, $y ),
                ),
                $styles['color']->value,
                // @todo: How thick should overlines be?
                ezcDocumentPcssMeasure::create( '1px' )->get(),
                false
            );
        }

        if ( strpos( $styles['text-decoration'], 'underline' ) !== false )
        {
            $this->driver->drawPolyline(
                array(
                    array( $x, $y + $height * 1.1 ),
                    array( $x + $width, $y + $height * 1.1 ),
                ),
                $styles['color']->value,
                // @todo: How thick should underlines be?
                ezcDocumentPcssMeasure::create( '1px' )->get(),
                false
            );
        }
    }

    /**
     * Tokenize the input string
     *
     * For proper word wrapping in the paragraph the strng needs to be
     * tokenized, while each token has to maintain its stack of assigned
     * formats.
     *
     * This method should return an array of tokens, also maintaining the
     * included whitespace characters, each associated with its markup
     * elements.
     *
     * @param ezcDocumentLocateableDomElement $element
     * @param ezcDocumentPdfTokenizer $tokenizer
     * @param bool $recursed
     * @return array
     */
    protected function tokenize( ezcDocumentLocateableDomElement $element, ezcDocumentPdfTokenizer $tokenizer, $recursed = false )
    {
        $tokens = array();
        $rules  = $this->styles->inferenceFormattingRules( $element, ezcDocumentPcssStyleInferencer::TEXT );

        // Do not inherit background and border rules from paragraph
        if ( !$recursed )
        {
            $rules = array_diff_key( $rules, array(
                'background-color' => true,
                'border'           => true,
            ) );
        }

        $url    = $element->tagName === 'ulink' && $element->hasAttribute( 'url'    ) ? $element->getAttribute( 'url'    ) : null;
        $target = $element->tagName === 'link'  && $element->hasAttribute( 'linked' ) ? $element->getAttribute( 'linked' ) : null;

        foreach ( $element->childNodes as $child )
        {
            switch ( $child->nodeType )
            {
                // case XML_CDATA_SECTION_NODE:
                case XML_TEXT_NODE:
                    $words = $tokenizer->tokenize( $child->textContent );
                    foreach ( $words as $word )
                    {
                        $tokens[] = array(
                            'word'   => $word,
                            'style'  => $rules,
                            'url'    => $url,
                            'target' => $target,
                        );
                    }
                    break;

                case XML_ELEMENT_NODE:
                    $tokens = array_merge(
                        $tokens,
                        $this->tokenize( $child, $tokenizer, true )
                    );
                    break;
            }
        }

        if ( !$recursed )
        {
            // Remove double spaces
            foreach ( $tokens as $nr => $token )
            {
                if ( ( $token['word'] === ezcDocumentPdfTokenizer::SPACE ) &&
                     isset( $tokens[$nr + 1] ) &&
                     ( $tokens[$nr + 1]['word'] === ezcDocumentPdfTokenizer::SPACE ) )
                {
                    $i = 1;
                    do {
                        unset( $tokens[$nr + $i] );
                    } while ( isset( $tokens[$nr + ( ++$i )] ) &&
                              ( $tokens[$nr + $i]['word'] === ezcDocumentPdfTokenizer::SPACE ) );
                }
            }
            $tokens = array_values( $tokens );

            // Remove optional starting spaces
            if ( count( $tokens ) &&
                 ( $tokens[0]['word'] === ezcDocumentPdfTokenizer::SPACE ) )
            {
                $tokens = array_slice( $tokens, 1 );
            }
        }

        return $tokens;
    }

    /**
     * Force split a word.
     *
     * Force the splitting of a word, which did not fit in a line alone and
     * could not be splitted using the hyphenator. We just search for the
     * maximum word part length which fits the available space.
     *
     * Could be improved to use a binary search on the word length, but this
     * shouldn't happen too often anyways.
     * 
     * @param string $word 
     * @param float $available 
     * @return array
     */
    protected function forceSplit( $word, $available )
    {
        $length = iconv_strlen( $word ) - 1;
        while ( $this->driver->calculateWordWidth( iconv_substr( $word, 0, $length ) ) > $available )
        {
            --$length;
        }

        return array(
            iconv_substr( $word, 0, $length ),
            iconv_substr( $word, $length )
        );
    }

    /**
     * Try to match tokens into lines
     *
     * Try to match tokens into lines of the given width. Returns an array with
     * words for each line. The words might already be split up by the
     * hyphenator.
     *
     * @param array $tokens
     * @param ezcDocumentPdfHyphenator $hyphenator
     * @param float $available
     * @return array
     */
    protected function fitTokensInLines( array $tokens, ezcDocumentPdfHyphenator $hyphenator, $available )
    {
        $lines    = array( array(
            'tokens' => array(),
            'height' => 0,
            'words'  => 0,
            'spaces' => 0,
        ) );
        $line     = 0;
        $consumed = 0;
        while ( $token = array_shift( $tokens ) )
        {
            // Handle forced line breaks
            if ( $token['word'] === ezcDocumentPdfTokenizer::FORCED )
            {
                // Continue rendering in next line
                $consumed = 0;
                $lines[++$line] = array(
                    'tokens' => array(),
                    'height' => 0,
                    'words'  => 0,
                    'spaces' => 0,
                );
                continue;
            }

            // Pure wrapping tokens are irrleveant to width calculation
            if ( $token['word'] === ezcDocumentPdfTokenizer::WRAP )
            {
                continue;
            }

            // Apply current styles
            foreach ( $token['style'] as $style => $value )
            {
                // Only pass whitelist of properties to the backend. This is not really 
                // the right place to do this, but it reduces the amount of calls to 
                // the backend and the size of the call log massively.
                if ( !( ( $style === 'font-style' ) ||
                        ( $style === 'font-weight' ) ||
                        ( $style === 'color' ) ||
                        ( $style === 'font-size' ) ||
                        ( $style === 'font-family' ) ) )
                {
                    continue;
                }

                $this->driver->setTextFormatting( $style, $value->value );
            }

            // Just add space to consumed wird width
            if ( $token['word'] === ezcDocumentPdfTokenizer::SPACE )
            {
                if ( $consumed > 0 )
                {
                    $consumed += $width = $this->driver->calculateWordWidth( ' ' );
                    $token['width']           = $width;
                    $lines[$line]['tokens'][] = $token;
                    $lines[$line]['spaces']++;
                }
                continue;
            }

            $wordStack = array(
                'tokens' => array(),
                'height' => 0,
                'words'  => 0,
                'spaces' => 0,
            );
            $wConsumed = 0;
            do {
                if ( ( $consumed + $wConsumed + ( $width = $this->driver->calculateWordWidth( $token['word'] ) ) ) < $available )
                {
                    // The word just fits into the current line
                    $token['width']        = $width;
                    $wordStack['tokens'][] = $token;
                    $wordStack['height']   = max( $lines[$line]['height'], $this->driver->getCurrentLineHeight() );
                    $wordStack['words']++;
                    $wConsumed            += $width;
                    
                    if ( !isset( $tokens[0] ) ||
                         ( $tokens[0]['word'] === ezcDocumentPdfTokenizer::WRAP ) ||
                         ( $tokens[0]['word'] === ezcDocumentPdfTokenizer::FORCED ) ||
                         ( $tokens[0]['word'] === ezcDocumentPdfTokenizer::SPACE ) )
                    {
                        // We are allowed to wrap, so we can continue with the
                        // next iteration and merge the current word stack with
                        // the line array.
                        $lines[$line]['tokens'] = array_merge( $lines[$line]['tokens'], $wordStack['tokens'] );
                        $lines[$line]['height'] = max( $lines[$line]['height'], $wordStack['height'] );
                        $lines[$line]['words'] += $wordStack['words'];
                        $consumed              += $wConsumed;
                        continue 2;
                    }

                    continue;
                }

                // Try to hyphenate the current word
                $hyphens = array_reverse( $hyphenator->splitWord( $token['word'] ) );
                foreach ( $hyphens as $hyphen )
                {
                    if ( ( $consumed + $wConsumed + ( $width = $this->driver->calculateWordWidth( $hyphen[0] ) ) ) < $available )
                    {
                        $second         = $token;
                        $second['word'] = $hyphen[1];
                        array_unshift( $tokens, $second );

                        $token['width']           = $width;
                        $token['word']            = $hyphen[0];
                        $lines[$line]['tokens'] = array_merge( $lines[$line]['tokens'], $wordStack['tokens'], array( $token ) );
                        $lines[$line]['height'] = max( $lines[$line]['height'], $wordStack['height'], $this->driver->getCurrentLineHeight() );
                        $lines[$line]['words'] += $wordStack['words'] + 1;

                        // Continue rendering in next line
                        $consumed = 0;
                        $lines[++$line] = array(
                            'tokens' => array(),
                            'height' => 0,
                            'words'  => 0,
                            'spaces' => 0,
                        );
                        continue 3;
                    }
                }

                if ( ( $consumed + $wConsumed ) <= 0 )
                {
                    // If we are already at the beginning of the line, and the
                    // word does still not fit, we forcefully split the word.
                    $hyphen = $this->forceSplit( $token['word'], $available );

                    $second         = $token;
                    $second['word'] = $hyphen[1];
                    array_unshift( $tokens, $second );

                    $token['width']         = $this->driver->calculateWordWidth( $hyphen[0] );
                    $token['word']          = $hyphen[0];
                    $lines[$line]['tokens'] = array( $token );
                    $lines[$line]['height'] = $this->driver->getCurrentLineHeight();
                    $lines[$line]['words']  = 1;

                    // Continue rendering in next line
                    $consumed = 0;
                    $lines[++$line] = array(
                        'tokens' => array(),
                        'height' => 0,
                        'words'  => 0,
                        'spaces' => 0,
                    );
                    continue 2;
                }

                // Did not fit using hyphenation, so retry after wrapping
                // the current word stack into the next line.
                array_unshift( $tokens, $token );

                // Still does not fit, move whole word stack into the next line
                // and try again
                $token['width'] = $width = $this->driver->calculateWordWidth( $token['word'] );
                $lines[++$line] = $wordStack;
                $consumed       = $wConsumed;
                continue 2;
            } while ( $token = array_shift( $tokens ) );
        }

        return $lines;
    }
}
?>
