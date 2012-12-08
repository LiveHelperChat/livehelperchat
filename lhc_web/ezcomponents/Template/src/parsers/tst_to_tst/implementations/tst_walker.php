<?php
/**
 * File containing the ezcTemplateTstWalker
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * The entire TST tree, doing nothing.
 * 
 * @package Template
 * @version 1.4.2
 * @access private
 */
abstract class ezcTemplateTstWalker implements ezcTemplateTstNodeVisitor
{
    /**
     * Keeps a trace of the nodes currently entered.
     *
     * @var array(ezcTemplateTstNode)
     */
    protected $nodePath = array();
    
    /**
     * Keeps count of the amount of subnodes.
     *
     * @var int
     */
    protected $statements = array();

    /**
     * Keeps track of the current offset. E.g. when an extra statement is 
     * inserted the offset should be increased. 
     *
     * var array(int)
     */
    protected $offset = array();

    /**
     * Constructs a ezcTemplateTstWalker
     */
    public function __construct()
    {
    }

    /**
     * visitProgramTstNode
     *
     * @param ezcTemplateProgramTstNode $node
     * @return void
     */
    public function visitProgramTstNode( ezcTemplateProgramTstNode $node )
    {
        array_unshift( $this->nodePath, $node );
        array_unshift( $this->statements, 0 );
        array_unshift( $this->offset, 0 );

        $b = clone $node;

        for ( $i = 0; $i < sizeof( $b->children ); $i++ )
        {
            $this->statements[0] = $i;
            $this->acceptAndUpdate( $b->children[$i] );
        }

        array_shift( $this->offset );
        array_shift( $this->statements );
        array_shift( $this->nodePath );
    }

    /**
     * visitForeachLoopTstNode
     *
     * @param ezcTemplateForeachLoopTstNode $node
     * @return void
     */
    public function visitForeachLoopTstNode( ezcTemplateForeachLoopTstNode $node )
    {
        foreach ( $node->children as $element )
        {
            $this->acceptAndUpdate( $element );
        }
    }

    /**
     * visitWhileLoopTstNode
     *
     * @param ezcTemplateWhileLoopTstNode $node
     * @return void
     */
    public function visitWhileLoopTstNode( ezcTemplateWhileLoopTstNode $node )
    {
        foreach ( $node->children as $element )
        {
            $this->acceptAndUpdate( $element );
        }
    }

    /**
     * visitDynamicBlockTstNode
     *
     * @param ezcTemplateDynamicBlockTstNode $node
     * @return void
     */
    public function visitDynamicBlockTstNode( ezcTemplateDynamicBlockTstNode $node )
    {
        foreach ( $node->children as $element )
        {
            $this->acceptAndUpdate( $element );
        }
    }

    /**
     * visitSwitchTstNode
     *
     * @param ezcTemplateSwitchTstNode $node
     * @return void
     */
    public function visitSwitchTstNode( ezcTemplateSwitchTstNode $node )
    {
        foreach ( $node->children as $element )
        {
            $this->acceptAndUpdate( $element );
        }
    }

    /**
     * visitCaseTstNode
     *
     * @param ezcTemplateCaseTstNode $node
     * @return void
     */
    public function visitCaseTstNode( ezcTemplateCaseTstNode $node )
    {
        foreach ( $node->children as $element )
        {
            $this->acceptAndUpdate( $element );
        }
    }

    /**
     * visitIfConditionTstNode
     *
     * @param ezcTemplateIfConditionTstNode $node
     * @return void
     */
    public function visitIfConditionTstNode( ezcTemplateIfConditionTstNode $node )
    {
        foreach ( $node->children as $element )
        {
            $this->acceptAndUpdate( $element );
        }
    }

    /**
     * visitConditionBodyTstNode
     *
     * @param ezcTemplateConditionBodyTstNode $node
     * @return void
     */
    public function visitConditionBodyTstNode( ezcTemplateConditionBodyTstNode $node )
    {
        foreach ( $node->children as $element )
        {
            $this->acceptAndUpdate( $element );
        }
    }

    /**
     * visitCaptureTstNode
     *
     * @param ezcTemplateCaptureTstNode $node
     * @return void
     */
    public function visitCaptureTstNode( ezcTemplateCaptureTstNode $node )
    {
        foreach ( $node->children as $element )
        {
            $this->acceptAndUpdate( $element );
        }
    }

    /**
     * visitCacheBlockTstNode
     *
     * @param ezcTemplateCacheBlockTstNode $node
     * @return void
     */
    public function visitCacheBlockTstNode( ezcTemplateCacheBlockTstNode $node )
    {
        foreach ( $node->children as $element )
        {
            $this->acceptAndUpdate( $element );
        }
    }

    /**
     * Calls the accept method on the given tst node. The return value
     * replaces the $node.
     *
     * @param ezcTemplateTstNode $node
     * @return void
     */
    protected function acceptAndUpdate( ezcTemplateTstNode &$node )
    {
        $ret = $node->accept( $this );
        if ( $ret !== null )
        {
            $node = $ret;
        }
    }
}
?>
