<?php
/**
 * File containing the ezcTemplateGreaterEqualOperatorTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Operator for comparing two values using PHPs ==.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateGreaterEqualOperatorTstNode extends ezcTemplateOperatorTstNode
{
    /**
     * Initialise operator with source and cursor positions.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end,
                             6, 1, self::NON_ASSOCIATIVE,
                             '>=' );
    }
}
?>
