<?php
/**
 * File containing the ezcTemplateDecrementOperatorAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents the PHP decrement operator --
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDecrementOperatorAstNode extends ezcTemplateOperatorAstNode
{
    /**
     * Initialize operator code constructor with 1 parameters (unary).
     *
     * @param bool $preOperator Controls whether this operator is placed in front or after an operand.
     * @param ezcTemplateAstNode $parameter The code element to use as first parameter.
     */
    public function __construct( $preOperator, ezcTemplateAstNode $parameter = null )
    {
        parent::__construct( self::OPERATOR_TYPE_UNARY, $preOperator );
        if ( $parameter )
        {
            $this->appendParameter( $parameter );
        }
    }

    /**
     * Returns a text string representing the PHP operator.
     *
     * @return string
     */
    public function getOperatorPHPSymbol()
    {
        return '--';
    }
}
?>
