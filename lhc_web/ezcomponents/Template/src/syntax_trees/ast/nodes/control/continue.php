<?php
/**
 * File containing the ezcTemplateContinueAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a continue control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateContinueAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression which, when evaluated, returns the number of levels to continue.
     * This can be set to null if it should only continue the current level, ie.
     * <code>
     * continue;
     * </code>
     * @var ezcTemplateAstNode
     */
    public $expression;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param  ezcTemplateAstNode $expression
     */
    public function __construct( ezcTemplateAstNode $expression = null )
    {
        parent::__construct();
        $this->expression = $expression;
    }
}
?>
