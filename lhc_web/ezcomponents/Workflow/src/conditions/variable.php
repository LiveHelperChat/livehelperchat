<?php
/**
 * File containing the ezcWorkflowConditionVariable class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Wrapper that applies a condition to a workflow variable.
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionVariable implements ezcWorkflowCondition
{
    /**
     * The name of the variable the condition is applied to.
     *
     * @var string
     */
    protected $variableName;

    /**
     * The condition that is applied to the variable.
     *
     * @var ezcWorkflowCondition
     */
    protected $condition;

    /**
     * Constructor.
     *
     * @param  string $variableName
     * @param  ezcWorkflowCondition $condition
     */
    public function __construct( $variableName, ezcWorkflowCondition $condition )
    {
        $this->variableName = $variableName;
        $this->condition    = $condition;
    }

    /**
     * Evaluates this condition.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        if ( is_array( $value ) && isset( $value[$this->variableName] ) )
        {
            return $this->condition->evaluate( $value[$this->variableName] );
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return $this->variableName . ' ' . $this->condition;
    }

    /**
     * Returns the name of the variable the condition is evaluated for.
     *
     * @return string
     * @ignore
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Returns the condition.
     *
     * @return ezcWorkflowCondition
     * @ignore
     */
    public function getCondition()
    {
        return $this->condition;
    }
}
?>
