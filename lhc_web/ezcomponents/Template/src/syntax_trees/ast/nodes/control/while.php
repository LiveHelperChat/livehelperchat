<?php
/**
 * File containing the ezcTemplateWhileAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a while control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateWhileAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression which makes up the condition and body of the while
     * statement.
     * @var ezcTemplateConditionBodyAstNode
     */
    public $conditionBody;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateConditionBodyAstNode $conditionBody
     */
    public function __construct( ezcTemplateConditionBodyAstNode $conditionBody = null )
    {
        parent::__construct();
        $this->conditionBody = $conditionBody;
    }
}
?>
