<?php
/**
 * File containing the ezcTemplateBreakAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a break control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateBreakAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression which, when evaluated, returns the number of levels to break.
     * This can be set to null if it should only break one level, ie.
     * <code>
     * break;
     * </code>
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
    }
}
?>
