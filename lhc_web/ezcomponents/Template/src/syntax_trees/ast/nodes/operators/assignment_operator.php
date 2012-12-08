<?php
/**
 * File containing the ezcTemplateAssignmentOperatorAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents the PHP assignment operator =
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateAssignmentOperatorAstNode extends ezcTemplateBinaryOperatorAstNode
{
    /**
     * Initialize operator code constructor with 2 parameters (binary).
     */
    /*public function __construct()
    {
        parent::__construct( self::OPERATOR_TYPE_BINARY );
    }
    */

    public function checkAndSetTypeHint()
    {
        $symbolTable = ezcTemplateSymbolTable::getInstance();

        $this->typeHint = $this->parameters[1]->typeHint;

        if ( $this->parameters[0] instanceof ezcTemplateVariableAstNode )
        {
             if ( $symbolTable->retrieve( $this->parameters[0]->name ) == ezcTemplateSymbolTable::IMPORT )
             {
                 // It can be anything.
                 $symbolTable->setTypeHint( $this->parameters[0]->name, self::TYPE_ARRAY | self::TYPE_VALUE );
             }
             else
             {
                $symbolTable->setTypeHint( $this->parameters[0]->name, $this->typeHint );
             }
        }
    }


    
    /**
     * Returns a text string representing the PHP operator.
     * @return string
     */
    public function getOperatorPHPSymbol()
    {
        return '=';
    }
}
?>
