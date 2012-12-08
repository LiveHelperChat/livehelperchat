<?php
/**
 * File containing the ezcTemplateOperatorAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents PHP builtin types.
 *
 * Creating the type is done by simply passing the type to the constructor
 * which will take care of storing it and exporting it to PHP code later on.
 * The following types can be added:
 * - integer
 * - float
 * - boolean
 * - null
 * - string
 * - array
 * - objects
 *
 * The following types are not supported:
 * - resource
 *
 * Note: Objects will have to implement the __set_state magic method to be
 *       properly exported.
 *
 * <code>
 * $tInt = new ezcTemplateOperatorAstNode( 5 );
 * $tFloat = new ezcTemplateOperatorAstNode( 5.2 );
 * $tString = new ezcTemplateOperatorAstNode( "a simple string" );
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
abstract class ezcTemplateOperatorAstNode extends ezcTemplateParameterizedAstNode
{
    /**
     * Constant for the number of parameters of unary operators, ie. 1.
     */
    const OPERATOR_TYPE_UNARY = 1;

    /**
     * Constant for the number of parameters of binary operators, ie. 2.
     */
    const OPERATOR_TYPE_BINARY = 2;

    /**
     * Constant for the number of parameters of ternary operators, ie. 3.
     */
    const OPERATOR_TYPE_TERNARY = 3;

    /**
     * Controls how unary operators are handled.
     * If true the operator is placed infront of the operand, if false it is
     * placed after the operand.
     */
    public $preOperator;

    /**
     * Constructs a new ezcTemplateOperatorAstNode
     *  
     * @param int $parameterCount The number of parameters the operator must have.
     * @param bool $preOperator Controls whether unary operators are placed before or after operand.
     */
    public function __construct( $parameterCount, $preOperator = false )
    {
        parent::__construct( $parameterCount, $parameterCount );

        $this->preOperator = $preOperator;
    }

    /**
     * Returns the parameters of the operator.
     *
     * Note: The values returned from this method must never be modified.
     * @return array(ezcTemplateAstNode)
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Checks if the visitor object is accepted and if so calls the appropriate
     * visitor method in it.
     *
     * Calls visitUnaryOperator if it has one parameter, visitBinaryOperator() if it has two and visitTernaryOperator() if it has three.
     * All part of the ezcTemplateBasicAstNodeVisitor interface.
     *
     * @param ezcTemplateAstNodeVisitor $visitor
     * @return ezcTemplateAstNode
     */
    public function accept( ezcTemplateAstNodeVisitor $visitor )
    {
        $count = count( $this->parameters );
        if ( $count == 1 )
        {
            $visitor->visitUnaryOperatorAstNode( $this );
        }
        else if ( $count == 2 )
        {
            $visitor->visitBinaryOperatorAstNode( $this );
        }
        else if ( $count == 3 )
        {
            $visitor->visitTernaryOperatorAstNode( $this );
        }
        else
        {
            throw new ezcTemplateInternalException( "Operator can only have 1, 2 or 3 operands but this has {$count}" );
        }
    }

    /**
     * Returns a text string representing the PHP operator.
     *
     * @return string
     */
    abstract public function getOperatorPHPSymbol();
}
?>
