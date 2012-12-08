<?php
/**
 * File containing the ezcWorkflowConditionIsAnything class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Condition that always evaluates to true.
 *
 * Typically used together with ezcWorkflowConditionVariable to use the
 * condition on a workflow variable.
 *
 * <code>
 * <?php
 * $condition = new ezcWorkflowConditionVariable(
 *   'variable name',
 *   new ezcWorkflowConditionIsAnything
 * );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowConditionIsAnything extends ezcWorkflowConditionType
{
    /**
     * Returns true.
     *
     * @param  mixed $value
     * @return boolean true
     * @ignore
     */
    public function evaluate( $value )
    {
        return true;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'is anything';
    }
}
?>
