<?php
/**
 * File containing the ezcTemplateIssetAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents an isset construct.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateIssetAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression to evaluate if exists.
     * @var array(ezcTemplateAstNode)
     */
    public $expressions;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param array(ezcTemplateAstNode) $expressions
     */
    public function __construct( Array $expressions = null )
    {
        parent::__construct();
        $this->expressions = array();

        if ( $expressions !== null )
        {
            foreach ( $expressions as $expression )
            {
                if ( !$expression instanceof ezcTemplateAstNode )
                {
                    throw new ezcBaseValueException( "expressions[$id]", $expression, 'ezcTemplateAstNode' );
                }
                $this->expressions[] = $expression;
            }
        }
    }

    /**
     * Appends the expression to be checked for existance.
     *
     * @param ezcTemplateAstNode $expression Expression to check.
     */
    public function appendExpression( ezcTemplateAstNode $expression )
    {
        $this->expressions[] = $expression;
    }

    /**
     * Returns a list of expressions which will be checked for existance.
     * @return array(ezcTemplateAstNode)
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * Validates the expressions against their constraints.
     *
     * @throws ezcTemplateInternalException if the constraints are not met.
     */
    public function validate()
    {
        if ( count( $this->expressions ) == 0 )
        {
            throw new ezcTemplateInternalException( "Too few expressions for class <" . get_class( $this ) . ">, needs at least 1 but got 0." );
        }
    }
}
?>
