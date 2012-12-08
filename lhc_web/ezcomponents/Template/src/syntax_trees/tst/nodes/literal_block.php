<?php
/**
 * File containing the ezcTemplateLiteralBlockTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Element representing a literal block and the literal text.
 *
 * The element consists of four cursors, the $startCursor points to the start of
 * the opening literal block while $endCursor points to the end of the closing
 * literal block. In addition the $textStartCursors points to the end of the
 * opening literal block and marks the start of the actual text, while
 * $textEndCursor points to the start of the closing literal block and marks
 * the end of the text.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateLiteralBlockTstNode extends ezcTemplateTextTstNode
{
    /**
     * The starting point for the text after the initial literal block
     * specified with a cursor.
     *
     * @var ezcTemplateCursor
     */
    public $textStartCursor;

    /**
     * The end point for the text after the inital literal block specified with
     * a cursor. This is also the starting point of the end literal block which
     * goes on until $endCursor.
     *
     * @var ezcTemplateCursor
     */
    public $textEndCursor;

    /**
     * The extracted text from the source code found between start and end cursor.
     * The text will exactly the same as the source code.
     */
    public $text;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->textStartCursor = null;
        $this->textEndCursor   = null;
    }

    /**
     * Stores the text from the source code using the $textStartCursor and
     * $textEndCursor and stores the result in the $text property.
     */
    public function storeText()
    {
        $this->text = $this->textStartCursor->subString( $this->textEndCursor->position );
    }
}
?>
