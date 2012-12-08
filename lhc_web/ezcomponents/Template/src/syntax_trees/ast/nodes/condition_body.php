<?php
/**
 * File containing the ezcTemplateConditionBodyAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a condition entry in an if construct.
 * The entry consists of a condition and a body.
 *
 * The condition entry is used to represent an if, else or elseif construct.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateConditionBodyAstNode extends ezcTemplateAstNode
{
    /**
     * The expression holding the condition element.
     * @var ezcTemplateAstNode
     */
    public $condition;

    /**
     * The body element.
     * @var ezcTemplateBodyAstNode
     */
    public $body;

    /**
     * Initialize with condition and body statement.
     *
     * @param ezcTemplateAstNode $condition
     * @param ezcTemplateBodyAstNode $body
     */
    public function __construct( ezcTemplateAstNode $condition = null, ezcTemplateBodyAstNode $body = null )
    {
        parent::__construct();
        $this->condition = $condition;
        $this->body = $body;
    }
}
?>
