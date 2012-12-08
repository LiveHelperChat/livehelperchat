<?php
/**
 * File containing the ezcTemplateDynamicBlockAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * This node represents a dynamic block inside a template.
 * The dynamic blocks are represented like:
 * <code>
 * {dynamic}
 * ...
 * {/dynamic}
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDynamicBlockAstNode extends ezcTemplateStatementAstNode
{
    /**
     * Boolean to keep track whether the single quotes should be escaped.
     *
     * @var bool
     */
    public $escapeSingleQuote = false;

    /**
     * The body node of this dynamic block.
     *
     * @var ezcTemplateBodyAstNode 
     */
    public $body;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateBodyAstNode $body
     */
    public function __construct( ezcTemplateBodyAstNode $body = null )
    {
        parent::__construct();

        $this->body = $body;
    }

}

?>
