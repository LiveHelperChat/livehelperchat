<?php
/**
 * File containing the ezcTemplateEqualOperatorAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents the PHP equal operator ==
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateEqualOperatorAstNode extends ezcTemplateBinaryOperatorAstNode
{
    /**
     * Returns a text string representing the PHP operator.
     * @return string
     */
    public function getOperatorPHPSymbol()
    {
        return '==';
    }



    /**
     *  Check the typehints.
     *  
     *  It doesn't matter which types are used. And we return always a boolean; thus a value.
     */
    public function checkAndSetTypeHint()
    {
        $this->typeHint = self::TYPE_VALUE; 
    }


}
?>
