<?php
/**
 * File containing the ezcTemplateTextTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Element interface representing text found in the template source code.
 *
 * The specific text elements needs to inherit this function and set the
 * $text variable.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
abstract class ezcTemplateTextTstNode extends ezcTemplateCodeTstNode
{
    /**
     * The extracted text from the source code. How the text is extracted and
     * optionally filtered is up to the sub-class.
     */
    public $text;

    /**
     * An array of lines containing the extracted text in $text.
     *
     * Each line entry is an array where index 0 is the line string and index 1
     * is the EOL character(s).
     * <code>
     * array( array( 0 => "some string", 1 => "\n" ),
     *        array( 0 => "more text", 1 => "\r\n" ),
     *        array( 0 => "and more", 1 => false ) );
     * </code>
     *
     * @var array(array)
     */
    public $lines;

    /**
     * The calculated indentation value for the entire text block.
     *
     * @var string/false
     */
    protected $minimumWhitespace;

    /**
     * The column value of the first line in the text.
     *
     * @var int
     */
    protected $startColumn;

    /**
     * Constructs a new ezcTemplateTextTstNode
     *
     * @param ezcTemplateSourceCode $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end 
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->minimumWhitespace = null;
        $this->startColumn       = $start->column;
        $this->text              = false;
        $this->lines             = array();
    }

    /**
     * Returns the tree properties of this object.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'text' => $this->text );
    }

    /**
     * Returns true since text elements can always be children of blocks.
     *
     * @param ezcTemplateBlockTstNode $block
     * @return bool
     */
    public function canBeChildOf( ezcTemplateBlockTstNode $block )
    {
        // Text elements can always be child of blocks
        return true;
    }

    /**
     * {@inheritdoc}
     * Returns the minimum whitespace column by scanning all lines of the text
     * string.
     */
    public function minimumWhitespaceColumn()
    {
        if ( $this->minimumWhitespace !== null )
            return $this->minimumWhitespace;

        $minimum = false;
        foreach ( $this->lines as $i => $line )
        {
            // Skip the first line if it is placed after another element (column > 0 ).
            if ( $i == 0 &&
                 $this->startColumn > 0 )
                continue;

            // Find first non-whitespace character
            // Note: At this point it does not contain the EOL characters \r or \n
            if ( !preg_match( "#[^ \t\x0B]#", $line[0], $matches, PREG_OFFSET_CAPTURE ) )
            {
                continue;
            }

            if ( $minimum === false )
            {
                $minimum = $matches[0][1];
            }
            else
            {
                $minimum = min( $minimum, $matches[0][1] );
            }
        }
        $this->minimumWhitespace = $minimum;
        return $this->minimumWhitespace;
    }

    /**
     * Returns the column of the first line.
     *
     * If this is non-zero it means the first line is placed after another
     * element,e.g.
     * <code>
     * {some_block} and a
     * string over two lines
     * </code>
     *
     * @return int
     */
    public function firstLineColumn()
    {
        return $this->startColumn;
    }

}
?>
