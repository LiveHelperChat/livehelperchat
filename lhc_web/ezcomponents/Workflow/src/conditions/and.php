<?php
/**
 * File containing the ezcWorkflowConditionAnd class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Boolean AND.
 *
 * An object of the ezcWorkflowConditionAnd class represents a boolean AND expression. It
 * can hold an arbitrary number of ezcWorkflowCondition objects.
 *
 * <code>
 * <?php
 * $and = new ezcWorkflowConditionAnd( array( $condition , ... ) );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionAnd extends ezcWorkflowConditionBooleanSet
{
    /**
     * Textual representation of the concatenation.
     *
     * @var string
     */
    protected $concatenation = '&&';

    /**
     * Evaluates this condition with $value and returns true if the condition holds and false otherwise.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        foreach ( $this->conditions as $condition )
        {
            if ( !$condition->evaluate( $value ) )
            {
                return false;
            }
        }

        return true;
    }
}
?>
