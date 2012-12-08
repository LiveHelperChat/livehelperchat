<?php
/**
 * File containing the ezcTemplateLiteralArrayAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * This node represents an array.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateLiteralArrayAstNode extends ezcTemplateAstNode
{
    /**
     * An array containing all the values of the array. 
     * Those values can be expressions.
     *
     * @var array(ezcTemplateAstNode)
     */
    public $value = array();

    /**
     * An array containing all the keys of the array. 
     * Those key values can be expressions.
     *
     * @var array(ezcTemplateAstNode)
     */
    public $key = array();


    /**
     * Checks and set the type hints.
     *
     * @return void
     */
    public function checkAndSetTypeHint()
    {
        $this->typeHint = ezcTemplateAstNode::TYPE_ARRAY;
    }

    /**
     * Constructs a new ezcTemplate Literal array.
     */
    public function __construct( )
    {
        parent::__construct();
        $this->checkAndSetTypeHint();
    }
}
?>
