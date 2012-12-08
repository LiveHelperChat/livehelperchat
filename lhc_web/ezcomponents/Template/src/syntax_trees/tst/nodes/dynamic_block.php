<?php
/**
 * File containing the ezcTemplateDynamicBlockTstNode  class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * The dynamic block node contains the possible the dynamic block.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDynamicBlockTstNode extends ezcTemplateBlockTstNode
{
    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->name = 'dynamic';
    }

    public function getTreeProperties()
    {
        return array( 'children' => $this->children );
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
        // Must at least have one parent with cache_block, or be after cache_template

        $p = $parentElement;

        while ( !$p instanceof ezcTemplateProgramTstNode )
        {
            if ( $p instanceof ezcTemplateCacheBlockTstNode )
            {
                return; // Perfect, we are inside a cache_block
            }

            $p = $p->parentBlock;
        }

        if ( $p instanceof ezcTemplateProgramTstNode )
        {
            foreach ( $p->children as $node )
            {
                if ( $node instanceof ezcTemplateCacheTstNode )
                {
                    return; // Perfect, we are after cache_template
                }
            }
        }

        throw new ezcTemplateParserException( $this->source, $this->startCursor, $this->startCursor, 
            "{" . $this->name . "} can only be a child of {cache_template} or a {cache_block} block." );
    }
}
?>
