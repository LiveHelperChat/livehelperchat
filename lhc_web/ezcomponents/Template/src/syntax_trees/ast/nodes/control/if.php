<?php
/**
 * File containing the ezcTemplateIfAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents an if control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateIfAstNode extends ezcTemplateStatementAstNode
{
    /**
     * Array of expressions which represents the conditions for the if, elseif
     * and else entries. The first entry is used for the if, the last for the
     * else and the one in between for elseif.
     * @var array(ezcTemplateConditionBodyAstNode)
     */
    public $conditions;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateConditionBodyAstNode $conditionBody
     */
    public function __construct( ezcTemplateConditionBodyAstNode $conditionBody = null )
    {
        parent::__construct();
        if ( $conditionBody !== null )
        {
            $this->conditions[] = $conditionBody;
        }
    }

    /**
     * Appends the condition object to the current list of conditions.
     *
     * @param ezcTemplateConditionBodyAstNode $condition Append an extra condition block.
     */
    public function appendCondition( ezcTemplateConditionBodyAstNode $condition )
    {
        $this->conditions[] = $condition;
    }

    /**
     * Returns the last condition object from the body.
     * If there are no conditions in the body it returns null.
     *
     * @return ezcTemplateConditionBodyAstNode
     */
    public function getLastCondition()
    {
        $count = count( $this->conditions );
        if ( $count === 0 )
        {
            return null;
        }
        return $this->conditions[$count - 1];
    }
}
?>
