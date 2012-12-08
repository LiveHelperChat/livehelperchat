<?php
/**
 * File containing the ezcWorkflowConditionXor class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Boolean XOR.
 *
 * An object of the ezcWorkflowConditionXor class represents a boolean XOR expression. It
 * can hold an arbitrary number of ezcWorkflowCondition objects.
 *
 * <code>
 * <?php
 * $xor = new ezcWorkflowConditionXor( array ( $condition , ... ) );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionXor extends ezcWorkflowConditionBooleanSet
{
    /**
     * Textual representation of the concatenation.
     *
     * @var string
     */
    protected $concatenation = 'XOR';

    /**
     * Evaluates this condition with $value and returns true if the condition holds and false otherwise.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate( $value )
    {
        $result = false;

        foreach ( $this->conditions as $condition )
        {
            if ( $condition->evaluate( $value ) )
            {
                if ( $result )
                {
                    return false;
                }

                $result = true;
            }
        }

        return $result;
    }
}
?>
