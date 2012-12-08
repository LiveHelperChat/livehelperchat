<?php
/**
 * File containing the ezcTemplateIfConditionTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Control structure: if.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateIfConditionTstNode extends ezcTemplateBlockTstNode
{
    public $name;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
    }

    public function getTreeProperties()
    {
        return array( 'name'      => $this->name,
                      'children'  => $this->children );
    }

    public function canHandleElement( ezcTemplateTstNode $element )
    {
        if ( $element instanceof ezcTemplateIfConditionTstNode )
        {
            if ( $element->name == "if" ) 
            {
                return false;
            }

            return true;
        }

        return ( $element instanceof ezcTemplateLoopTstNode );
    }

    public function handleElement( ezcTemplateTstNode $element )
    {
        $last = sizeof( $this->children ) - 1;

        if ( !$element instanceof ezcTemplateConditionBodyTstNode )
        {
            $this->children[$last]->children[] = $element;
        }
        else
        {
            $this->children[] = $element;
        }
    }


    public function trimLine( ezcTemplateWhitespaceRemoval $removal )
    {
        if ( count( $this->children ) == 0 )
            return;

        foreach ( $this->children as $child )
        {
            if ( $child instanceof ezcTemplateConditionBodyTstNode )
            {
                if ( count( $child->children ) == 0 )
                {
                    continue;
                }

                // Tell the removal object to trim our first text child
                if ( $child->children[0] instanceof ezcTemplateTextTstNode )
                {
                    $removal->trimBlockLine( $this, $child->children[0] );
                }
                // Tell the removal object to trim text blocks after the current block
                // and after all sub-blocks.
                $removal->trimBlockLines( $this, $child->children );
            }

        }
    }


}
?>
