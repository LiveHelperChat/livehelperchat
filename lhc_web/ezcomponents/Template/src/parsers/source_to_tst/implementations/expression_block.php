<?php
/**
 * File containing the ezcTemplateExpressionBlockSourceToTstParser class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for template blocks containing an expression only.
 *
 * Parses inside the blocks {...} and looks for an expression by using the
 * ezcTemplateExpressionSourceToTstParser class.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateExpressionBlockSourceToTstParser extends ezcTemplateSourceToTstParser
{
    /**
     * The bracket start character.
     * @var string
     */
    public $startBracket;

    /**
     * The bracket end character.
     * @var string
     */
    public $endBracket;

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
        $this->startBracket = '{';
        $this->endBracket = '}';
        $this->block = null;
    }

    /**
     * Returns true if the current character is a curly bracket (}) which means
     * the end of the block.
     *
     * @param ezcTemplateCursor $cursor
     * @param ezcTemplateTstNode $operator  
     * @param bool $finalize
     * @return bool
     */
    public function atEnd( ezcTemplateCursor $cursor, /*ezcTemplateTstNode*/ $operator, $finalize = true )
    {
        if ( $cursor->current( strlen( $this->endBracket ) ) == $this->endBracket )
        {
            if ( !$finalize )
                return true;

            // reached end of expression
            $cursor->advance( 1 );
            $this->block->endCursor = clone $this->block->endCursor;
            return true;
        }
        return false;
    }

    /**
     * Parses the expression by using the ezcTemplateExpressionSourceToTstParser class.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        $rawBlock = false;

        if ( $cursor->match( "raw" ) )
        {
            $rawBlock = true;
            $this->findNextElement();
        }

        // $cursor will be update as the parser continues
        if ( $this->startBracket == '(' )
        {
            // This is a parenthesis so we use a different node type
            $this->block = new ezcTemplateParenthesisTstNode( $this->parser->source, $this->startCursor, $cursor );
            $this->block->startBracket = $this->startBracket;
            $this->block->endBracket = $this->endBracket;
        }
        else
        {
            $this->block = new ezcTemplateOutputBlockTstNode( $this->parser->source, $this->startCursor, $cursor );
            $this->block->isRaw = $rawBlock;
            $this->block->startBracket = $this->startBracket;
            $this->block->endBracket = $this->endBracket;
        }

        // skip whitespace and comments
        if ( !$this->findNextElement() )
        {
            return false;
        }

        $allowIdentifier = false;

        // Check for expression, the parser will call atEnd() of this class to
        // check for end of the expression.
        $expressionParser = new ezcTemplateExpressionSourceToTstParser( $this->parser, $this, null );
        $expressionParser->setAllCursors( $cursor );
        $expressionParser->startCursor = clone $cursor;
        $expressionParser->allowEmptyExpressions = true;
        if ( !$this->parseRequiredType( $expressionParser /*'Expression'*/, $this->startCursor, false ) )
        {
            return false;
        }

        $this->findNextElement();

        $rootOperator = $this->lastParser->currentOperator;
        if ( $rootOperator instanceof ezcTemplateOperatorTstNode )
        {
            $rootOperator = $rootOperator->getRoot();
        }

        // If there is no root operator the block is empty, change the block type.
        if ( $rootOperator === null )
        {
            $this->block = new ezcTemplateEmptyBlockTstNode( $this->parser->source, clone $this->startCursor, $cursor );
            $this->appendElement( $this->block );
            return true;
        }

        // Change the block type if the top-most operator is a modifiying operator.
        if ( $rootOperator instanceof ezcTemplateModifyingOperatorTstNode )
        {
            if ( $rawBlock)
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_ASSIGNMENT_NOT_ALLOWED );
            }

            // @todo if the parser block is a parenthesis it is not allowed to have modifying nodes
            $oldBlock = $this->block;
            $this->block = new ezcTemplateModifyingBlockTstNode( $this->parser->source, clone $this->startCursor, $cursor );
            $this->block->startBracket = $this->startBracket;
            $this->block->endBracket = $this->endBracket;
            $this->block->children = $oldBlock->children;
        }

        $this->block->expressionRoot = $rootOperator;
        $this->block->children = array( $rootOperator );
        $this->appendElement( $this->block );

        return true;
    }
}

?>
