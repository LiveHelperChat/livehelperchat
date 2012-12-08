<?php
/**
 * File containing the ezcTemplateSwitchAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a switch control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateSwitchAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression which, when evaluated, will be used for matching
     * against the cases.
     * @var ezcTemplateAstNode
     */
    public $expression;

    /**
     * Array of case statements which are placed inside switch body.
     * @var array(ezcTemplateCaseAstNode)
     */
    public $cases;

    /**
     * Is set to true if the case list contains a default entry.
     * @var bool
     */
    public $hasDefaultCase;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateAstNode $expression
     * @param array(ezcTemplateAstNode) $cases  Should be either ezcTemplateCaseAstNode or ezcTemplateDefaultAstNode.
     */
    public function __construct( ezcTemplateAstNode $expression = null, Array $cases = null )
    {
        parent::__construct();
        $this->expression = $expression;
        $this->cases = array();
        $this->hasDefaultCase = false;

        if ( $cases !== null )
        {
            $hasDefault = false;
            foreach ( $cases as $case )
            {
                if ( !$case instanceof ezcTemplateCaseAstNode )
                {
                    throw new ezcTemplateInternalException( "Array in case list \$cases must consist of object which are instances of ezcTemplateCaseAstNode, not <" . get_class( $case ) . ">." );
                }
                if ( $case instanceof ezcTemplateDefaultAstNode )
                {
                    if ( $hasDefault )
                    {
                        throw new ezcTemplateInternalException( "The default case is already present as a case entry." );
                    }
                    $hasDefault = true;
                }
                $this->cases[] = $case;
            }
            $this->hasDefaultCase = $hasDefault;
        }
    }
}
?>
