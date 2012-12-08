<?php
/**
 * File containing the ezcTemplateGenericStatementAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a function call.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateParenthesisAstNode extends ezcTemplateAstNode
{
    /**
     * The expression making up the statement.
     * @var ezcTemplateAstNode
     */
    public $expression;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateAstNode $expression
     */
    public function __construct( ezcTemplateAstNode $expression = null )
    {
        parent::__construct();

        $this->expression = $expression;
        $this->typeHint = $expression->typeHint;
    }
}
?>
