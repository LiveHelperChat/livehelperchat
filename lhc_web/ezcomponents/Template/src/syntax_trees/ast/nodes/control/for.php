<?php
/**
 * File containing the ezcTemplateForAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a for control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateForAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression which, when evaluated, will return set the initial values
     * for iterator variables.
     * Set this to null to skip initial elements.
     * @var ezcTemplateAstNode
     */
    public $initial;

    /**
     * The expression which has the condition for each iteration.
     * @var ezcTemplateAstNode
     */
    public $condition;

    /**
     * The expression which, when evaluated, will increase the iterator
     * variables.
     * Set this to null to skip iteration.
     * @var ezcTemplateAstNode
     */
    public $iteration;

    /**
     * The body element for the for control structure.
     * @var ezcTemplateBodyAstNode
     */
    public $body;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateAstNode $initial
     * @param ezcTemplateAstNode $condition
     * @param ezcTemplateAstNode $iteration
     * @param ezcTemplateBodyAstNode $body
     */
    public function __construct( ezcTemplateAstNode $initial = null, ezcTemplateAstNode $condition = null, ezcTemplateAstNode $iteration = null,
                                 ezcTemplateBodyAstNode $body = null )
    {
        parent::__construct();
        $this->initial = $initial;
        $this->condition = $condition;
        $this->iteration = $iteration;
        $this->body = $body;
    }
}
?>
