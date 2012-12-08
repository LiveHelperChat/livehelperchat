<?php
/**
 * File containing the ezcTemplateTextBlockTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Element represents a text portion in the template source code.
 *
 * The text portions are the text which are placed in between the template
 * blocks. The text portions will be stripped of escaped braces when being
 * read and then placed in the member property $text.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateTextBlockTstNode extends ezcTemplateTextTstNode
{
    /**
     * The extracted text from the source code found between start and end cursor.
     * The text may differ from the source code since output whitespace cleanup
     * and replacements of escaped character have been processed.
     *
     * @var string
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
     * Extracts the text the cursor point to and splits it into lines.
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->text  = self::stripText( $start->subString( $end->position ) );
        $this->lines = $this->splitIntoLines( $this->text );
    }

    /**
     * Cleans up any escaped curly brackets by removing the escape character
     * leaving the bracket intact. The cleaned up text string is returned.
     *
     * @param string $text The text to be stripped.
     * @return string
     */
    static public function stripText( $text )
    {
        $text = str_replace( array( "\\{", "\\}", "\\\\", "\\\r\n", "\\\n", "\\\r" ),
                             array( "{",   "}",   "\\",   "",       "",     ""     ),
                             $text );
        return $text;
    }

    /**
     * Splits the text string $text into an array of lines.
     *
     * Each line entry is an array where index 0 is the line string and index 1
     * is the EOL character(s). The EOL character(s) can be set to false.
     * <code>
     * array( array( 0 => "some string", 1 => "\n" ),
     *        array( 0 => "more text", 1 => "\r\n" ),
     *        array( 0 => "and more", 1 => false ) );
     * </code>
     *
     * Note: The last line will always have the EOL entry set to false
     *
     * @param string $text The string to split.
     * @return array(array)
     */
    protected function splitIntoLines( $text )
    {
        // Split into lines including the EOL string
        $elements = preg_split( "#(\r\n|\r|\n)#", $text, -1, PREG_SPLIT_DELIM_CAPTURE );

        // Rebuild into line structures
        $lines = array();
        $count = count( $elements );
        for ( $i = 0; $i < $count; )
        {
            $line = array( 0 => $elements[$i],
                           1 => false );
            if ( $i + 1 < $count )
                $line[1] = $elements[$i + 1];

            $lines[] = $line;

            $i += 2;
        }
        return $lines;
    }

    /**
     * Sets the text string from an array of lines.
     *
     * Each line entry is an array where index 0 is the line string and index 1
     * is the EOL character(s). The EOL character(s) can be set to false.
     * <code>
     * array( array( 0 => "some string", 1 => "\n" ),
     *        array( 0 => "more text", 1 => "\r\n" ),
     *        array( 0 => "and more", 1 => false ) );
     * </code>
     *
     * Note: The last line will always have the EOL entry set to false
     *
     * @param array(array) $lines The lines to join together into text string.
     */
    public function setTextLines( $lines )
    {
        $this->lines = $lines;
        $this->text = $this->joinTextLines( $lines );
    }

    /**
     * Joins the lines together to form a text string and returns it.
     *
     * Each line entry is an array where index 0 is the line string and index 1
     * is the EOL character(s). The EOL character(s) can be set to false.
     * <code>
     * array( array( 0 => "some string", 1 => "\n" ),
     *        array( 0 => "more text", 1 => "\r\n" ),
     *        array( 0 => "and more", 1 => false ) );
     * </code>
     *
     * Note: The last line will always have the EOL entry set to false
     *
     * @param array(array) $lines The lines to join together into text string.
     * @return string
     */
    protected function joinTextLines( $lines )
    {
        $text = '';
        foreach ( $lines as $line )
        {
            $text .= $line[0] . $line[1];
        }
        return $text;
    }

}
?>
