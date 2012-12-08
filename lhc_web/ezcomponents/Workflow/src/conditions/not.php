<?php
/**
 * File containing the ezcWorkflowConditionNot class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Boolean NOT.
 *
 * An object of the ezcWorkflowConditionNot decorates an ezcWorkflowCondition object
 * and negates its expression.
 *
 * <code>
 * <?php
 * $notNondition = new ezcWorkflowConditionNot( $condition ) ;
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionNot implements ezcWorkflowCondition
{
    /**
     * Holds the expression to negate.
     *
     * @var ezcWorkflowCondition
     */
    protected $condition;

    /**
     * Constructs a new not condition on $condition.
     *
     * @param  ezcWorkflowCondition $condition
     */
    public function __construct( ezcWorkflowCondition $condition )
    {
        $this->condition = $condition;
    }

    /**
     * Evaluates this condition with the value $value and returns true if the condition holds.
     *
     * If the condition does not hold false is returned.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        return !$this->condition->evaluate( $value );
    }

    /**
     * Returns the condition that is negated.
     *
     * @return ezcWorkflowCondition
     * @ignore
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return '! ' . $this->condition;
    }
}
?>
