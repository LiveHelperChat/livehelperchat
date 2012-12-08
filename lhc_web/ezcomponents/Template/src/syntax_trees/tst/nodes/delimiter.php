<?php
/**
 * File containing the ezcTemplateDelimiterTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Creates a delimiter
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDelimiterTstNode extends ezcTemplateBlockTstNode
{
    /**
     * The modulo value.
     *
     * @var ezcTemplateExpressionTstNode
     */
    public $modulo;


    /**
     * The remainder.
     *
     * @var ezcTemplateExpressionTstNode
     */
    public $rest;

    /**
     * Constructs a new ezcTemplateDelimiterTstNode.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->modulo = null;
        $this->rest = null;
        $this->name = 'delimiter';
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'name'             => $this->name,
                      'isClosingBlock'   => $this->isClosingBlock,
                      'isNestingBlock'   => $this->isNestingBlock,
                      'modulo'           => $this->modulo,
                      'rest'             => $this->rest,
                      'children'         => $this->children );
    }

    /**
     * Handle the given element.
     *
     * @param ezcTemplateTstNode $element
     * @return void
     */
    public function handleElement( ezcTemplateTstNode $element )
    {
        parent::handleElement( $element );
    }

    /**
     * Checks if the given node can be attached to its parent.
     *
     * @throws ezcTemplateParserException if the node cannot be attached.
     * @param ezcTemplateTstNode $parentElement
     * @return void
     */
    public function canAttachToParent( $parentElement )
    {
        // Process the lot.
        // Must at least have one parent with foreach or while.

        $p = $parentElement;

        while ( !$p instanceof ezcTemplateProgramTstNode )
        {
            if ( $p instanceof ezcTemplateForeachLoopTstNode || $p instanceof ezcTemplateWhileLoopTstNode )
            {
                return; // Perfect, we are inside a loop.
            }

            $p = $p->parentBlock;
        }


        throw new ezcTemplateParserException( $this->source, $this->startCursor, $this->startCursor, 
            "{" . $this->name . "} can only be a child of an {foreach} or a {while} block." );
    }
}
?>
