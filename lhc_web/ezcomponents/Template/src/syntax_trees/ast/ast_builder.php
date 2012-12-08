<?php
/**
 * File containing the ezcTemplateAstBuilder class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Easy building of PHP code elements.
 *
 * This class has several methods which will make it much easier to build
 * PHP code elements without having to instantiate the specific code classes
 * manually. The code will be much sparser and should be easier to read.
 *
 * The simplest usage is to instantiate this class and starting calling the
 * various methods which appends a new statement to the current body.
 * e.g.
 * <code>
 * $cb = new ezcTemplateAstBuilder;
 * $cb->assign( "a", 5 ); // $a = 5
 * $cb->call( "echo", $cb->variable( "a" ) ); // echo $a;
 * </code>
 *
 * It can also create more complex code which involes control structures.
 * The structures usually has a condition or expression as a parameter
 * and then a body element, this is where the body() method comes into
 * play. This creates a new build object which is returned, the control
 * structure methods will recognize it and use the main body of it as
 * CS body. However once the builder is returned from body() it can be
 * used to fill in the body before it is passed to the CS.
 * .e.g to create an 'if' with a body:
 * <code>
 * $cb->_if( true,
 *           $cb->body()
 *              ->assign( "b", 5 ) );
 * </code>
 * As you see almost all methods in this class returns $this, which means
 * consecutive calls on the same object is possible without having to resort
 * to temporary variables.
 *
 * Another important part is that most methods accepts PHP basic types as
 * parameters, the method will convert it internally to the correct object
 * and use that instead. This is useful when you only need to work with
 * constant values and not complex structures. See each respective methods
 * for what is allowed as parameter values.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateAstBuilder
{
    /**
     * The body element which starts the statements.
     * @var ezcTemplateBodyAstNode
     */
    private $mainBody;

    /**
     * The current body element.
     * @var ezcTemplateBodyAstNode
     * @todo This might not be needed anymore due to the body() functionality.
     */
    private $currentBody;

    /**
     * The current element.
     * @var ezcTemplateAstNode
     * @todo This might not be needed anymore due to the body() functionality.
     */
    private $currentElement;

    /**
     * Initialize with a new body elemnt.
     */
    public function __construct()
    {
        $this->mainBody = new ezcTemplateBodyAstNode();
        $this->currentBody = $this->mainBody;
        $this->currentElement = $this->mainBody;
    }

    /**
     * Returns the body element which represents the start of the code.
     *
     * @return ezcTemplateBodyAstNode
     */
    public function getAstNode()
    {
        return $this->mainBody;
    }

    /**
     * Appends a function call to the current body and returns $this.
     * The $parameters can consist of PHP types in which case they are converted
     * into ezcTemplateLiteralAstNode objects, in addition a single parameter can be
     * passed in $parameters.
     *
     * @param string $name The name of the function to call.
     * @param mixed $parameters The parameters for the function call.
     * @return ezcTemplateAstBuilder
     */
    public function call( $name, $parameters )
    {
        if ( !is_array( $parameters ) )
        {
            $parameters = array( $parameters );
        }

        foreach ( $parameters as $i => $parameter )
        {
            if ( !is_object( $parameter ) ||
                 !$parameter instanceof ezcTemplateAstNode )
            {
                $parameter = new ezcTemplateLiteralAstNode( $parameter );
                $parameters[$i] = $parameter;
            }
        }
        switch ( $name )
        {
            case 'echo':
                $call = new ezcTemplateEchoAstNode( $parameters );
                break;
            default:
                $call = new ezcTemplateGenericStatementAstNode( new ezcTemplateFunctionCallAstNode( $name, $parameters ) );
        }
        $this->currentBody->appendStatement( $call );
        return $this;
    }

    /**
     * Appends an assignment operator to the current body and returns $this.
     * The left hand side can either be a code element or a string which
     * is turned into an ezcTemplateVariableAstNode object.
     * The right hand side can either be a code element or a PHP type which
     * is turned into an ezcTemplateLiteralAstNode object.
     *
     * @param mixed $lhs The left hand side of the expression.
     * @param mixed $rhs The right hand side of the expression.
     * @return ezcTemplateAstBuilder
     */
    public function assign( $lhs, $rhs )
    {
        if ( !is_object( $lhs ) ||
             !$lhs instanceof ezcTemplateAstNode )
        {
            $lhs = new ezcTemplateVariableAstNode( $lhs );
        }
        if ( !is_object( $rhs ) ||
             !$rhs instanceof ezcTemplateAstNode )
        {
            $rhs = new ezcTemplateLiteralAstNode( $rhs );
        }
        $assignment = new ezcTemplateAssignmentOperatorAstNode();
        $assignment->appendParameter( $lhs );
        $assignment->appendParameter( $rhs );
        $this->currentBody->appendStatement( new ezcTemplateGenericStatementAstNode( $assignment ) );
        return $this;
    }

    /**
     * Appends an if control structure to the current body and returns $this.
     * The $expression parameter can either be a code element or a PHP type
     * which is turned into an ezcTemplateLiteralAstNode object.
     * The $body parameter can either be an ezcTemplateBodyAstNode element or
     * an ezcTemplateAstBuilder object in which case the body is taken from
     * the builder.
     *
     * Usually you will call body() and use the result as the $body parameter,
     * this allows you to specify the statements for the body of the if.
     * <code>
     * $cb->_if( true,
     *           $cb->body()
     *              ->assign( "a", 5 ) );
     * </code>
     * This represents the code:
     * <code>
     * if ( true )
     * {
     *     $a = 5;
     * }
     * </code>
     *
     * @param mixed $expression The evaluated expression for the 'if'.
     * @param mixed $body       The body of the 'if' structure.
     * @return ezcTemplateAstBuilder
     */
    public function _if( $expression, $body )
    {
        if ( !is_object( $expression ) ||
             !$expression instanceof ezcTemplateAstNode )
        {
            $expression = new ezcTemplateLiteralAstNode( $expression );
        }

        if ( $body instanceof ezcTemplateAstBuilder )
        {
            $body = $body->getAstNode();
        }

        $conditionBody = new ezcTemplateConditionBodyAstNode( $expression, $body );
        $if = new ezcTemplateIfAstNode( $conditionBody );
        $this->currentBody->appendStatement( $if );
        return $this;
    }

    /**
     * Appends an elseif control structure to the current body and returns $this.
     *
     * Note: This can only called directly after a call to {@link self::_if() if ()}.
     *
     * The $expression parameter can either be a code element or a PHP type
     * which is turned into an ezcTemplateLiteralAstNode object.
     * The $body parameter can either be an ezcTemplateBodyAstNode element or
     * an ezcTemplateAstBuilder object in which case the body is taken from
     * the builder.
     *
     * Usually you will call body() and use the result as the $body parameter,
     * this allows you to specify the statements for the body of the else.
     * <code>
     * $cb->_elseif( true,
     *               $cb->body()
     *                  ->assign( "a", 5 ) );
     * </code>
     * This represents the code:
     * <code>
     * elseif ( true )
     * {
     *     $a = 5;
     * }
     * </code>
     *
     * @param mixed $expression The evaluated expression for the 'elseif'.
     * @param mixed $body       The body of the 'elseif' structure.
     * @return ezcTemplateAstBuilder
     */
    public function _elseif( $expression, $body )
    {
        $if = $this->currentBody->getLastStatement();
        if ( !$if instanceof ezcTemplateIfAstNode )
        {
            throw new ezcTemplateInternalException( "An 'elseif' control structure can only be appended right after an 'if', last statement was <" . get_class( $if ) . ">" );
        }
        $lastCondition = $if->getLastCondition();
        if ( $lastCondition !== null &&
             $lastCondition->condition === null )
        {
            throw new ezcTemplateInternalException( "An 'elseif' control structure can only be appended right after an 'if', tried placing it after an 'else' control structure." );
        }

        if ( !is_object( $expression ) ||
             !$expression instanceof ezcTemplateAstNode )
        {
            $expression = new ezcTemplateLiteralAstNode( $expression );
        }

        if ( $body instanceof ezcTemplateAstBuilder )
        {
            $body = $body->getAstNode();
        }

        $conditionBody = new ezcTemplateConditionBodyAstNode( $expression, $body );
        $if->appendCondition( $conditionBody );
        return $this;
    }

    /**
     * Appends an else control structure to the current body and returns $this.
     *
     * Note: This can only called directly after a call to {@link self::_if() if ()} or {@link self::_elseif() _elseif()}.
     *
     * The $body parameter can either be an ezcTemplateBodyAstNode element or
     * an ezcTemplateAstBuilder object in which case the body is taken from
     * the builder.
     *
     * Usually you will call body() and use the result as the $body parameter,
     * this allows you to specify the statements for the body of the else.
     * <code>
     * $cb->_else( $cb->body()
     *                ->assign( "a", 5 ) );
     * </code>
     * This represents the code:
     * <code>
     * else
     * {
     *     $a = 5;
     * }
     * </code>
     *
     * @param mixed $body The body of the 'else' structure.
     * @return ezcTemplateAstBuilder
     */
    public function _else( $body )
    {
        $if = $this->currentBody->getLastStatement();
        if ( !$if instanceof ezcTemplateIfAstNode )
        {
            throw new ezcTemplateInternalException( "An 'else' control structure can only be appended right after an 'if' or 'elseif', last statement was <" . get_class( $if ) . ">" );
        }
        $lastCondition = $if->getLastCondition();
        if ( $lastCondition !== null &&
             $lastCondition->condition === null )
        {
            throw new ezcTemplateInternalException( "An 'else' control structure can only be appended right after an 'if' or 'elseif', tried placing it after an 'else' control structure." );
        }

        if ( $body instanceof ezcTemplateAstBuilder )
        {
            $body = $body->getAstNode();
        }

        $conditionBody = new ezcTemplateConditionBodyAstNode( null, $body );
        $if->appendCondition( $conditionBody );
        return $this;
    }

    /**
     * Appends a while control structure to the current body and returns $this.
     * The $expression parameter can either be a code element or a PHP type
     * which is turned into an ezcTemplateLiteralAstNode object.
     * The $body parameter can either be an ezcTemplateBodyAstNode element or
     * an ezcTemplateAstBuilder object in which case the body is taken from
     * the builder.
     *
     * Usually you will call body() and use the result as the $body parameter,
     * this allows you to specify the statements for the body of the if.
     * <code>
     * $cb->_while( true,
     *              $cb->body()
     *                 ->assign( "a", 5 )
     *                 ->_break() );
     * </code>
     * This represents the code:
     * <code>
     * while ( true )
     * {
     *     $a = 5;
     *     break;
     * }
     * </code>
     *
     * @param mixed $expression The evaluated expression for the 'while'.
     * @param mixed $body       The body of the 'while' structure.
     * @return ezcTemplateAstBuilder
     */
    public function _while( $expression, $body )
    {
        if ( !is_object( $expression ) ||
             !$expression instanceof ezcTemplateAstNode )
        {
            $expression = new ezcTemplateLiteralAstNode( $expression );
        }

        if ( $body instanceof ezcTemplateAstBuilder )
        {
            $body = $body->getAstNode();
        }

        $conditionBody = new ezcTemplateConditionBodyAstNode( $expression, $body );
        $while = new ezcTemplateWhileAstNode( $conditionBody );
        $this->currentBody->appendStatement( $while );
        return $this;
    }

    /**
     * Appends a do/while control structure to the current body and returns $this.
     * The $expression parameter can either be a code element or a PHP type
     * which is turned into an ezcTemplateLiteralAstNode object.
     * The $body parameter can either be an ezcTemplateBodyAstNode element or
     * an ezcTemplateAstBuilder object in which case the body is taken from
     * the builder.
     *
     * Usually you will call body() and use the result as the $body parameter,
     * this allows you to specify the statements for the body of the if.
     * <code>
     * $cb->_doWhile( true,
     *                $cb->body()
     *                   ->assign( "a", 5 )
     *                   ->_break() );
     * </code>
     * This represents the code:
     * <code>
     * do
     * {
     *     $a = 5;
     *     break;
     * } while ( true );
     * </code>
     *
     * @param mixed $expression The evaluated expression for the 'do/while'.
     * @param mixed $body       The body of the 'do/while' structure.
     * @return ezcTemplateAstBuilder
     */
    public function _doWhile( $expression, $body )
    {
        if ( !is_object( $expression ) ||
             !$expression instanceof ezcTemplateAstNode )
        {
            $expression = new ezcTemplateLiteralAstNode( $expression );
        }

        if ( $body instanceof ezcTemplateAstBuilder )
        {
            $body = $body->getAstNode();
        }

        $conditionBody = new ezcTemplateConditionBodyAstNode( $expression, $body );
        $while = new ezcTemplateDoWhileAstNode( $conditionBody );
        $this->currentBody->appendStatement( $while );
        return $this;
    }

    /**
     * Creates a variable code element with the specified variable name and
     * returns it.
     *
     * @param string $name Name of variable.
     * @return ezcTemplateVariableAstNode
     */
    public function variable( $name )
    {
        return new ezcTemplateVariableAstNode( $name );
    }

    /**
     * Creates a new builder and body object and returns the builder.
     * The new builder can be used as parameter for all functions which
     * accepts a body as a parameter. This allows the builder object to
     * be used in consecutive calls without storing it in a temporary variable.
     * e.g.
     * <code>
     * $cb->body()
     *    ->assign( "a", 5 );
     * </code>
     *
     * @return ezcTemplateAstBuilder
     */
    public function body()
    {
        $builder = new ezcTemplateAstBuilder();
        return $builder;
    }
}
?>
