<?php
/**
 * File containing the ezcTemplateCycleControlTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCycleControlTstNode extends ezcTemplateBlockTstNode
{
    /**
     * The name of the cycle.
     *
     * @var string
     */
    public $name;

    /**
     * The variables.
     *
     * @var array(ezcTemplateAstNode)
     */
    public $variables;

    /**
     * Constructs a new ezcTemplateCycleControlTstNode.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end, $name = null )
    {
        parent::__construct( $source, $start, $end );
        $this->name = $name;
        $this->variables = array();

        $this->isNestingBlock = false;
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'name'       => $this->name,
                      'variables'   => $this->variables );
    }
    
}
?>
