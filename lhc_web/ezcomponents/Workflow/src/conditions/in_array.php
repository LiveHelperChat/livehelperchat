<?php
/**
 * File containing the ezcWorkflowConditionInArray class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Condition that checks if a value is in an array.
 *
 * Typically used together with ezcWorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new ezcWorkflowConditionVariable(
 *   'variable name',
 *   new ezcWorkflowConditionInArray( array( ... ) )
 * );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionInArray extends ezcWorkflowConditionComparison
{
    /**
     * Textual representation of the comparison operator.
     *
     * @var mixed
     */
    protected $operator = 'in array';

    /**
     * Evaluates this condition with $value and returns true if it is false or false if it is not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        return in_array( $value, $this->value );
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        $array = $this->value;
        $count = count( $array );

        for ( $i = 0; $i < $count; $i++ )
        {
            $array[$i] = var_export( $array[$i], true );
        }

        return $this->operator . '(' . join( ', ', $array ) . ')';
    }
}
?>
