<?php
/**
 * File containing the ezcTemplateCatchAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a catch control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCatchAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The name of the exception class to catch.
     * @var string
     */
    public $className;

    /**
     * The expression which holds the variable name to use.
     * @var ezcTemplateVariableAstNode
     */
    public $variableExpression;

    /**
     * The body element for the catch statement.
     * @var ezcTemplateBodyAstNode
     */
    public $body;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param string $className
     * @param ezcTemplateVariableAstNode $var
     * @param ezcTemplateBodyAstNode $body
     */
    public function __construct( $className, ezcTemplateVariableAstNode $var, ezcTemplateBodyAstNode $body = null )
    {
        parent::__construct();

        if ( !is_string( $className ) )
        {
            throw new ezcBaseValueException( "className", $className, 'string' );
        }
        $this->className = $className;
        $this->variableExpression = $var;
        $this->body = $body;
    }
}
?>
