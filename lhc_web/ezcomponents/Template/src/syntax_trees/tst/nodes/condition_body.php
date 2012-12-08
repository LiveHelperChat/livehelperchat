<?php
/**
 * File containing the ezcTemplateConditionBodyTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The condition body.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateConditionBodyTstNode extends ezcTemplateBlockTstNode
{
    /**
     * The conditions.
     * @var ezcTemplateAstNode
     */
    public $condition;

    /**
     * Constructs a new ezcTemplateConditionBodyTstNode.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->condition = null;

        $this->isNestingBlock = false;
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'condition' => $this->condition,
                      'children'  => $this->children );
    }

    /**
     * Checks if the given element can be attached to its parent.
     *
     * @throws ezcTemplateParserException if the element cannot be attached.
     * @return void
     */
    public function canAttachToParent( $parentElement )
    {
        if ( !$parentElement instanceof ezcTemplateIfConditionTstNode )
        {
            if ( $parentElement instanceof ezcTemplateProgramTstNode )
            {
               throw new ezcTemplateParserException( $this->source, $this->startCursor, $this->startCursor, 
                   "{" . $this->name . "} can only be a child of an {if} block." );
            } 

            throw new ezcTemplateParserException( $this->source, $this->startCursor, $this->startCursor, 
               "The block {" . $this->name . "} cannot be a sub-block of {".$parentElement->name."}. {".$this->name."} can only be a child of an {if} block." );
        }
    }


}
?>
