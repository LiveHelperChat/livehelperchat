<?php
/**
 * File containing the ezcTemplateForeachAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a foreach control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateForeachAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression which, when evaluated, will return the array to iterate over.
     * @var ezcTemplateAstNode
     */
    public $arrayExpression;

    /**
     * The variable element which holds the name for the key variable to create.
     * This can be set to null to disable the creation of the key variable.
     * @var ezcTemplateVariableAstNode
     */
    public $keyVariable;

    /**
     * The variable element which holds the name of the value variable to create.
     * @var ezcTemplateVariableAstNode
     */
    public $valueVariable;

    /**
     * The body element for the foreach control structure.
     * @var ezcTemplateBodyAstNode
     */

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateAstNode $array
     * @param ezcTemplateVariableAstNode $key
     * @param ezcTemplateVariableAstNode $value
     * @param ezcTemplateBodyAstNode $body
     */
    public function __construct( ezcTemplateAstNode $array = null,
                                 ezcTemplateVariableAstNode $key = null, ezcTemplateVariableAstNode $value = null,
                                 ezcTemplateBodyAstNode $body = null )
    {
        parent::__construct();
        $this->arrayExpression = $array;
        $this->keyVariable = $key;
        $this->valueVariable = $value;
        $this->body = $body;
    }
}
?>
