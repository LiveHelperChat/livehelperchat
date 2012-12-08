<?php
/**
 * File containing the ezcWorkflowConditionIsNotEqual class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Condition that checks if a value is different from a reference value.
 *
 * Typically used together with ezcWorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new ezcWorkflowConditionVariable(
 *   'variable name',
 *   new ezcWorkflowConditionNotEqual( $comparisonValue )
 * );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionIsNotEqual extends ezcWorkflowConditionComparison
{
    /**
     * Textual representation of the comparison operator.
     *
     * @var mixed
     */
    protected $operator = '!=';

    /**
     * Evaluates this condition with $value and returns true if it is false or false if it is not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        return $value != $this->value;
    }
}
?>
