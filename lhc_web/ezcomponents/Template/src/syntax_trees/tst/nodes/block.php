<?php
/**
 * File containing the ezcTemplateBlockTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Block elements in parser trees.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateBlockTstNode extends ezcTemplateCodeTstNode
{

    /**
     * Array of all child elements.
     *
     * @var array
     */
    public $children;

    /**
     * Is true if this block is a closing block (ie. {/...}), false otherwise.
     * @var bool
     */
    public $isClosingBlock;

    /**
     * Is true if this block can nest other elements.
     * A closing block is required to end the nesting in this case.
     *
     * @see $isClosingBlock
     * @var bool
     */
    public $isNestingBlock;

    /**
     * The name of the block.
     *
     * @var string
     */
    public $name;

    /**
     * The calculated indentation value for the entire text block.
     *
     * @var string/false
     */
    protected $minimumWhitespace;

    /**
     * The parent block element of the current block.
     *
     * @var ezcTemplateBlockTstNode
     */
    public $parentBlock;

    /**
     * The starting point for the closing marker (the slash) specified with a cursor
     *
     * <code>
     * {/foreach}
     *  ^
     * </code>
     *
     * @var ezcTemplateCursor
     */
    public $closingCursor;

    /**
     * Constructs a new ezcTemplateBlockTstNode
     *
     * @param ezcTemplateSourceCode $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end  
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->children = array();
        $this->isClosingBlock = false;
        $this->parentBlock = null;
        $this->closingCursor = null;
        // Most blocks use nesting
        $this->isNestingBlock = true;
        $this->minimumWhitespace = null;
    }

    /**
     * Returns the current tree properties
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'name'           => $this->name,
                      'isClosingBlock' => $this->isClosingBlock,
                      'isNestingBlock' => $this->isNestingBlock,
                      'children'       => $this->children );
    }

    /**
     * Checks if the block has any children, returns true or false.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return count( $this->children );
    }

    /**
     * Adds the element $child as child of this block element.
     *
     * @param ezcTemplateTstNode $child
     */
    public function appendChild( ezcTemplateTstNode $child )
    {
        $this->children[] = $child;
    }

    /**
     * Returns the first child element of the block.
     *
     * @return ezcTemplateTstNode
     * @throws ezcTemplateTstNodeException::NO_FIRST_CHILD if there are no children.
     */
    public function getFirstChild()
    {
        if ( count( $this->children ) === 0 )
            throw new ezcTemplateTstNodeException( ezcTemplateTstNodeException::NO_FIRST_CHILD );

        return $this->children[0];
    }

    /**
     * Removes the first child element of the block.
     *
     * @throws ezcTemplateTstNodeException::NO_FIRST_CHILD if there are no children.
     */
    public function removeFirstChild()
    {
        if ( count( $this->children ) === 0 )
            throw new ezcTemplateTstNodeException( ezcTemplateTstNodeException::NO_FIRST_CHILD );

        array_shift( $this->children );
    }

    /**
     * Returns the last child element of the block.
     *
     * @return ezcTemplateTstNode
     * @throws ezcTemplateTstNodeException::NO_LAST_CHILD if there are no children.
     */
    public function getLastChild()
    {
        if ( count( $this->children ) === 0 )
            throw new ezcTemplateTstNodeException( ezcTemplateTstNodeException::NO_LAST_CHILD );

        return $this->children[count( $this->children ) - 1];
    }

    /**
     * Removes the last child element of the block.
     *
     * @throws ezcTemplateTstNodeException::NO_LAST_CHILD if there are no children.
     */
    public function removeLastChild()
    {
        if ( count( $this->children ) === 0 )
            throw new ezcTemplateTstNodeException( ezcTemplateTstNodeException::NO_LAST_CHILD );

        array_pop( $this->children );
    }

    /**
     * Returns true if the block is not meant to start a new nesting level.
     *
     * @param ezcTemplateBlockTstNode $block
     * @return bool
     */
    public function canBeChildOf( ezcTemplateBlockTstNode $block )
    {
        // If the current block cannot start a new nesting level
        // it means it should be considered an standalone block and
        // can be a child.
        if ( !$this->isNestingBlock )
        {
            return true;
        }
        return false;
    }

    /**
     * Callback for closing an open block element.
     *
     * This can be used to perform changes to the open block before the closing
     * block (this) is discarded.
     *
     * @param ezcTemplateBlockTstNode $openBlock The block which is currently open.
     */
    public function closeOpenBlock( ezcTemplateBlockTstNode $openBlock )
    {
        // Does nothing, re-implement to perform your duties.
    }

    /**
     * Checks if the element object $element can be handled by the current block.
     *
     * This method can be overridden to handle custom elements for instance {else}
     * blocks of {if} control structures.
     * Returns true if it can be handled, false otherwise. If it can be handled
     * the caller must make sure handleElement() is used.
     *
     * @param ezcTemplateTstNode $element The element object which should be checked for special handling.
     * @return bool
     * Note: This method will be called if the element could not be added as a
     *       child or used to close the block.
     */
    public function canHandleElement( ezcTemplateTstNode $element )
    {
        // Default is to not handle any elements, sub-classes needs to
        // figure out which ones it can handle.
        return false;
    }

    /**
     * Empty function
     *
     * @todo Can be removed?
     *
     * @param mixed $parentElement
     * @return void
     */
    public function canAttachToParent( $parentElement )
    {
    }

    /**
     * Passes control of $element to the current block for special handling.
     *
     * The sub-classes need to re-implement canHandleElement() and this method
     * to handle special cases which are not covered by the general child/closure
     * rules in the program parser.
     * Typically block elements which have special child blocks need to
     * re-implement this.
     *
     * @param ezcTemplateTstNode $element The element object which should be handled.
     * Note: This method will be called if the element could not be added as a
     *       child or used to the block.
     */
    public function handleElement( ezcTemplateTstNode $element )
    {
        // Sub-classes need to implement the special handling here
        if ( $this === $element )
        {
            throw new ezcTemplateInternalException( "Detected invalid recursion creation in parser element " . get_class( $this ) );
        }

        if ( $element instanceof ezcTemplateLoopTstNode )
        {
            throw new ezcTemplateParserException( $element->source, $element->startCursor, $element->startCursor, ezcTemplateSourceToTstErrorMessages::MSG_UNEXPECTED_BREAK_OR_CONTINUE );
        }

        if ( $element instanceof ezcTemplateConditionBodyTstNode )
        {
            throw new ezcTemplateParserException( $element->source, $element->startCursor, $element->startCursor, 
                sprintf( ezcTemplateSourceToTstErrorMessages::MSG_UNEXPECTED_BLOCK, $element->name ) );
        }

        if ( $element instanceof ezcTemplateCaseTstNode )
        {
            throw new ezcTemplateParserException( $element->source, $element->startCursor, $element->startCursor, 
                sprintf( ezcTemplateSourceToTstErrorMessages::MSG_UNEXPECTED_BLOCK, $element->name ) );
        }



        $this->children[] = $element;

        return true;

    }

    /**
     * {@inheritdoc}
     * Returns the minimum column of all children.
     */
    public function minimumWhitespaceColumn()
    {
        if ( $this->minimumWhitespace !== null )
            return $this->minimumWhitespace;

        $this->minimumWhitespace = $this->minimumElementsColumn( $this->children );
        return $this->minimumWhitespace;
    }

    /**
     * Finds the minimum whitespace column for all the specified elements.
     *
     * This method is useful for sub-classes of the block element in case they have
     * multiple element lists and need to fetch the column value from all of them.
     *
     * @param array(ezcTemplateTstNode) $elements Array of element objects to check.
     * @return int/false
     */
    protected function minimumElementsColumn( Array $elements )
    {
        $minimum = false;
        foreach ( $elements as $element )
        {
            $column = $element->minimumWhitespaceColumn();
            // If the column is false it means there it only contains whitespace
            // or could figure out a minimum column
            if ( $column === false )
                continue;

            if ( $minimum === false )
            {
                $minimum = $column;
            }
            else
            {
                $minimum = min( $minimum, $column );
            }
        }
        return $minimum;
    }

    /**
     * Trims away the minimum indentation for the current block.
     *
     * Note: If a sub-class of this block element class uses another variable
     *       than $children for child elements or uses multiple lists then it
     *       needs to re-implement this method and call
     *       $removal->trimBlockLevelIndentation() for the correct list.
     *
     * @param ezcTemplateWhitespaceRemoval $removal
     *        The removal object which knows how to get rid of indentation for the current level.
     */
    public function trimIndentation( ezcTemplateWhitespaceRemoval $removal )
    {
        // Tell the removal object to trim our children
        $removal->trimBlockLevelIndentation( $this, $this->children );
    }

    /**
     * Trims away the whitespace and EOL marker from the block line.
     * The whitespace and EOL marker is found in the first child element
     * which must be a text block element.
     *
     * Note: If a sub-class of this block element class uses another variable
     *       than $children for child elements or uses multiple lists then it
     *       needs to re-implement this method and call
     *       $removal->trimBlockLine() for the correct list.
     *
     * @param ezcTemplateWhitespaceRemoval $removal
     *        The removal object which knows how to get rid of block line whitespace.
     */
    public function trimLine( ezcTemplateWhitespaceRemoval $removal )
    {
        if ( count( $this->children ) == 0 )
            return;

        if ( $this->children[0] instanceof ezcTemplateTextTstNode )
        {
            // Tell the removal object to trim our first text child
            $removal->trimBlockLine( $this, $this->children[0] );
        }

        // Tell the removal object to trim text blocks after the current block
        // and after all sub-blocks.
        $removal->trimBlockLines( $this, $this->children );
    }
}
?>
