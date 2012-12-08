<?php
/**
 * File containing the ezcWorkflowConditionOr class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Boolean OR.
 *
 * An object of the ezcWorkflowConditionOr class represents a boolean OR expression. It can
 * hold an arbitrary number of ezcWorkflowCondition objects.
 *
 * <code>
 * <?php
 * $or = new ezcWorkflowConditionOr( array ( $condition , ... ) );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionOr extends ezcWorkflowConditionBooleanSet
{
    /**
     * Textual representation of the concatenation.
     *
     * @var string
     */
    protected $concatenation = '||';

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
            if ( $condition->evaluate( $value ) )
            {
                return true;
            }
        }

        return false;
    }
}
?>
