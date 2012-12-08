<?php
/**
 * File containing the ezcTemplateWhitespaceRemoval class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Trims away whitespace from parser elements.
 *
 * This class can perform several types of whitespace removal on the parsed 
 * result to ensure that the output given to the end user does not contain 
 * unneccesary whitespaces which can be important in some output contexts.  It 
 * is important to note that this will only remove whitespace from the parsed 
 * result and is not applied at run-time, this means that whitespace in 
 * outputted strings are kept.
 *
 * The various removal types are configurable in the constructor which allows 
 * it to be tailored to what is set as the current output context, e.g.  
 * whitespace removal for plain text might be different than XHTML output.
 * Controlling the removal types are done with boolean switches in member 
 * variables, they are:
 *
 * - $trimTrailing - If enabled it will remove the trailing whitespace from 
 * text blocks after the last block in the code, it checks each line to see if 
 * it contains whitespace only and if it does the line is removed.
 * - $trimLeading - Same as $trimTrailing but the trimming is done for the 
 * leading lines of the text block found before the first block in the code.
 * - $trimBlockEol - Trims away the whitespace and newline after all command 
 * blocks, this essentially makes the block line disappear from the output.
 * - $trimIndent - Trims away whitespace for each line in each block level by 
 * using the minimum column as the last trimming point. All lines in the same 
 * block level will get the same amount of whitespace removed.
 *
 * Example of leading whitespace removal:
 * <code>
 * "\n" .
 * "    " .
 * "  some text\n" .
 * "  "
 * becomes:
 * "  some text\n" .
 * "  "
 * </code>
 * here whitespace is kept after the first non-whitespace line.
 *
 * Example of trailing whitespace removal:
 * <code>
 * "\n" .
 * "    " .
 * "  some text\n" .
 * "  "
 * becomes:
 * "\n" .
 * "    " .
 * "  some text"
 * </code>
 * here whitespace is kept before the first non-whitespace line.
 *
 * Example of block-line eol removal:
 * <code>
 * "{if}\n" .
 * "    {$item}\n" .
 * "{/if}    \n"
 * becomes
 * "{if}" .
 * "    {$item}\n" .
 * "{/if}"
 * </code>
 *
 * here the whitespace with EOL marker is removed only at the end of the the 
 * block line, this ensures that critical newlines are kept for the {$item} 
 * code and that the {if} block do not add extra newlines
 *
 * Example of indent removal:
 * <code>
 * "{if}\n" .
 * "    {$item}\n" .
 * "{/if}    \n"
 * becomes
 * "{if}" .
 * "    {$item}\n" .
 * "{/if}"
 * </code>
 *
 * here the whitespace with EOL marker is removed only at the end of the the 
 * block line, this ensures that critical newlines are kept for the {$item} 
 * code and that the {if} block do not add extra newlines
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateWhitespaceRemoval
{
    /**
     *
     */
    public function __construct()
    {
        $this->trimTrailing = true; // Remove trailing whitespace after last block
        $this->trimLeading = true; // Remove leading whitespace before first block
        $this->trimBlockEol = true; // Remove whitespace found on the same line as the end of the block definition
        $this->trimIndent   = true; // Remove indentation whitespace until the minimum column of the current column is reached
        $this->tabSize = 8;         // Size of vertical tabs
    }

    /**
     * Trims away trailing and leading whitespace lines from the top of the
     * element tree.
     *
     * @param ezcTemplateProgramTstNode $tree The program element for the tree.
     */
    public function trimProgram( ezcTemplateProgramTstNode $tree )
    {
        if ( !$tree->hasChildren() )
        {
            // echo "has no children\n";
            return;
        }

        if ( $this->trimLeading )
        {
            // echo "trim leading\n";
            $child = $tree->getFirstChild();
            // We only touch text block elements, not literal text
            if ( $child instanceof ezcTemplateTextBlockTstNode )
            {
                $lines = $this->trimLeading( $child->lines );

                // Set back modified lines if they are modified
                if ( $lines !== false )
                {
                    $child->setTextLines( $lines );
                }
            }
            else
            {
                // echo "first child is not text\n";
            }
        }

        if ( $this->trimTrailing )
        {
            // echo "trim trailing\n";
            $child = $tree->getLastChild();
            // We only touch text block elements, not literal text
            if ( $child instanceof ezcTemplateTextBlockTstNode )
            {
                $lines = $this->trimTrailing( $child->lines );

                // Set back modified lines if they are modified
                if ( $lines !== false )
                {
                    $child->setTextLines( $lines );
                }
            }
            else
            {
                // echo "last child is not text\n";
            }
        }
    }

    /**
     * Trim away the minimum indentation level for all elements in $elements.
     *
     * @param ezcTemplateBlockTstNode $parentBlock The block which owns elements in $elements.
     * @param array(ezcTemplateTstNode) $elements List of elements to trim.
     */
    public function trimBlockLevelIndentation( ezcTemplateTstNode $parentBlock, Array $elements )
    {
        // First figure out the smallest amount of indentation that can be removed
        $indentation = $parentBlock->minimumWhitespaceColumn();

            $nrOfElements = sizeof( $elements );
            for ( $el = 0; $el < $nrOfElements ; $el++ )
            {
                $element = $elements[$el];

                if ( $element instanceof ezcTemplateTextBlockTstNode )
                {
                    $lines = $element->lines;

                    $count = count( $lines );
                    for ( $i = 0; $i < $count; ++$i )
                    {
                        // Skip the first line if it is placed after another element (column > 0 ).
                        // We can only modify lines with leading point at column 0.
                        if ( $i == 0 && $element->firstLineColumn() > 0 )
                        {
                            // It prevents some text nodes from removal.
                           continue;
                        }

                        // Trim the line and leave EOL alone
                        $lines[$i][0] = $this->trimIndentationLine( $lines[$i][0], $indentation );

                        if ( $i ==  $count - 1 )
                        {
                            if ( $el < $nrOfElements - 1 )
                            {
                                if ( $elements[ $el + 1 ] instanceof ezcTemplateBlockTstNode && !( $elements[ $el + 1 ] instanceof ezcTemplateOutputBlockTstNode ) )
                                {
                                    $trimmed = trim( $lines[$i][0], " \t" );
                                    if ( strlen( $trimmed  ) == 0 ) 
                                    {
                                        $lines[$i][0] = "";
                                    }
                                }
                            }
                            else
                            {
                                if ( $parentBlock instanceof ezcTemplateBlockTstNode  && !( $parentBlock instanceof ezcTemplateOutputBlockTstNode ) )
                                {
                                    $last = sizeof( $lines ) -1;

                                    $trimmed = trim( $lines[$last][0], " \t" );
                                    if ( strlen ( trim( $lines[$last][0], " \t") ) == 0  ) 
                                    {
                                        $lines[ $last ][0] = "";
                                    }
                                }
                            }
                        } 
                    }

                    $element->setTextLines( $lines );
                }
                elseif ( $element instanceof ezcTemplateConditionBodyTstNode )
                {
                    $this->trimBlockLevelIndentation( $element, $element->children );
                }
        }
    }

    /**
     * Trim away the excess whitespace which makes up the block lines.
     *
     * It will examine all elements in $elements until it finds a text block.
     * If the text block is found directly after a block element (of any kind)
     * it will trim the first line of the text.
     *
     * @param ezcTemplateBlockTstNode $parentBlock
     *        The block which owns the text element.
     * @param array(ezcTemplateTstNode) $elements
     *        Element list to check for block objects.
     * Note: The block line is considered the first line of a text block placed
     */
    public function trimBlockLines( ezcTemplateTstNode $parentBlock, Array $elements )
    {
        // Trim after all sub-blocks
        $previousSibling = null;
        foreach ( $elements as $element )
        {
            if ( $element instanceof ezcTemplateTextTstNode &&
                 ( $previousSibling instanceof ezcTemplateBlockTstNode &&
                   !$previousSibling instanceof ezcTemplateOutputBlockTstNode ) )
            {
                // This text element is placed directly after a block element
                // so we need to trim it.
                $this->trimBlockLine( $parentBlock, $element );
            }
            $previousSibling = $element;
        }
    }

    /**
     * Trim away the excess whitespace which makes up the block line.
     *       after a block element.
     *
     * The first line of the text block is examined, if it contains whitespace
     * only the line will be emptied and the EOL marker is disabled (set to
     * false).
     *
     * @see ezcTemplateTextTstNode::setTextLines for details of the line format
     *      of text blocks.
     * @param ezcTemplateBlockTstNode $parentBlock
     *        The block which owns the text element.
     * @param ezcTemplateTextTstNode $textElement
     *        Text element to trim.
     * Note: The block line is considered the first line of a text block placed
     */
    public function trimBlockLine( ezcTemplateTstNode $parentBlock, ezcTemplateTextTstNode $textElement )
    {
        $lines = $textElement->lines;
        if ( count( $lines ) == 0 )
            return;

        $line = $lines[0];
        // Find first non-whitespace character, if we find one we cannot trim
        if ( preg_match( "#[^ \t\x0B]#", $line[0] ) )
        {
            return;
        }

        // Clear line text and EOL marker
        $line[0] = '';
        $line[1] = false;
        $lines[0] = $line;
        $textElement->setTextLines( $lines );
    }

    /**
     * Trims characters in the text line $line until the required indentation
     * level is reached.
     * The function will check for vertical tabs \t and handle that specially
     * by using self::$tabSize for size.
     * If the line is too short it will become an empty string.
     *
     * Note: If the indentation is stops within a vertical tab, the returned
     *       string will start right after the tab character.
     * @param string $line
     *        A text string contain a line but without the EOL marker.
     * @param int    $indentation
     *        The required indentation level.
     * @return string
     */
    public function trimIndentationLine( $line, $indentation )
    {
        $len = strlen( $line );
        $i = 0;
        $column = 0;
        while ( $i < $len )
        {
            if ( $column == $indentation )
            {
                return (string)substr( $line, $i );
            }

            // Vertical tabs need special care
            if ( $line[$i] == "\t" )
            {
                $tabCharacters = $column % $this->tabSize;
                $tabLeft = $this->tabSize - $tabCharacters;
                $column += $tabLeft;
                if ( $column >= $indentation )
                {
                    // Return string after tab character
                    return (string)substr( $line, $i + 1 );
                }
            }
            else
            {
                if ( $column >= $indentation )
                {
                    return (string)substr( $line, $i );
                }
                ++$column;
            }
            ++$i;
        }

        // Identation is larger than string so we return an empty one.
        return '';
    }

    /**
     * Removes all lines (from the start) which are empty after trimming.
     * As soon as a non-empty line is found it stops the process and keeps the
     * rest of the lines and returns the modified lines or false it nothing
     * was modified.
     *
     * For instance the text:
     * <code>
     * "    \n" .
     * "\n" .
     * "  abc\n" .
     * "    \n" .
     * "\n"
     * </code>
     * Will be turned into:
     * <code>
     * "  abc\n" .
     * "    \n" .
     * "\n"
     * </code>
     *
     * @param array(array) $lines The text lines to trim.
     * @return array(array)/false
     */
    public function trimLeading( $lines )
    {
        $count = count( $lines );
        for ( $i = 0; $i < $count; ++$i )
        {
            $line = $lines[$i];
            $lineText = ltrim( $line[0] );
            if ( strlen( $lineText ) != 0 )
            {
                break;
            }

            // The line is empty so we disable the line by setting empty line
            // text and removing the EOL marker.
            $line[0] = $lineText;
            $line[1] = false;
            $lines[$i] = $line;
        }
        // If $i is 0 it means no lines have been modified.
        if ( $i == 0 )
            return false;

        return $lines;
    }

    /**
     * Removes all lines (from the end) which are empty after trimming.
     * As soon as a non-empty line is found it stops the process and keeps the
     * rest of the lines and returns the modified lines, or false it nothing
     * was modified.
     *
     * For instance the text:
     * <code>
     * "    \n" .
     * "\n" .
     * "  abc\n" .
     * "    \n" .
     * "\n"
     * </code>
     * Will be turned into:
     * <code>
     * "    \n" .
     * "\n" .
     * "  abc\n"
     * </code>
     *
     * and the text:
     * <code>
     * "    \n" .
     * "\r\n" .
     * "  \r" .
     * "    \n" .
     * "\n"
     * </code>
     * Will be turned into:
     * <code>
     * "\n"
     * </code>
     *
     * @param array(array) $lines The text lines to trim.
     * @return array(array)/false
     */
    public function trimTrailing( $lines )
    {
        $count = count( $lines );
        for ( $i = $count - 1; $i >= 0; --$i )
        {
            $line = $lines[$i];
            $lineText = rtrim( $line[0] );
            if ( strlen( $lineText ) != 0 )
            {
                // EOL marker is kept as it is, while line is replaced with new trimmed text
                $lines[$i] = $line;
                break;
            }

            // The line is empty so we disable the line by setting empty line
            // text and removing the EOL marker.
            $line[0] = $lineText;
            // Keep the EOL marker if this is the top-most line
            if ( $i > 0 )
            {
                $line[1] = false;
            }
            $lines[$i] = $line;
        }
        // If $i is is the same as the starting iteration value
        // it means no lines have been modified.
        if ( $i == $count - 1 )
            return false;

        return $lines;
    }
}

?>
