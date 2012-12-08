<?php
/**
 * File containing the ezcTemplateBinaryOperatorAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This node represents a binary operator.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
abstract class ezcTemplateBinaryOperatorAstNode extends ezcTemplateOperatorAstNode
{
    /**
     * Constructs a new ezcTemplateBinaryOperatorAstNode
     *
     * @param ezcTemplateAstNode $parameter1
     * @param ezcTemplateAstNode $parameter2
     */
    public function __construct( $parameter1 = null, $parameter2 = null )
    {
        parent::__construct( self::OPERATOR_TYPE_BINARY );

        if ( $parameter1 !== null && $parameter2 !== null )
        {
            $this->appendParameter( $parameter1 );
            $this->appendParameter( $parameter2 );
        }
        elseif ( $parameter1 != null )
        {
            throw new ezcTemplateInternalException( "The binary operator expects zero or two parameters." );
        }
    }
}
?>
