<?php
/**
 * File containing the ezcTemplateDynamicVariableAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents PHP variables.
 *
 * Dynamic variables are code elements with one expression which defines the
 * name of the variable to find. The expression will be evaluated and the
 * return value of it will be used as variable name.
 *
 * Dynamic lookup of variable using other variable $some_var.
 * <code>
 * $var1 = new ezcTemplateVariableAstNode( 'some_var' );
 * $var2 = new ezcTemplateDynamicVariableAstNode( $var1 );
 * </code>
 * The corresponding PHP code will be:
 * <code>
 * ${$some_var}
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDynamicVariableAstNode extends ezcTemplateAstNode
{
    /**
     * The expression which will, when evaluated, return the name of the
     * variable to use.
     * @var ezcTemplateAstNode
     */
    public $nameExpression;

    /**
     * Constructs a new ezcTemplateDynamicVariableAstNode
     *
     * @param ezcTemplateAstNode $nameExpression The code element which will evaluate to the name of the variable.
     */
    public function __construct( ezcTemplateAstNode $nameExpression = null )
    {
        parent::__construct();
        $this->nameExpression = $nameExpression;
    }
}
?>
