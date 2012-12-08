<?php
/**
 * File containing the ezcTemplateCaseConditionTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Control structure: case.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCaseTstNode extends ezcTemplateBlockTstNode
{
    public $conditions;

    /*
    // Array.
    public $body;
    */

    /**
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->conditions = array();
        $this->name = "case";

        $this->isNestingBlock = true;
    }

    public function getTreeProperties()
    {
        return array( 'conditions' => $this->conditions,
                      'children'  => $this->children );
    }

    public function handleElement( ezcTemplateTstNode $element )
    {
        parent::handleElement( $element );
    }
}
?>
