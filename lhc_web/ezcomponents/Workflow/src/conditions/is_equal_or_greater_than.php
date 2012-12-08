<?php
/**
 * File containing the ezcWorkflowConditionIsEqualOrGreaterThan class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Condition that evaluates to true if the provided value is greater than or equal to the reference value.
 *
 * Typically used together with ezcWorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new ezcWorkflowConditionVariable(
 *   'variable name',
 *   new ezcWorkflowConditionIsEqualOrGreatherThan( $comparisonValue )
 * );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionIsEqualOrGreaterThan extends ezcWorkflowConditionComparison
{
    /**
     * Textual representation of the comparison operator.
     *
     * @var mixed
     */
    protected $operator = '>=';

    /**
     * Evaluates this condition with $value and returns true if $value is greather than
     * or equal to the reference value or false if not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        return $value >= $this->value;
    }
}
?>
