<?php
/**
 * File containing the ezcWorkflowCondition interface.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for workflow conditions.
 *
 * @package Workflow
 * @version 1.4.1
 */
interface ezcWorkflowCondition
{
    /**
     * Evaluates this condition.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     */
    public function evaluate( $value );

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     */
    public function __toString();
}
?>
