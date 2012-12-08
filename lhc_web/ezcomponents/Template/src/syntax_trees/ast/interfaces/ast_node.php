<?php
/**
 * File containing the ezcTemplateAstNode abstract class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Abstract class for representing PHP code elements as objects.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
abstract class ezcTemplateAstNode
{
    const TYPE_ARRAY = 1;
    const TYPE_VALUE = 2;

    /**
     * Keep track if the statement returns an Array, a value, or both. 
     * Both is returned when it's not certain what the statement will return.
     *
     * The typeHint information is used to do extra compile time checking.
     * For example, the following template should give a compile time exception:
     * <code>
     * {$a = 2}
     * {foreach $a => $b}
     * {$b}
     * {/foreach}
     * </code>
     *
     * @var int
     */
    public $typeHint = null;

    /**
     * Constructs a new AstNode.
     */
    public function __construct()
    {
    }

    /**
     * Checks if the visitor object is accepted and if so calls the appropriate
     * visitor method in it.
     *
     * The sub classes don't need to implement the usual accept() method.
     *
     * If the current object is: ezcTemplateVariableAstNode then
     * the method: $visitor->visitVariableTstNode( $this ) will be called.
     *
     * @param ezcTemplateAstNodeVisitor $visitor
     *        The visitor object which can visit the current code element.
     * @return ezcTemplateAstNode
     */
    public function accept( ezcTemplateAstNodeVisitor $visitor )
    {
        $class = get_class( $this );
        $visit = "visit" . substr( $class, 11 );

        return $visitor->$visit( $this );
    }
}
?>
