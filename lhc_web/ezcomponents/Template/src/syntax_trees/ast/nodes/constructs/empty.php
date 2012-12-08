<?php
/**
 * File containing the ezcTemplateEmptyAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents an empty construct.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateEmptyAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression to output when evaluated.
     * @var ezcTemplateAstNode
     */
    public $expression;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateAstNode $expression
     */
    public function __construct( ezcTemplateAstNode $expression = null )
    {
        parent::__construct();
        $this->expression = $expression;
    }

    /**
     * Validates the expression against its constraints.
     *
     * @throws ezcTemplateInternalException if the constraints are not met.
     * @return void
     */
    public function validate()
    {
        if ( $this->expression === null )
        {
            throw new ezcTemplateInternalException( "Missing expression for class <" . get_class( $this ) . ">." );
        }
    }
}
?>
