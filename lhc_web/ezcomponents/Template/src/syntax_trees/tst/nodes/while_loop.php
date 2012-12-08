<?php
/**
 * File containing the ezcTemplateWhileLoopTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Control structures: while
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateWhileLoopTstNode extends ezcTemplateBlockTstNode
{
    public $condition;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end, $name = null )
    {
        parent::__construct( $source, $start, $end );
        $this->condition = null;
        $this->name = $name;
    }

    public function getTreeProperties()
    {
        return array( 'name'      => $this->name,
                      'condition' => $this->condition,
                      'children'  => $this->children );
    }

    public function canHandleElement( ezcTemplateTstNode $element )
    {
        return ( $element instanceof ezcTemplateLoopTstNode && $element->name != 'delimiter' );
    }

    public function handleElement( ezcTemplateTstNode $element )
    {
        if ( $this->canHandleElement( $element ) )
        {
            $this->children[] = $element;
            $element->parentBlock = $this;
        }
        else
        {
            parent::handleElement( $element );
        }
    }

}
?>
