<?php
/**
 * File containing the ezcTemplateAstNodeGenerator class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Generator of PHP code.
 *
 * Implements the ezcTemplateBasicAstNodeVisitor interface for visiting code elements
 * and generating code for them.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateAstToPhpGenerator implements ezcTemplateAstNodeVisitor
{
    /**
     * The default value to increase the indentation with.
     * Set it through the constructor.
     * @var int
     */
    private $indentation;

    /**
     * The current indentation value.
     * This starts at 0 and usually increases with value defined in self::$indentation.
     * @var int
     */
    private $currentIndentation;

    /**
     * Text which represents the current indentation.
     * This is automatically generated each time the indentation is changed.
     * @var string
     */
    private $indentationText;

    /**
     * Stack of old indentation values.
     * When the indentation changes it is pushed to the stack and later on restored.
     * @var array(int)
     */
    private $indentationStack;

    /**
     * File descriptor for the opened file which will contain the generated code.
     * @var resource
     */
    private $fd;

    /**
     * Flag for whether the footer has been written or.
     * This makes sure the destructor does not write it if it has been called earlier.
     * @var bool
     */
    private $hasWrittenFooter;

    /**
     * Flag for whether the output is currently at the beginning of a new line or not.
     * This is used to determine if the indentation text self::$indentationText
     * should be prepended to the current line or not.
     * @var bool
     */
    private $newline;


    /**
     * Escape the single quote in the output. Useful for the caching. 
     * @var bool
     */
    private $escapeSingleQuote = false;


    /**
     * The character set defined in the template. 
     * If set to false, it uses the $sourceCharset. 
     * @var string|bool
     */
    private $charsetInTemplate = false;

    /**
     * The source character set.
     * @var string
     */
    private $sourceCharset;

    /**
     * Transform to the target character set.
     * @var string
     */
    private $targetCharset;


    /**
     * Constructs a new ezcTemplateAstToPhpGenerator
     *
     * @param string $path File path for the file which should be generated.
     * @param ezcTemplateConfiguration $configuration The template configuration.
     * @param int $indentation The default indentation to use when increasing it.
     */
    public function __construct( $path, $configuration, $indentation = 4 )
    {
        if ( file_exists( $path ) )
        {
            if ( !is_writable( $path ) )
            {
                throw new ezcTemplateFileNotWriteableException( $path, "generated PHP file" );
            }
        }

        $this->fd = @fopen( $path, "w" );
        if ( !$this->fd )
        {
            throw new ezcTemplateFileNotWriteableException( $path, "generated PHP file" );
        }

        $this->indentation = $indentation;
        $this->hasWrittenFooter = false;
        $this->currentIndentation = 0;
        $this->indentationText = '';
        $this->indentationStack = array();
        $this->newline = true;

        $this->sourceCharset = $configuration->sourceCharset;
        $this->targetCharset = $configuration->targetCharset;
    }

    /**
     * Writes the files footer and closes the file if it is open.
     */
    public function __destruct()
    {
        if ( $this->fd )
        {
            fclose( $this->fd );
            $this->fd = null;
        }
    }

    /**
     * Writes the opening tag and some comments for the PHP file.
     * @return void
     */
    protected function writeHeader()
    {
        fwrite( $this->fd,
                "<?php\n" );
    }

    /**
     * Writes the ending tag for the PHP file.
     * @return void
     */
    protected function writeFooter()
    {
        // Only write a newline if the current output is not properly ended with a newline.
        if ( !$this->newline )
            fwrite( $this->fd, "\n" );

        fwrite( $this->fd, "?>\n" );
        $this->hasWrittenFooter = true;
    }

    /**
     * Writes a text string to the currenly opened file.
     * The text string will be split up into lines and will have each line
     * indented according to current indentation rules.
     *
     * @param string $text The text string to write.
     * @param string $pre  Text to place in front of each line (after indentation).
     * @param string $post Text to place after each line (before newline character).
     * @return void
     */
    protected function write( $text, $pre = null, $post = null )
    {
        $lines = preg_split( "#(\r\n|\r|\n)#", $text, -1, PREG_SPLIT_DELIM_CAPTURE );
        $count = count( $lines );
        for ( $i = 0; $i < $count; $i += 2 )
        {
            $line = $lines[$i];
            if ( $i + 1 < $count )
            {
                $str = $pre . $line . $post . $lines[$i + 1];
                if ( $this->newline )
                    $str = $this->indentationText . $str;
                fwrite( $this->fd, $str );
                $this->newline = true;
            }
            else // The last line.
            {
                if ( strlen( $line ) > 0 )
                {
                    $str = $pre . $line . $post;
                    if ( $this->newline )
                        $str = $this->indentationText . $str;
                    fwrite( $this->fd, $str );
                    $this->newline = false;
                }
            }
        }
    }

    /**
     * Increases the indentation with n characters.
     * The old indentation value is added to a stack which can be restored
     * with restoreIndentation().
     *
     * @param int $indentation The number of characters to increase the current indentation with.
     * @return void
     */
    public function increaseIndentation( $indentation )
    {
        $this->indentationStack[] = $this->currentIndentation;
        $this->currentIndentation += $indentation;
        if ( $this->currentIndentation > 0 )
        {
            $this->indentationText = str_repeat( " ", $this->currentIndentation );
        }
        else
        {
            $this->indentationText = "";
        }
    }

    /**
     * Restores the old indentation value from the stack.
     *
     * @throws Exception if the stack is empty.
     * @return void
     */
    public function restoreIndentation()
    {
        if ( count( $this->indentationStack ) == 0 )
        {
            throw new ezcTemplateInternalException( "Indentation stack is empty, cannot restore last indentation." );
        }

        $this->currentIndentation = array_pop( $this->indentationStack );
        if ( $this->currentIndentation > 0 )
        {
            $this->indentationText = str_repeat( " ", $this->currentIndentation );
        }
        else
        {
            $this->indentationText = "";
        }
    }

    /**
     * Generates code for control structures which takes a single parameter (optional).
     * The name of the control structure is sent in $name
     * @param ezcTemplateAstNode $control The control structure element to get parameter from.
     * @param string $name The name of the control structure, e.g. 'break'
     * @return void
     */
    protected function generateOptionalUnaryControl( ezcTemplateAstNode $control, $name )
    {
        $expression = $control->expression;
        if ( $expression === null )
        {
            $this->write( "{$name};\n" );
        }
        else
        {
            $this->write( "{$name} " );
            $expression->accept( $this );
            $this->write( ";\n" );
        }
    }

    /**
     * Generates code for control structures which takes a single parameter.
     * The name of the control structure is sent in $name
     * @param ezcTemplateAstNode $control The control structure element to get parameter from.
     * @param string $name The name of the control structure, e.g. 'break'
     * @return void
     */
    protected function generateUnaryControl( ezcTemplateAstNode $control, $name )
    {
        $this->write( "{$name} " );
        $control->expression->accept( $this );
        $this->write( ";\n" );
    }

    /**
     * Generates code for construct elements which looks like function calls.
     *
     * @param string $name The name of the construct to place in code.
     * @param ezcTemplateAstNode $construct The construct element.
     * @param array(ezcTemplateAstNode) $parameters
     *        The parameters for the construct, will be written out with a comma
     *        in between.
     * @return void
     */
    private function generateFunctionConstruct( $name, ezcTemplateAstNode $construct, Array $parameters )
    {
        $this->write( $name . "(" );
        foreach ( $parameters as $i => $parameter )
        {
            if ( $i > 0 )
                $this->write( "," );
            $parameter->accept( $this );
        }
        $this->write( ");\n" );
    }

    /**
     * Exports the constant type value.
     *
     * @param ezcTemplateLiteralAstNode $type The code element containing the constant value.
     * @return void
     */
    public function visitLiteralAstNode( ezcTemplateLiteralAstNode $type )
    {
        // Output type using var_export
        if ( is_string( $type->value ) )
        {
            $search = array( "\\",
                             "\"",
                             "\$",
                             "\t",
                             "\r",
                             "\n" );

            $replace = array( "\\\\",
                              "\\\"",
                              "\\$",
                              "\\t",
                              "\\r",
                              "\\n" );

            if ( $this->escapeSingleQuote ) 
            {
                $search[] = "'";
                $replace[] = "\\'";
            }

            $valueText = str_replace( $search, $replace, $type->value );

            $charset = $this->charsetInTemplate === false ? $this->sourceCharset : $this->charsetInTemplate;
            #echo "FROM",  $charset;
            #echo "TO",  $this->targetCharset;
            $valueText = iconv( $charset, $this->targetCharset, $valueText );

            $text = "\"$valueText\"";
            $this->write( $text );
        }
        elseif ( is_array( $type->value ) )
        {
            $this->write( "array(" );
            foreach ( $type->value as $val )
            {
                $val->accept( $this );

                $this->write( "," );
            }
            $this->write( ")" );
            // $this->write( var_export( $type->value, true ) );
        }
        else
        {
            $this->write( var_export( $type->value, true ) );
        }
    }

    /**
     * Writes an php array.
     *
     * @param ezcTemplateLiteralArrayAstNode $type
     * @return void
     */
    public function visitLiteralArrayAstNode( ezcTemplateLiteralArrayAstNode $type )
    {
        $first = true;

        $this->write( "array(" );
        foreach ( $type->value as $key => $val )
        {
            if ( !$first )
            {
                $this->write( "," );
            }

            if ( isset( $type->keys[$key] ) )
            {
                $type->keys[$key]->accept( $this );
                $this->write ( " => " );
            }

            $val->accept( $this );
            $first = false;

        }
        $this->write( ")" );
    }


    /**
     * Writes an output node.
     *
     * @param ezcTemplateOutputAstNode $type
     * @return void
     */
    public function visitOutputAstNode( ezcTemplateOutputAstNode $type )
    {
        $type->expression->accept( $this );
    }


    /**
     * Casts the value to the type.
     *
     * @param ezcTemplateTypeCastAstNode $node
     * @return void
     */
    public function visitTypeCastAstNode( ezcTemplateTypeCastAstNode $node )
    {
       $this->write( "(" . $node->type . ")" );

       $node->value->accept( $this );
    }

    /**
     * Writes the constant as-is.
     *
     * @param ezcTemplateConstantAstNode $type The code element containing the constant value.
     * @return void
     */
    public function visitConstantAstNode( ezcTemplateConstantAstNode $type )
    {
        $this->write( $type->value );
    }

    /**
     * Writes the EOL comment with correct start marker.
     *
     * @param ezcTemplateEolCommentAstNode $comment The code element containing the EOL comment.
     * @return void
     */
    public function visitEolCommentAstNode( ezcTemplateEolCommentAstNode $comment )
    {
        $marker = $comment->createMarkerText();
        if ( $comment->hasSeparator )
        {
            $marker .= ' ';
        }
        $this->write( $comment->text . "\n", $marker );
    }

    /**
     * Writes the block comment.
     *
     * @param ezcTemplateBlockCommentAstNode $comment The code element containing the block comment.
     * @return void
     */
    public function visitBlockCommentAstNode( ezcTemplateBlockCommentAstNode $comment )
    {
        // Comment output.
        
        // if ( $comment->hasSeparator )
        // {
        //     $startMarker = '/* ';
        //     $endMarker   = ' */';
        // }
        // else
        // {
        //     $startMarker = '/*';
        //     $endMarker   = '*/';
        // }
        // $this->write( $startMarker . $comment->text . $endMarker . "\n" );
    }

    /**
     * Writes a dollar sign and the name of the variable.
     *
     * @param ezcTemplateVariableAstNode $var The code element containing the variable value.
     * @return void
     */
    public function visitVariableAstNode( ezcTemplateVariableAstNode $var )
    {
        $this->write( '$' . $var->name );
    }

    /**
     * Writes a dollar sign and curly braces around the sub-expression.
     *
     * @param ezcTemplateDynamicVariableAstNode $var The code element containing the variable value.
     * @return void
     */
    public function visitDynamicVariableAstNode( ezcTemplateDynamicVariableAstNode $var )
    {
        $this->write( '${' );
        $var->nameExpression->accept( $this );
        $this->write( '}' );
    }

    /**
     * Visits a code element containing a dynamic string.
     *
     * @param ezcTemplateDynamicStringAstNode $dynamic The code element containing the dynamic string.
     * @return void
     */
    public function visitDynamicStringAstNode( ezcTemplateDynamicStringAstNode $dynamic )
    {
        $parameters = $dynamic->getParameters();

        $this->write( '"' );
        foreach ( $parameters as $parameter )
        {
            if ( $parameter instanceof ezcTemplateLiteralAstNode )
            {
                // Extract value as string and write it without the quotation marks.
                $value = (string)$parameter->value;
                $value = str_replace( array( "\\", "\"", "{" ),
                                      array( "\\\\", "\\\"", "\\{" ),
                                      $value );
                $this->write( $value );
            }
            else
            {
                $this->write( '{' );
                $parameter->accept( $this );
                $this->write( '}' );
            }
        }
        $this->write( '"' );
    }

    /**
     * Visits a code element containing an array fetch operator type.
     *
     * @param ezcTemplateArrayFetchOperatorAstNode $operator The code element containing the array fetch operator.
     * @return void
     */
    public function visitArrayFetchOperatorAstNode( ezcTemplateArrayFetchOperatorAstNode $operator )
    {
        $parameters = $operator->getParameters();
        $count = count( $parameters );
        if ( $count < $operator->minParameterCount )
        {
            throw new ezcTemplateInternalException( "The operator <" . get_class( $operator ) . " contains only " . count( $parameters ) . " parameters but should at least have {$operator->minParameterCount} parameters." );
        }

        // Generate code for first operand
        $parameters[0]->accept( $this );

        for ( $i = 1; $i < $count; ++$i )
        {
            // Generate the operator symbol before parameter.
            $this->write( "[" );

            // Generate code for operand
            $parameters[$i]->accept( $this );

            // and after parameter.
            $this->write( "]" );
        }
    }

    /**
     * Visits a code element containing a unary operator type.
     * Unary operators take one parameter and consist of a symbol.
     *
     * @param ezcTemplateOperatorAstNode $operator The code element containing the operator with parameter.
     * @return void
     */
    public function visitUnaryOperatorAstNode( ezcTemplateOperatorAstNode $operator )
    {
        $parameters = $operator->getParameters();
        if ( count( $parameters ) < $operator->minParameterCount )
        {
            throw new ezcTemplateInternalException( "The operator <" . get_class( $operator ) . " contains only " . count( $parameters ) . " parameters but should at least have {$operator->minParameterCount} parameters." );
        }

        if ( $operator->preOperator )
        {
            // Generate the operator symbol in between parameters.
            $this->write( " " . $operator->getOperatorPHPSymbol() );

            // Generate code for first operand
            $parameters[0]->accept( $this );
        }
        else
        {
            // Generate code for first operand
            $parameters[0]->accept( $this );

            // Generate the operator symbol in between parameters.
            $this->write( $operator->getOperatorPHPSymbol() . " " );
        }
    }

    /**
     * Visits a code element containing a binary operator type.
     * Binary operators take two parameters and consist of a symbol.
     *
     * @param ezcTemplateOperatorAstNode $operator The code element containing the operator with parameters.
     * @return void
     */
    public function visitBinaryOperatorAstNode( ezcTemplateOperatorAstNode $operator )
    {
        $parameters = $operator->getParameters();
        if ( count( $parameters ) < $operator->minParameterCount )
        {
            throw new ezcTemplateInternalException( "The operator <" . get_class( $operator ) . " contains only " . count( $parameters ) . " parameters but should at least have {$operator->minParameterCount} parameters." );
        }

        // Generate code for first operand
        $parameters[0]->accept( $this );

        // Generate the operator symbol in between parameters.

        if ( $operator instanceof ezcTemplateReferenceOperatorAstNode )
        {
            // No spaces around the '->'.  PHP cannot handle: $myObj -> class 
            $this->write( $operator->getOperatorPHPSymbol() );
        }
        else
        {
            $this->write( " " . $operator->getOperatorPHPSymbol() . " " );
        }

        // Generate code for second operand
        $parameters[1]->accept( $this );
    }

    /**
     * Visits a code element containing a function call.
     * Function call consist of a function name and arguments.
     *
     * @param ezcTemplateFunctionCallAstNode $fcall The code element containing the function call with arguments.
     * @return void
     */
    public function visitFunctionCallAstNode( ezcTemplateFunctionCallAstNode $fcall )
    {
        // Start arguments
        $this->write( $fcall->name . "(" );
        foreach ( $fcall->getParameters() as $i => $parameter )
        {
            if ( $i > 0 )
                $this->write( "," );
            $parameter->accept( $this );
        }
        $this->write( ")" );
    }

    /**
     * Visits a code element containing a body of statements.
     * A body consists of a series of statements in sequence.
     *
     * @param ezcTemplateBodyAstNode $body The code element containing the body.
     * @return void
     */
    public function visitBodyAstNode( ezcTemplateBodyAstNode $body )
    {
        foreach ( $body->statements as $statement )
        {
            $statement->accept( $this );
        }
    }

    /**
     * Visits a code element containing a body of statements.
     * A body consists of a series of statements in sequence.
     *
     * @param ezcTemplateRootAstNode $body The code element containing the body.
     * @return void
     */
    public function visitRootAstNode( ezcTemplateRootAstNode $body )
    {
        $this->charsetInTemplate = $body->charset;

        if ( $body->startProgram )
        {
            $this->writeHeader();
        }
        foreach ( $body->statements as $statement )
        {
            $statement->accept( $this );
        }
        if ( $body->startProgram )
        {
            $this->writeFooter();
        }
    }


    /**
     * Visits a code element containing a generic statement.
     * The expression is evaluated and and a semi-colon added to end the statement.
     * A generic statement contains a generic code expression but is terminated with a semi-colon.
     *
     * @param ezcTemplateGenericStatementAstNode $statement The code element containing the statement.
     * @return void
     */
    public function visitGenericStatementAstNode( ezcTemplateGenericStatementAstNode $statement )
    {
        $statement->expression->accept( $this );
        if ( $statement->terminateStatement )
        {
            $this->write( ";\n" );
        }
    }

    /**
     * Visits a code element containing if control structures.
     *
     * @param ezcTemplateIfAstNode $if The code element containing the if control structure.
     * @return void
     */
    public function visitIfAstNode( ezcTemplateIfAstNode $if )
    {
        foreach ( $if->conditions as $i => $conditionBody )
        {
            $condition = $conditionBody->condition;
            if ( $i == 0 )
                $this->write( "if (" );
            elseif ( $condition !== null )
                $this->write( "elseif (" );
            else
                $this->write( "else" );
            if ( $condition !== null )
            {
                $condition->accept( $this );
                $this->write( ")\n{\n" );
            }
            else
            {
                $this->write( "\n{\n" );
            }
            $this->increaseIndentation( $this->indentation );
            $conditionBody->body->accept( $this );
            $this->restoreIndentation();
            $this->write( "}\n" );
        }
    }

    /**
     * Pretends to write the dynamic block. The only thing it does is calling 
     * the children with the escapeSingleQuote switched on.
     *
     * @param ezcTemplateDynamicBlockAstNode $statement
     * @return void
     */
    public function visitDynamicBlockAstNode( ezcTemplateDynamicBlockAstNode $statement )
    {
        $this->escapeSingleQuote = $statement->escapeSingleQuote;

        // Do not write anything, but call the children.
        $statement->body->accept( $this );

        $this->escapeSingleQuote = false;
    }
 

    /**
     * Visits a code element containing while control structures.
     *
     * @param ezcTemplateWhileAstNode $while The code element containing the while control structure.
     * @return void
     */
    public function visitWhileAstNode( ezcTemplateWhileAstNode $while )
    {
        $conditionBody = $while->conditionBody;
        $this->write( "while (" );
        $conditionBody->condition->accept( $this );
        $this->write( ")\n{\n" );

        $this->increaseIndentation( $this->indentation );
        $conditionBody->body->accept( $this );
        $this->restoreIndentation();

        $this->write( "}\n" );
    }

    /**
     * Visits a code element containing foreach control structures.
     *
     * @param ezcTemplateForeachAstNode $foreach The code element containing the foreach control structure.
     * @return void
     */
    public function visitForeachAstNode( ezcTemplateForeachAstNode $foreach )
    {
        $this->write( "foreach (" );
        $foreach->arrayExpression->accept( $this );
        $this->write( " as " );
        if ( $foreach->keyVariable !== null )
        {
            $foreach->keyVariable->accept( $this );
            $this->write( " => " );
        }
        $foreach->valueVariable->accept( $this );
        $this->write( ")\n{\n" );

        $this->increaseIndentation( $this->indentation );
        $foreach->body->accept( $this );
        $this->restoreIndentation();

        $this->write( "}\n" );
    }

    /**
     * Visits a code element containing break control structures.
     *
     * @param ezcTemplateBreakAstNode $break The code element containing the break control structure.
     * @return void
     */
    public function visitBreakAstNode( ezcTemplateBreakAstNode $break )
    {
        $this->generateOptionalUnaryControl( $break, "break" );
    }

    /**
     * Visits a code element containing continue control structures.
     *
     * @param ezcTemplateContinueAstNode $continue The code element containing the continue control structure.
     * @return void
     */
    public function visitContinueAstNode( ezcTemplateContinueAstNode $continue )
    {
        $this->generateOptionalUnaryControl( $continue, "continue" );
    }

    /**
     * Visits a code element containing return control structures.
     *
     * @param ezcTemplateReturnAstNode $return The code element containing the return control structure.
     * @return void
     */
    public function visitReturnAstNode( ezcTemplateReturnAstNode $return )
    {
        $this->generateOptionalUnaryControl( $return, "return" );
    }

    /**
     * Visits a code element containing require control structures.
     *
     * @param ezcTemplateRequireAstNode $require The code element containing the require control structure.
     * @return void
     */
    public function visitRequireAstNode( ezcTemplateRequireAstNode $require )
    {
        $this->generateUnaryControl( $require, "require" );
    }

    /**
     * Visits a code element containing require_once control structures.
     *
     * @param ezcTemplateRequireOnceAstNode $require The code element containing the require_once control structure.
     * @return void
     */
    public function visitRequireOnceAstNode( ezcTemplateRequireOnceAstNode $require )
    {
        $this->generateUnaryControl( $require, "require_once" );
    }

    /**
     * Visits a code element containing include control structures.
     *
     * @param ezcTemplateIncludeAstNode $include The code element containing the include control structure.
     * @return void
     */
    public function visitIncludeAstNode( ezcTemplateIncludeAstNode $include )
    {
        $this->generateUnaryControl( $include, "include" );
    }

    /**
     * Visits a code element containing include_once control structures.
     *
     * @param ezcTemplateIncludeOnceAstNode $include The code element containing the include_once control structure.
     * @return void
     */
    public function visitIncludeOnceAstNode( ezcTemplateIncludeOnceAstNode $include )
    {
        $this->generateUnaryControl( $include, "include_once" );
    }

    /**
     * Visits a code element containing switch control structures.
     *
     * @param ezcTemplateSwitchAstNode $switch The code element containing the switch control structure.
     * @return void
     */
    public function visitSwitchAstNode( ezcTemplateSwitchAstNode $switch )
    {
        $this->write( "switch (" );
        $switch->expression->accept( $this );
        $this->write( ")\n{\n" );

        $this->increaseIndentation( $this->indentation );
        foreach ( $switch->cases as $case )
        {
            $case->accept( $this );
        }
        $this->restoreIndentation();

        $this->write( "}\n" );
    }

    /**
     * Visits a code element containing case control structures.
     *
     * @param ezcTemplateCaseAstNode $case The code element containing the case control structure.
     * @return void
     */
    public function visitCaseAstNode( ezcTemplateCaseAstNode $case )
    {
        $this->write( "case " );
        $case->match->accept( $this );
        $this->write( ":\n" );

        $this->increaseIndentation( $this->indentation );
        $case->body->accept( $this );
        $this->restoreIndentation();
    }

    /**
     * Visits a code element containing default case control structures.
     *
     * @param ezcTemplateDefaultAstNode $default The code element containing the default case control structure.
     * @return void
     */
    public function visitDefaultAstNode( ezcTemplateDefaultAstNode $default )
    {
        $this->write( "default:\n" );
        $this->increaseIndentation( $this->indentation );
        $default->body->accept( $this );
        $this->restoreIndentation();
    }

    /**
     * Visits a code element containing conditions for if control structures.
     *
     * @param ezcTemplateConditionBodyAstNode $cond The code element containing the if condition.
     * @return void
     */
    public function visitConditionBodyAstNode( ezcTemplateConditionBodyAstNode $cond )
    {
        // Not used, data is extracted directly for if, while and do/while
    }

    /**
     * Visits a code element containing try control structures.
     *
     * @param ezcTemplateTryAstNode $try The code element containing the try control structure.
     * @return void
     */
    public function visitTryAstNode( ezcTemplateTryAstNode $try )
    {
        $this->write( "try\n{\n" );
        $this->increaseIndentation( $this->indentation );
        $try->body->accept( $this );
        $this->restoreIndentation();
        $this->write( "}\n" );

        foreach ( $try->catches as $catch )
        {
            $catch->accept( $this );
        }
    }

    /**
     * Visits a code element containing catch control structures.
     *
     * @param ezcTemplateCatchAstNode $catch The code element containing the catch control structure.
     * @return void
     */
    public function visitCatchAstNode( ezcTemplateCatchAstNode $catch )
    {
        $this->write( "catch (" . $catch->className . " " );
        $catch->variableExpression->accept( $this );
        $this->write( ")\n{\n" );
        $this->increaseIndentation( $this->indentation );
        $catch->body->accept( $this );
        $this->restoreIndentation();
        $this->write( "}\n" );
    }

    /**
     * Visits a code element containing echo construct.
     *
     * @param ezcTemplateEchoAstNode $echo The code element containing the echo construct.
     * @return void
     */
    public function visitEchoAstNode( ezcTemplateEchoAstNode $echo )
    {
        $outputList = $echo->getOutputList();
        $this->write( "echo " );

        foreach ( $outputList as $i => $output )
        {
            if ( $i > 0 )
                $this->write( "," );
            $output->accept( $this );
        }
        $this->write( ";\n" );
    }

    /**
     * Visits a code element containing print construct.
     *
     * @param ezcTemplatePrintAstNode $print The code element containing the print construct.
     * @return void
     */
    public function visitPrintAstNode( ezcTemplatePrintAstNode $print )
    {
        $this->write( "print " );
        $print->expression->accept( $this );
        $this->write( ";\n" );
    }

    /**
     * Visits a code element containing isset construct.
     *
     * @param ezcTemplateIssetAstNode $isset The code element containing the isset construct.
     * @return void
     */
    public function visitIssetAstNode( ezcTemplateIssetAstNode $isset )
    {
        $this->generateFunctionConstruct( 'isset', $isset, $isset->getExpressions() );
    }

    /**
     * Visits a code element containing unset construct.
     *
     * @param ezcTemplateUnsetAstNode $unset The code element containing the unset construct.
     * @return void
     */
    public function visitUnsetAstNode( ezcTemplateUnsetAstNode $unset )
    {
        $this->generateFunctionConstruct( 'unset', $unset, $unset->getExpressions() );
    }

    /**
     * Visits a code element containing empty construct.
     *
     * @param ezcTemplateEmptyAstNode $empty The code element containing the empty construct.
     * @return void
     */
    public function visitEmptyAstNode( ezcTemplateEmptyAstNode $empty )
    {
        $this->write( "empty(" );
        $empty->expression->accept( $this );
        $this->write( ");\n" );
    }

    /**
     * Writes parenthesis.
     *
     * @param ezcTemplateParenthesisAstNode $parenthesis
     * @return void
     */
    public function visitParenthesisAstNode( ezcTemplateParenthesisAstNode $parenthesis )
    {
        $this->write( "(" );
        $parenthesis->expression->accept( $this );
        $this->write( ")" );
    }

    /**
     * Writes curly braces. 
     *
     * @param ezcTemplateCurlyBracesAstNode $curly
     * @return void
     */
    public function visitCurlyBracesAstNode( ezcTemplateCurlyBracesAstNode $curly )
    {
        $this->write( "{" );
        $curly->expression->accept( $this );
        $this->write( "}" );
    }

    /**
     * Writes a new class.
     *
     * @param ezcTemplateNewAstNode $new
     * @return void
     */
    public function visitNewAstNode( ezcTemplateNewAstNode $new )
    {
        $this->write( "new " );
        $this->write( $new->class . "(" );

        foreach ( $new->getParameters() as $i => $parameter )
        {
            if ( $i > 0 )
                $this->write( "," );
            $parameter->accept( $this );
        }
        $this->write( ")" );
    }

    /**
     * Writes the clone statement.
     *
     * @param ezcTemplateCloneAstNode $clone
     * @return void
     */
    public function visitCloneAstNode( ezcTemplateCloneAstNode $clone )
    {
        $this->write( "clone " );
        $this->write( $clone->object->accept($this) );
    }


    /**
     * Writes raw PHP code.
     *
     * @param ezcTemplatePhpCodeAstNode $code
     * @return void
     */
    public function visitPhpCodeAstNode( ezcTemplatePhpCodeAstNode $code )
    {
        $this->write( $code->code );
    }

    /**
     * Writes a new runtime exception.
     *
     * @param ezcTemplateThrowExceptionAstNode $exception
     * @return void
     */
    public function visitThrowExceptionAstNode( ezcTemplateThrowExceptionAstNode $exception )
    {
        $this->write( "throw new ezcTemplateRuntimeException( ");
        $exception->message->accept( $this );
        $this->write( ")" );
    }

    /**
     * Writes an identifier.
     *
     * @param ezcTemplateIdentifierAstNode $node
     * @return void
     */
    public function visitIdentifierAstNode( ezcTemplateIdentifierAstNode $node )
    {
        $this->write( $node->name );
    }


    /**
     * Visits a node containing a nop node.
     *
     * @param ezcTemplateNopAstNode $node The node containing the nop node.
     * @return void
     */
    public function visitNopAstNode( ezcTemplateNopAstNode $node )
    {
        // Nops don't create any output
    }
}
?>
