<?php
/**
 * File containing the ezcTemplateAssignmentOptimizer
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * This class does some very basic assignment optimizations. 
 *
 * <code>
 * $myVar .=  "hello";
 * $myVar .=  " world";
 * </code>
 *
 * Becomes:
 * <code>
 * $myVar .=  "hello world";
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateAstToAstAssignmentOptimizer extends ezcTemplateAstWalker
{
    /**
     * Returns true if the given element consists of &lt;var> = &lt;static value>, otherwise false.
     *
     * @param ezcTemplateAstNode $element
     * @return bool
     */
    protected function isOptimizableConcat( $element )
    {
        if ( $element instanceof ezcTemplateGenericStatementAstNode )
        {
            if ( $element->expression instanceof ezcTemplateConcatAssignmentOperatorAstNode )
            {
                if ( $element->expression->parameters[0] instanceof ezcTemplateVariableAstNode ) 
                {
                    if ( $element->expression->parameters[1] instanceof ezcTemplateLiteralAstNode )
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Returns an optimized AST body from the original AST body $body. 
     *
     * @param ezcTemplateBodyAstNode $body
     * @return bool
     */
    public function visitBodyAstNode( ezcTemplateBodyAstNode $body )
    {
        array_unshift( $this->nodePath, $body );

        $statements = sizeof( $body->statements );
       
        $k = 0;
        $i = 0;
        $j = 1;
        while ( $i < $statements )
        {
            if ( $this->isOptimizableConcat( $body->statements[$i] ) ) 
            {
                while ( $i + $j < $statements && $this->isOptimizableConcat( $body->statements[$i + $j] ) && 
                    ( $body->statements[$i]->expression->parameters[0]->name === $body->statements[$i + $j]->expression->parameters[0]->name ) )
                {
                    $body->statements[$i]->expression->parameters[1]->value .= $body->statements[$i + $j]->expression->parameters[1]->value;
                    $j++;
                }
            }
 
            if ( $k != $i )
            {
                $body->statements[$k] = $body->statements[$i];
            }

            $i += $j;
            $j = 1;
            $k++;
        }

        for( $i = $k; $i < $statements; $i++ )
        {
            unset( $body->statements[$i] );
        }
        
        array_shift( $this->nodePath );

        return $body;
    }
}
?>
