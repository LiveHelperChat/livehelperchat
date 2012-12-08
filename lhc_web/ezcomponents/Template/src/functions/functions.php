<?php
/**
 * File containing the ezcTemplateFunctions class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This class translates Template functions to PHP functions. If the function
 * could not directly mapped to a PHP internal function, it calls a static 
 * function from the Template component, implementing the desired behavior. 
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateFunctions
{
    /**
     * Keeps the translations from a specific regular expression match to a class name.
     * E.g. Everything that starts with "str_" is translated to ezcTemplateStringFunctions.
     * 
     * The 'functions_to_class.php' is read for the translations.
     *
     * @var array(string=>string) $functionToClass
     */
    protected $functionToClass;

    /**
     * Keeps a reference to the Template parser.
     *
     * @var ezcTemplateParser $parser
     */
    private $parser = null;

    /**
     * Constructs a new ezcTemplateFunctions
     *
     * @param ezcTemplateParser $parser
     */
    public function __construct( ezcTemplateParser $parser )
    {
        $this->parser = $parser;
        $this->functionToClass = include( "function_to_class.php" );
    }

    /**
     * Returns (a part of) an AST-tree that makes a function call to the function $name with 
     * the parameters $parameters. 
     *
     * @param string $name
     * @param array(ezcTemplateAstNode) $parameters
     * @return array(ezcTemplateAstNode)
     */
    protected static function functionCall( $name, $parameters )
    {
        return array( "ezcTemplateFunctionCallAstNode", array( $name, array( "_array", $parameters ) ) );
    }

    /**
     * Returns an Literal AST-node with the value $val.
     *
     * @param mixed $val
     * @return array(ezcTemplateAstNode)
     */
    protected static function value( $val )
    {
        return array( "ezcTemplateLiteralAstNode", array( $val ) );
    }

    /**
     * Returns an Constant AST-node with the value $val.
     *
     * @param mixed $val
     * @return array(ezcTemplateAstNode)
     */
    protected static function constant( $val )
    {
        return array( "ezcTemplateConstantAstNode", array( $val ) );
    }

    /**
     * Concats (with ezcTemplateConcatOperatorAstNode) the two AST trees: $left and $right together. 
     *
     * @param ezcTemplateAstNode $left
     * @param ezcTemplateAstNode $right
     * @return array(ezcTemplateAstNode)
     */
    protected static function concat( $left, $right )
    {
        return array( "ezcTemplateConcatOperatorAstNode", array( $left, $right ) );
    }
    
    /**
     * Returns true if the given parameter $parameter should be substituted with an ezcTemplateAstNode.
     *
     * @param string $parameter
     * @return bool
     */
    protected static function isSubstitution( $parameter )
    {
        return ( strpos( $parameter, "%" ) !== false );
    }

    /**
     * Returns true if the given parameter is optional.
     *
     * @param string $parameter
     * @return bool
     */
    protected static function isOptional( $parameter )
    {
        return $parameter[0] == "["; 
    }

    /**
     * Returns true if the given parameter represents one or more parameters.
     *
     * @param string $parameter
     * @return bool
     */
    protected static function isMany( $parameter )
    {
        return ( $parameter[1] == "." && $parameter[2] == "." );
    }   

    /**
     * Returns the given amount of parameters plus the optional remaining parameters.
     *
     * @param array(string) $parameters
     * @return int
     */
    protected static function countParameters( $parameters )
    {
        $total = sizeof( $parameters );
        if ( isset( $parameterMap[ "__rest__" ] ) )
        {
            $total += sizeof( $parametersMap["__rest__"] ) - 1;
        }

        return $total;
    }

    /**
     * Returns true if the given AST node is a variable; thus a value can be assigned.
     *
     * @param ezcTemplateAstNode $ast
     * @return bool
     */
    protected static function isVariable( $ast )
    {
        if ( $ast instanceof ezcTemplateVariableAstNode ||
            $ast instanceof ezcTemplateReferenceOperatorAstNode  ||
            $ast instanceof ezcTemplateArrayFetchOperatorAstNode )
        {
            return true;
        }

        return false;
    }

    /**
     * Checks the type (currently only Variable) for a given AST node. 
     * If the type doesn't match, an exception is thrown.
     *
     * @param string $defined
     * @param ezcTemplateAstNode $givenAst
     * @param string $functionName  Only important for the error message.
     * @param int $parameterNumber  Only important for the error message.
     *
     * @throws ezcTemplateException if the function type is incorrect.
     *
     * @return void
     */
    protected static function checkType( $defined, $givenAst, $functionName, $parameterNumber )
    {
        $s = explode( ":", $defined );
        $type = sizeof( $s ) > 1 ? $s[1] : null;

        if ( $type !== null && $type == "Variable" )
        {
            if ( !self::isVariable( $givenAst ) )
            {
                throw new ezcTemplateException( "The function '$functionName' expects parameter ". ($parameterNumber + 1)." to be a variable." );
            }   
        }
    } 

    /**
     * Creates a new instantance of the given $className string with the parameters $functionParameters.
     *
     * If the class name is equal to "_array", it will just return the $functionParameters.
     *
     * @param string $className
     * @param ezcTemplateAstNode $functionParameters
     * @return ezcTemplateAstNode
     */
    protected function createObject( $className, $functionParameters )
    {
        if ( $className == "_array" )
        {
            return $functionParameters;
        }

        return call_user_func_array(
            array( new ReflectionClass( $className ), 'newInstance' ), $functionParameters );
    }
 

    /**
     * Translates a function definition to a real AST tree. The function definition contains
     * some virtual nodes that needs to be translated.
     *
     * @param string $functionName
     * @param ezcTemplateAstNode $struct
     * @param array(string=>ezcTemplateAstNode) $parameterMap
     * @param bool $usedRest Initially set to false and may be set to true.
     * @return ezcTemplateAstNode
     */
    protected function processAst( $functionName, $struct, $parameterMap, &$usedRest )
    {
        $pOut = array();
        foreach ( $struct[1] as $pIn )
        {
            if ( is_array( $pIn ) )
            {
                $pOut[] =  $this->processAst( $functionName, $pIn, $parameterMap, $usedRest );
            }
            else
            {
                if ( self::isSubstitution( $pIn ) )
                {
                    // Skip the optional parameter if the value is null.
                    if ( $parameterMap[$pIn] !== null )
                    {
                        $pOut[] = $parameterMap[ $pIn ];

                        if ( self::isMany( $pIn ) && isset( $parameterMap[ "__rest__" ]  ) )
                        {
                            $usedRest = true;
                            foreach ( $parameterMap[ "__rest__" ] as $restParameter )
                            {
                                $pOut[] = $restParameter;
                            }
                        }
                    }
                    elseif ( !self::isOptional( $pIn ) )
                    {
                        throw new ezcTemplateException( sprintf( ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_PARAMETER, $functionName, $pIn ) );
                    }
                }
                else 
                {
                    // No substitution needed. 
                    $pOut[] = $pIn;
                }
            }
        }
        return $this->createObject( $struct[0], $pOut );
    }

    /**
     * Translates a function definition to a real AST tree. The function definition contains
     * some virtual nodes that needs to be translated.
     *
     * @param string $functionName
     * @param array(mixed) $functionDefinition
     * @param array(ezcTemplateAstNode) $processedParameters
     * @return ezcTemplateAstNode
     */
    protected function createAstNodes( $functionName, $functionDefinition, $processedParameters )
    {
        $index = sizeof( $functionDefinition ) == 3  ? 1 : 0;

        $realParameters = sizeof( $processedParameters );
        $definedParameters = self::countParameters( $functionDefinition[ $index ] );

        // Map the parameters from the function definition to the given (real) parameters.
        $parameterMap = array();
        $i = 0;
        foreach ( $functionDefinition[ $index ] as $p )
        {
            if ( self::isOptional( $p ) && $realParameters < $definedParameters)
            {
                // We should skip this parameter.
                $parameterMap[$p] = null;
            }
            else
            {
                $parameterMap[$p] = null;

                if ( isset( $processedParameters[$i] ) )
                {
                    $parameterMap[$p] = $processedParameters[$i];
                    self::checkType( $p, $processedParameters[$i], $functionName, $i );
                }

                $i++;
            }
        }

        // Remaining parameters are stored in the "__rest__".
        $hasRest = false;
        while ( isset( $processedParameters[$i] ) )
        {
            $parameterMap[ "__rest__" ][] = $processedParameters[$i++];
            $hasRest = true;
        }

        $usedRest = false;
        $ast = $this->processAst( $functionName, $functionDefinition[ $index + 1 ], $parameterMap, $usedRest );

        if ( $hasRest && !$usedRest )
        {
            throw new ezcTemplateException( sprintf( ezcTemplateSourceToTstErrorMessages::MSG_TOO_MANY_PARAMETERS, $functionName ) );
        }


        if ( sizeof( $functionDefinition ) == 3 )
        {
            $ast->typeHint = $functionDefinition[0];
        }
        else
        {
            $ast->typeHint = ezcTemplateAstNode::TYPE_ARRAY | ezcTemplateAstNode::TYPE_VALUE; 
        }
 
        return new ezcTemplateParenthesisAstNode( $ast );
    }

    /**
     * Returns the class that may implement this function $functionName.
     *
     * @param string $functionName
     * @return string
     */
    public function getClass( $functionName )
    {
        foreach ( $this->functionToClass as $func => $class )
        {
            if ( preg_match( $func, $functionName ) )
            {
                return $class;
            }
        }

        return null;
    }

    /**
     * Returns a custom function definition if the given function name $name is a custom function, 
     * otherwise return false.
     *
     * @param string $name
     * @return ezcTemplateCustomFunctionDefinition
     */
    public function getCustomFunction( $name )
    {
        foreach ( $this->parser->template->usedConfiguration->customFunctions as $class )
        {
            $def = call_user_func( array( $class, "getCustomFunctionDefinition" ),  $name );

            if ( $def instanceof ezcTemplateCustomFunctionDefinition )
            {
                return $def;
            }
        }

        return false;
    }


    /**
     * Checks if the definition is correct. It returns nothing if it's correct, and
     * throws an exception otherwise.
     *
     * @param ezcTemplateCustomFunctionDefintion $definition
     * @param ReflectionParameters $reflectionParameters
     * @throws ezcTemplateException if the given definition is incorrect.
     * @return void
     */
    protected function checkDefinition( $definition, $reflectionParameters )
    {
        if ( !isset( $definition->parameters ) || $definition->parameters === false )
        {
            return;
        }

        $i = 0;
        $foundOptionalParameter = false;
        foreach ( $definition->parameters as $p )
        {
            if ( !isset($reflectionParameters[$i]) )
            {
                throw new ezcTemplateException( sprintf(ezcTemplateSourceToTstErrorMessages::MSG_INVALID_DEFINITION_PARAMETER_DOES_NOT_EXIST, $i + 1));
            }
            
            if ( $p[0] == "[" )
            {
                if ( !$reflectionParameters[$i]->isOptional() )
                {
                    throw new ezcTemplateException(sprintf(ezcTemplateSourceToTstErrorMessages::MSG_INVALID_DEFINITION_PARAMETER_OPTIONAL, $i + 1));
                }

                $foundOptionalParameter = true;
            }
            else
            {
                if ($foundOptionalParameter)
                {
                    throw new ezcTemplateException(ezcTemplateSourceToTstErrorMessages::MSG_INVALID_DEFINITION_EXPECT_OPTIONAL_PARAMETER);
                }
            }

            $i++;
        }
    }

    /**
     * Counts how many parameters are required.
     *
     * @param ezcTemplateCustomFunctionDefintion $definition
     * @param ReflectionParameters $reflectionParameters
     * @return int 
     */
    protected function countRequiredParameters( $definition, $reflectionParameters )
    {
        $requiredParameters = 0;

        foreach ( $reflectionParameters as $p )
        {
            if ( !$p->isOptional() )
            {
                $requiredParameters++;
            }
        }

        return $requiredParameters;
    }

    /**
     * Counts the total amount of parameters.
     *
     * @param ezcTemplateCustomFunctionDefintion $definition
     * @param ReflectionParameters $reflectionParameters
     * @return int 
     */
    protected function countTotalParameters($definition, $reflectionParameters)
    {
        return sizeof( $reflectionParameters );
    }

    /**
     * Checks the given parameters.
     *
     * @param ezcTemplateCustomFunctionDefintion $definition
     * @param ReflectionParameters $reflectionParameters
     * @param array(ezcTemplateAstNode) $parameters 
     * @param string $functionName
     * @return int 
     */
    protected function checkGivenParameters( $definition, $reflectionParameters, $parameters, $functionName )
    {
        $givenParameters = sizeof($parameters);

        if ( $givenParameters < $this->countRequiredParameters($definition, $reflectionParameters) )
        {
            throw new ezcTemplateException( sprintf( ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_PARAMETER, $functionName, $definition->parameters[$givenParameters]));
        }

        if ( !$definition->variableArgumentList && $givenParameters > $this->countTotalParameters($definition, $reflectionParameters) )
        {
            throw new ezcTemplateException( sprintf( ezcTemplateSourceToTstErrorMessages::MSG_TOO_MANY_PARAMETERS, $functionName ) );
        }
    }

    /**
     * Orders the parameters.
     *
     * @param ReflectionParameters $reflectionParameters
     * @param array(ezcTemplateAstNode) $parameters 
     * @param string $functionName
     * @return array(ezcTemplateAstNode)
     */
    protected function orderParameters($reflectionParameters, $parameters, $functionName)
    {
        // First assign the named parameters, thereafter fill in the rest.
        $newParameters = array();

        // Process the named parameters.
        $maxNr = 0;
       
        // Named parameters.
        foreach ($parameters as $name => $value)
        {
            if (!is_int($name))
            {
                $found = false;
                $nr = 0;

                foreach ( $reflectionParameters as $rp)
                {
                    if ( $rp->getName() === $name )
                    {
                        $found = true;
                        break;
                    }

                    $nr++;
                }

                if ( !$found)
                {
                    throw new ezcTemplateException( sprintf( ezcTemplateSourceToTstErrorMessages::MSG_NAMED_PARAMETER_NOT_FOUND, $name, $functionName  ) );
                }

                $newParameters[$nr] = $value; 
                $maxNr = max($nr, $maxNr);
            }
        }
        ksort($newParameters, SORT_NUMERIC);

        $i = 0;
        foreach ($parameters as $name => $value)
        {
            if (is_int($name))
            {
                while ( isset($newParameters[$i])) $i++;

                $newParameters[$i] = $value; 
            }

            $i++;
        }

        // Search for 'holes'.
        for($i = 0; $i < $maxNr; $i++)
        {
            if ( !isset( $newParameters[$i]) )
            {
                if ( !$reflectionParameters[$i]->isDefaultValueAvailable() )
                {
                    throw new ezcTemplateException( sprintf("Parameter %s is not given and has not a default value.", $i + 1) );
                }
                else
                {
                    $newParameters[$i] = new ezcTemplateLiteralAstNode( $reflectionParameters[$i]->getDefaultValue() );
                }
            }
        }
        ksort($newParameters, SORT_NUMERIC);

        return $newParameters;
    }

    /**
     * Returns the reflection parameters.
     *
     * @param ezcTemplateCustomFunctionDefintion $definition
     * @return ReflectionParameters
     */
    protected function getReflectionParameters( $definition )
    {
        if ( !isset($definition->class) || $definition->class  === false )
        {
            $rm = new ReflectionFunction( $definition->method );
        }
        else
        {
            $rc = new ReflectionClass( $definition->class );
            $rm = $rc->getMethod($definition->method);
        }
        
        return $rm->getParameters();
    }

    /**
     * Returns the corresponding AST tree to the function name $functionName and its parameters.
     *
     * The function name will, most likely, be translated from a Template function name to 
     * the PHP function. The parameters are ordered, if needed.
     *
     * @param string $functionName
     * @param array(ezcTemplateAstNode) $parameters 
     * @throws ezcTemplateException if the parameters are not valid (Too many, not enough parameters, etc).
     * @return ezcTemplateAstNode
     */
    public function getAstTree( $functionName, $parameters )
    {
        // Try the built-in template functions.
        $class = $this->getClass( $functionName );

        if ( $class !== null )
        {
            $f = call_user_func( array( $class, "getFunctionSubstitution"), $functionName, $parameters );
            if ( $f !== null ) return $this->createAstNodes( $functionName, $f, $parameters );
        }

        // Try the custom template functions.
        $def = $this->getCustomFunction( $functionName );
        
        if ( $def !== false )
        {
            $reflectionParameters = $this->getReflectionParameters( $def );

            $a = new ezcTemplateFunctionCallAstNode( ( $def->class ? ( $def->class . "::" ) : "" ) . $def->method);
            $a->checkAndSetTypeHint();

            if ( isset($def->sendTemplateObject) && $def->sendTemplateObject )
            {
                array_shift($reflectionParameters);
                $a->appendParameter( new ezcTemplateVariableAstNode("this->template") );
            }

            $this->checkDefinition($def, $reflectionParameters);
            $this->checkGivenParameters($def, $reflectionParameters, $parameters, $functionName);

            $parameters = $this->orderParameters( $reflectionParameters, $parameters, $functionName);



            foreach ( $parameters as $p)
            {
                $a->appendParameter( $p );
            }

            return $a;
        }

        throw new ezcTemplateException( sprintf( ezcTemplateSourceToTstErrorMessages::MSG_UNKNOWN_FUNCTION, $functionName ) );
    }

    /**
     * Subclasses need to implement this method.
     *
     * @param string $functionName
     * @param array(ezcTemplateAstNode) $parameters
     * @throws ezcTemplateInternalException if the subclass does not implement this method.
     * @return array(mixed)
     */
    public static function getFunctionSubstitution( $functionName, $parameters )
    {
        throw new ezcTemplateInternalException( "Subclasses need to implement the getFunctionSubstitution method." );
    }
}
?>
