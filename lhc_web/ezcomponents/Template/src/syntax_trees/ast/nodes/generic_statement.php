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
class ezcTemplateGenericStatementAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression making up the statement.
     * @var ezcTemplateAstNode
     */
    public $expression;

    /**
     * Flag for whether the statement should be terminated with a semicolon or not.
     * This is true by default and can be turned off e.g. when one the expression
     * is contains multiple sub-statements.
     * @var bool
     */
    public $terminateStatement;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateAstNode $expression
     * @param bool $terminateStatement
     */
    public function __construct( ezcTemplateAstNode $expression = null, $terminateStatement = true )
    {
        parent::__construct();

        $this->expression = $expression;
        $this->terminateStatement = $terminateStatement;
    }
}
?>
