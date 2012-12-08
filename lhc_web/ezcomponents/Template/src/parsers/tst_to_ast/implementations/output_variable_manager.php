<?php
/**
 * File containing the ezcTemplateOutputVariableManager class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Keeps a stack of VariableAstNodes.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateOutputVariableManager
{
    private $outputVariables = array();
    private $stackSize = 0;

    public function __construct( $initialValue = null )
    {
        if ( is_object( $initialValue ) &&
             $initialValue instanceof ezcTemplateAstNode )
        {
            $this->initialValue = $initialValue;
        }
        else
        {
            $this->initialValue = new ezcTemplateLiteralAstNode( $initialValue );
        }
    }

    public function push( $name, $astNode = null )
    {
        if ( $astNode === null )
        {
            $astNode = new ezcTemplateVariableAstNode( $name );

            $symbolTable = ezcTemplateSymbolTable::getInstance();
            if ( $symbolTable->getTypeHint( $name ) == false )
            {
                $astNode->typeHint = ezcTemplateAstNode::TYPE_ARRAY | ezcTemplateAstNode::TYPE_VALUE;
            }
            else
            {
                // Will this work, values from this function is different than AST constants?
                $astNode->typeHint = $symbolTable->getTypeHint( $name );
            }
        }
        array_push( $this->outputVariables, array( 'name'    => $name,
                                                   'ast'     => $astNode,
                                                   'is_used' => false ) );
        ++$this->stackSize;
    }

    public function pop()
    {
        if ( count( $this->outputVariables ) == 0 )
        {
            throw new ezcTemplateInternalException( "Attempted pop() on an empty stack of variables" );
        }

        array_pop( $this->outputVariables );
        --$this->stackSize;
    }

    public function getName()
    {
        return $this->outputVariables[$this->stackSize - 1]['name'];
    }

    public function getAst()
    {
        $this->outputVariables[$this->stackSize - 1]['is_used'] = true;
        return clone $this->outputVariables[$this->stackSize - 1]['ast'];
    }

    public function isUsed()
    {
        return $this->outputVariables[$this->stackSize - 1]['is_used'];
    }

    public function getInitializationAst()
    {
        return new ezcTemplateGenericStatementAstNode(
            new ezcTemplateAssignmentOperatorAstNode( $this->getAst(),
                                                      clone $this->initialValue )
            );
    }

    public function getConcatAst( $concatValue )
    {
        return new ezcTemplateGenericStatementAstNode(
            new ezcTemplateConcatAssignmentOperatorAstNode( $this->getAst(),
                                                            $concatValue )
            );
    }


}


?>
