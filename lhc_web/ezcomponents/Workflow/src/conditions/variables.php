<?php
/**
 * File containing the ezcWorkflowConditionVariables class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Wrapper that applies a condition to two workflow variables.
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionVariables implements ezcWorkflowCondition
{
    /**
     * The name of the first variable the condition is applied to.
     *
     * @var string
     */
    protected $variableNameA;

    /**
     * The name of the second variable the condition is applied to.
     *
     * @var string
     */
    protected $variableNameB;

    /**
     * The condition that is applied to the variable.
     *
     * @var ezcWorkflowCondition
     */
    protected $condition;

    /**
     * Constructor.
     *
     * @param  string $variableNameA
     * @param  string $variableNameB
     * @param  ezcWorkflowCondition $condition
     * @throws ezcBaseValueException
     */
    public function __construct( $variableNameA, $variableNameB, ezcWorkflowCondition $condition )
    {
        if ( !$condition instanceof ezcWorkflowConditionComparison )
        {
            throw new ezcBaseValueException(
              'condition',
              $condition,
              'ezcWorkflowConditionComparison'
            );
        }

        $this->variableNameA = $variableNameA;
        $this->variableNameB = $variableNameB;
        $this->condition     = $condition;
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
        if ( is_array( $value ) &&
             isset( $value[$this->variableNameA] ) &&
             isset( $value[$this->variableNameB] ) )
        {
            $this->condition->setValue( $value[$this->variableNameA] );
            return $this->condition->evaluate( $value[$this->variableNameB] );
        }
        else
        {
            return false;
        }
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

    /**
     * Returns the names of the variables the condition is evaluated for.
     *
     * @return array
     * @ignore
     */
    public function getVariableNames()
    {
        return array( $this->variableNameA, $this->variableNameB );
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return sprintf(
          '%s %s %s',

          $this->variableNameA,
          $this->condition->getOperator(),
          $this->variableNameB
        );
    }
}
?>
