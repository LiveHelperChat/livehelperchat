<?php
/**
 * File containing the ezcWorkflowNodeInput class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An object of the ezcWorkflowNodeInput class represents an input (from the application) node.
 *
 * When the node is reached, the workflow engine will suspend the workflow execution if the
 * specified input data is not available (first activation). While the workflow is suspended,
 * the application that embeds the workflow engine may supply the input data and resume the workflow
 * execution (second activation of the input node). Input data is stored in a workflow variable.
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * This example creates a simple workflow that expectes two input variables,
 * once which can be any value and another that can only be an integer between
 * one and ten.
 *
 * <code>
 * <?php
 * $workflow = new ezcWorkflow( 'Test' );
 *
 * $input = new ezcWorkflowNodeInput(
 *   'mixedVar' => new ezcWorkflowConditionIsAnything,
 *   'intVar'   => new ezcWorkflowConditionAnd(
 *     array(
 *       new ezcWorkflowConditionIsInteger,
 *       new ezcWorkflowConditionIsGreatherThan( 0 )
 *       new ezcWorkflowConditionIsLessThan( 11 )
 *     )
 *   )
 * );
 *
 * $input->addOutNode( $workflow->endNode );
 * $workflow->startNode->addOutNode( $input );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeInput extends ezcWorkflowNode
{
    /**
     * Constructs a new input node.
     *
     * An input node accepts an array of workflow variables to accept
     * and/or together with a condition on the variable if required.
     *
     * Each element in the configuration array must be either
     * <b>String:</b> The name of the workflow variable to require. No conditions.
     *
     * or
     * <ul>
     *   <li><i>Key:</i> The name of the workflow variable to require.</li>
     *   <li><i>Value:</i> An object of type ezcWorkflowCondition</li>
     *
     * </ul>
     *
     * @param mixed $configuration
     * @throws ezcBaseValueException
     */
    public function __construct( $configuration = '' )
    {
        if ( !is_array( $configuration ) )
        {
            throw new ezcBaseValueException(
              'configuration', $configuration, 'array'
            );
        }

        $tmp = array();

        foreach ( $configuration as $key => $value )
        {
            if ( is_int( $key ) )
            {
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException(
                      'workflow variable name', $value, 'string'
                    );
                }

                $variable  = $value;
                $condition = new ezcWorkflowConditionIsAnything;
            }
            else
            {
                if ( !is_object( $value ) || !$value instanceof ezcWorkflowCondition )
                {
                    throw new ezcBaseValueException(
                      'workflow variable condition', $value, 'ezcWorkflowCondition'
                    );
                }

                $variable  = $key;
                $condition = $value;
            }

            $tmp[$variable] = $condition;
        }

        parent::__construct( $tmp );
    }

    /**
     * Executes this node.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        $variables  = $execution->getVariables();
        $canExecute = true;
        $errors     = array();

        foreach ( $this->configuration as $variable => $condition )
        {
            if ( !isset( $variables[$variable] ) )
            {
                $execution->addWaitingFor( $this, $variable, $condition );

                $canExecute = false;
            }

            else if ( !$condition->evaluate( $variables[$variable] ) )
            {
                $errors[$variable] = (string)$condition;
            }
        }

        if ( !empty( $errors ) )
        {
            throw new ezcWorkflowInvalidInputException( $errors );
        }

        if ( $canExecute )
        {
            $this->activateNode( $execution, $this->outNodes[0] );

            return parent::execute( $execution );
        }
        else
        {
            return false;
        }
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
        $configuration = array();

        foreach ( $element->getElementsByTagName( 'variable' ) as $variable )
        {
            $configuration[$variable->getAttribute( 'name' )] = ezcWorkflowDefinitionStorageXml::xmlToCondition(
              ezcWorkflowUtil::getChildNode( $variable )
            );
        }

        return $configuration;
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML( DOMElement $element )
    {
        foreach ( $this->configuration as $variable => $condition )
        {
            $xmlVariable = $element->appendChild(
              $element->ownerDocument->createElement( 'variable' )
            );

            $xmlVariable->setAttribute( 'name', $variable );

            $xmlVariable->appendChild(
              ezcWorkflowDefinitionStorageXml::conditionToXml(
                $condition, $element->ownerDocument
              )
            );
        }
    }
}
?>
