<?php
/**
 * File containing the ezcWorkflowConditionBooleanSet class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base class for boolean sets of conditions like AND, OR and XOR.
 *
 * @package Workflow
 * @version 1.4.1
 */
abstract class ezcWorkflowConditionBooleanSet implements ezcWorkflowCondition
{
    /**
     * Array of ezcWorkflowConditions
     *
     * @var array
     */
    protected $conditions;

    /**
     * String representation of the concatination.
     *
     * Used by the __toString() methods.
     *
     * @var string
     */
    protected $concatenation;

    /**
     * Constructs a new boolean set with the conditions $conditions.
     *
     * The format of $conditions must be array( ezcWorkflowCondition )
     *
     * @param array $conditions
     * @throws ezcWorkflowDefinitionStorageException
     */
    public function __construct( array $conditions )
    {
        foreach ( $conditions as $condition )
        {
            if ( !$condition instanceof ezcWorkflowCondition )
            {
                throw new ezcWorkflowDefinitionStorageException(
                  'Array does not contain (only) ezcWorkflowCondition objects.'
                );
            }

            $this->conditions[] = $condition;
        }
    }

    /**
     * Returns the conditions in this boolean set.
     *
     * @return ezcWorkflowCondition[]
     * @ignore
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        $string = '( ';

        foreach ( $this->conditions as $condition )
        {
            if ( $string != '( ' )
            {
                $string .= ' ' . $this->concatenation . ' ';
            }

            $string .= $condition;
        }

        return $string . ' )';
    }
}
?>
