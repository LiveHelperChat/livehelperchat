<?php
/**
 * File containing the ezcWorkflowNodeVariableSub class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Subtracts a workflow variable from another variable or a constant value.
 *
 * An object of the ezcWorkflowNodeVariableSub class subtracts a given operand, either a
 * constant or the value of another workflow variable, from a specifled workflow variable.
 *
 * This example will subtract five from the contents of the workflow variable
 * 'wfVar' and put the result back in 'wfVar'.
 *
 * <code>
 * <?php
 * $sub = new ezcWorkflowNodeVariableSub(
 *   array( 'name' => 'wfVar', 'operand' => 5 )
 * );
 * ?>
 * </code>
 *
 * If operand is a string, the value of the workflow variable identified by that string is used.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeVariableSub extends ezcWorkflowNodeArithmeticBase
{
    /**
     * Perform variable modification.
     */
    protected function doExecute()
    {
        $this->variable -= $this->operand;
    }

    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @return array
     * @ignore
     */
    public static function configurationFromXML( DOMElement $element )
    {
        return array(
          'name'    => $element->getAttribute( 'variable' ),
          'operand' => $element->getAttribute( 'operand' )
        );
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML( DOMElement $element )
    {
        $element->setAttribute( 'variable', $this->configuration['name'] );
        $element->setAttribute( 'operand', $this->configuration['operand'] );
    }

    /**
     * Returns a textual representation of this node.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return $this->configuration['name'] . ' -= ' . $this->configuration['operand'];
    }
}
?>
