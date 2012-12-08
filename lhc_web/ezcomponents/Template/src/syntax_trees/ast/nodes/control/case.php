<?php
/**
 * File containing the ezcTemplateCaseAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a case control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCaseAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression to use as case match.
     * @var ezcTemplateAstNode
     */
    public $match;

    /**
     * The body element for the case statement.
     * @var ezcTemplateBodyAstNode
     */
    public $body;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateAstNode $match 
     * @param ezcTemplateBodyAstNode $body
     */
    public function __construct( ezcTemplateAstNode $match = null, ezcTemplateBodyAstNode $body = null )
    {
        parent::__construct();
        $this->match = $match;
        $this->body = $body;
    }
}
?>
