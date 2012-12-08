<?php
/**
 * File containing the ezcTemplateProgramSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Element parser for the program part of the template code.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateProgramSourceToTstParser extends ezcTemplateSourceToTstParser
{
    /**
     * The program element of the parse operation if the parsing was successful.
     *
     * @var ezcTemplateProgramTstNode
     */
    public $program;

    /**
     * The last block which was processed by the parser. This is used to
     * figure out the correct nesting of block elements.
     *
     * @var ezcTemplateTstNode
     */
    private $lastBlock;

    /**
     * Passes control to parent.
     * 
     * @param ezcTemplateParser $parser
     * @param ezcTemplateSourceToTstParser $parentParser
     * @param ezcTemplateCursor $startCursor
     */
    function __construct( ezcTemplateParser $parser, /*ezcTemplateSourceToTstParser*/ $parentParser, /*ezcTemplateCursor*/ $startCursor )
    {
        parent::__construct( $parser, $parentParser, $startCursor );
        $this->program = null;
        $this->lastBlock = null;
    }

    /**
     * Parses the code by looking for start of expression blocks and then
     * passing control to the block parser (ezcTemplateBlockSourceToTstParser). The
     * text which is not covered by the block parser will be added as
     * text elements.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        $this->program = new ezcTemplateProgramTstNode( $this->parser->source, $this->startCursor, $cursor );
        $this->lastBlock = $this->program;

        while ( !$cursor->atEnd() )
        {
            // Find the first block
            $bracePosition = $cursor->findPosition( "{", true );
            if ( $bracePosition === false )
            {
                $cursor->gotoEnd();
                // This will cause handleSuccessfulResult() to be called
                return true;
            }

            // Reached a block {...}
            $cursor->gotoPosition( $bracePosition );
            $blockCursor = clone $cursor;
            $cursor->advance( 1 );
            if ( $this->lastCursor->length( $blockCursor ) > 0 )
            {
                $textElement = new ezcTemplateTextBlockTstNode( $this->parser->source, clone $this->lastCursor, clone $blockCursor );
                $this->handleElements( array( $textElement ) );
                unset( $textElement );
            }

            $this->startCursor->copy( $blockCursor );
            $this->lastCursor->copy( $cursor );
            if ( !$this->parseRequiredType( 'Block', $this->startCursor, false ) )
            {
                return false;
            }
            $this->startCursor->copy( $cursor );

            $elements = $this->lastParser->elements;
            // Sanity checking to make sure element list does not contain duplicates,
            // this avoids having infinite recursions
            $count = count( $elements );
            if ( $count > 0 )
            {
                $offset = 0;
                while ( $offset < $count )
                {
                    $element = $elements[$offset];
                    for ( $i = $offset + 1; $i < $count; ++$i )
                    {
                        if ( $element === $elements[$i] )
                            throw new ezcTemplateInternalException( "Received element list with duplicate objects from parser " . get_class( $this->lastParser ) );
                    }
                    ++$offset;
                }
            }
            $this->handleElements( $elements );
        }

        // This will cause handleSuccessfulResult() to be called
        return true;
    }

    /**
     * Performs checking on the parse result.
     *
     * The method will check if there are more text after the current cursor
     * location and if so appends a new ezcTextElement object containing the
     * text.
     *
     * It also checks if the $lastBlock contains the current program parser, if it
     * does not it means the nesting in the current source code is incorrect.
     *
     * @param ezcTemplateCursor $lastCursor
     * @param ezcTemplateCursor $cursor
     *
     * @throws ezcTemplateParserException if blocks are incorrectly nested.
     *
     * @return void
     */
    protected function handleSuccessfulResult( ezcTemplateCursor $lastCursor, ezcTemplateCursor $cursor )
    {
        if ( $lastCursor->length( $cursor ) > 0 )
        {
            $textElement = new ezcTemplateTextBlockTstNode( $this->parser->source, clone $lastCursor, clone $cursor );
            $this->handleElements( array( $textElement ) );
        }

        if ( $this->lastBlock === null )
        {
            throw new ezcTemplateInternalException( "lastBlock is null, should have been a parser element object." );
        }

        if ( !$this->lastBlock instanceof ezcTemplateProgramTstNode )
        {
            $parents = array();

            // Calculate level of the last block, this used to indent the last block
            $level = 0;
            $block = $this->lastBlock;
            while ( $block->parentBlock !== null &&
                    !( $block->parentBlock instanceof ezcTemplateProgramTstNode ) )
            {
                if ( $block === $block->parentBlock )
                {
                    throw new ezcTemplateInternalException( "Infinite recursion found in parser element " . get_class( $block ) );
                }

                ++$level;
                $block = $block->parentBlock;
            }

            $block = $this->lastBlock;

            // Go trough all parents until the root is reached
            while ( $block->parentBlock !== null &&
                    !( $block->parentBlock instanceof ezcTemplateProgramTstNode ) )
            {
                if ( $block === $block->parentBlock )
                {
                    throw new ezcTemplateInternalException( "Infinite recursion found in parser element " . get_class( $block ) );
                }

                $block = $block->parentBlock;
                --$level;
                $parents[] = str_repeat( "  ", $level ) . "{" . $block->name . "} @ {$block->startCursor->line}:{$block->startCursor->column}:";
            }

            $parents = array_reverse( $parents );
            $treeText = "The current nesting structure:\n" . join( "\n", $parents );


            throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor,
                                                 "Incorrect nesting in code, close block {/" . $this->lastBlock->name . "} expected." );
        }

        // Get rid of whitespace for the block line of the program element
        $this->parser->trimBlockLine( $this->program );
    }

    /**
     * Handles elements
     *
     * @param array(ezcTemplateTstNode) $elements
     * @return void
     */ 
    public function handleElements( $elements )
    {
        foreach ( $elements as $element )
        {
            if ( $element instanceof ezcTemplateBlockTstNode && $element->isClosingBlock ) 
            {
                // Check for closing of current block

 //                echo ("Closing block: ".  get_class( $element ) ."\n"  );
                $this->closeOpenBlock( $element );
                $this->parser->symbolTable->decreaseScope();
            }
            else 
            {
                // This method throws an exception if the node cannot be attached.
                $element->canAttachToParent( $this->lastBlock );

                $this->lastBlock->handleElement( $element );

                if ( $element instanceof ezcTemplateBlockTstNode && $element->isNestingBlock)
                {

                    // No special handling required so we check if the element
                    // is a nesting block and should start a new nesting level

                    $element->parentBlock = $this->lastBlock;
                    $this->lastBlock = $element;

                    $this->parser->symbolTable->increaseScope();
                }
            }
        }
    }

    /**
     * Matches an open with an closing block.
     *
     * @param ezcTemplateTstNode $element
     * @throws ezcTemplateParserException for non matching open and close blocks.
     * @return void
     */ 
    protected function closeOpenBlock( $element )
    {
        // The previous element must be a block element,
        // if not throw an exception
        if ( !$this->lastBlock instanceof ezcTemplateBlockTstNode )
        {
            throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                              "Found closing block {" . $element->name . "} without a previous block element <" . get_class( $this->lastBlock ) . ">" );

        }

        // Check for closing blocks that do not belong to an opening block.
        if ( $this->lastBlock->parentBlock === null && $element->isClosingBlock )
        {
            if ( $element instanceof ezcTemplateCustomBlockTstNode )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->startCursor, 
                    "The custom block: {".$element->name."} should not have a closing block. Check the custom block definition. " );
             }
            else
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->startCursor, 
                    "Found closing block {/". $element->name."} without an opening block." ); 
            }
        }
         
        // The name of the previous element must match the closing block,
        // if not throw an exception
        if ( $this->lastBlock->name != $element->name )
        {
            throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, 
                "Open and close block do not match: {". $this->lastBlock->name ."} and {/".$element->name. "}" );
        }

        // Sanity check
        if ( $this->lastBlock->parentBlock === null )
        {
            throw new ezcTemplateInternalException( "Parent block of last block <" . get_class( $this->lastBlock ) . "> is null, should not happen." );
        }

        // Call the closing element with the block element it closes,
        // this allows it to update the open block if required.
        $element->closeOpenBlock( $this->lastBlock );

        // Tell the main parser to trim indentation for the block,
        // the whitespace trimming rules are defined within the main parser.
        $this->parser->trimBlockLevelIndentation( $this->lastBlock );

        // Get rid of whitespace for the block line
        $this->parser->trimBlockLine( $this->lastBlock );

        // Go up (closer to program) one level in the nested tree structure
        $this->lastBlock = $this->lastBlock->parentBlock;
    }
}

?>
