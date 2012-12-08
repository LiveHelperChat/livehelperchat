<?php
/**
 * File containing the ezcTemplateModifyingOperatorTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Interface for modifying operator elements in parser trees.
 *
 * Modifying operators are those which directly alters their operand.
 * These operators are currently: ++$a, --$a, $a++ and $a--
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
abstract class ezcTemplateModifyingOperatorTstNode extends ezcTemplateOperatorTstNode
{
    /**
     * Initialize element with source and cursor positions.
     *
     * @param ezcTemplateSourceCode $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     * @param int $precedence
     * @param int $order
     * @param int $associativity
     * @param string $symbol
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end,
                                 $precedence, $order, $associativity, $symbol )
    {
        parent::__construct( $source, $start, $end,
                             $precedence, $order, $associativity, $symbol );
    }
}
?>
